<?php
require_once 'libs/rb.php';

$dsn = 'mysql:dbname=myroommate;host=127.0.0.1';
$user = 'myroommate';
$password = 'roommatepwd';

try {
	R::setup($dsn,$user,$password);
} catch (Exception $e) {
	echo 'Connection failed: ' . $e->getMessage();
}

//init DB
R::exec("CREATE TABLE IF NOT EXISTS mate (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(50) );", null);
R::exec("CREATE TABLE IF NOT EXISTS statuslog (id INT PRIMARY KEY AUTO_INCREMENT, mate_id INT, status VARCHAR(50), startTimestamp DATETIME, endTimestamp DATETIME, comment VARCHAR(255), ".
		"FOREIGN KEY (mate_id) ".
        "REFERENCES mate(id) ".
		"ON UPDATE CASCADE ON DELETE CASCADE );", null);
R::exec("CREATE TABLE IF NOT EXISTS expense (id INT PRIMARY KEY AUTO_INCREMENT, amount FLOAT, fromWho VARCHAR(50), type VARCHAR(50), comment VARCHAR(255), timestamp DATETIME );", null);
R::exec("CREATE TABLE IF NOT EXISTS expense_mate (id INT PRIMARY KEY AUTO_INCREMENT, mate_id INT, expense_id INT, ".
		"FOREIGN KEY (mate_id) ".
        "REFERENCES mate(id) ".
		"ON UPDATE CASCADE ON DELETE CASCADE, ".
		"FOREIGN KEY (expense_id) ".
        "REFERENCES expense(id) ".
		"ON UPDATE CASCADE ON DELETE CASCADE".
		");", null);
R::exec("CREATE TABLE IF NOT EXISTS payment (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(50), amount FLOAT, fromWho VARCHAR(50), toWho VARCHAR(50), timestamp DATETIME );");
R::exec("CREATE TABLE IF NOT EXISTS bill (id INT PRIMARY KEY AUTO_INCREMENT, billType VARCHAR(50), emissionDate DATE, fromDate DATE, toDate DATE, expense_id INT, ".
		"FOREIGN KEY (expense_id) ".
        "REFERENCES expense(id) ".
		"ON UPDATE CASCADE ON DELETE CASCADE".
		");");



$GLOBALS['debug'] = false;

if($GLOBALS['debug'])
{
	R::$adapter->getDatabase()->setDebugMode(true);
	ini_set('display_errors', 'On');
}

function addNewMate($name, $comment)
{
	$newMate = R::dispense("mate");
	$newMate->name = $name;
	R::store($newMate);

	$newStatusLog = R::dispense("statuslog");
	$newStatusLog->status = "ACTIVE";
	$newStatusLog->startTimestamp = date("Y-m-d H:i:s");
	$newStatusLog->comment = $comment;
	$newStatusLog->endTimestamp = null;

	R::link( $newStatusLog, $newMate );
	R::store($newStatusLog);

}

function getMate($name)
{
	if($name == NULL)
	throw new Exception("Can't look up mate with null name", $this);

	$mates = Finder::where("mate", " name = ? ",array($name));
	$mate = current($mates);
	if(!isset($mate))
	throw new Exception("No mate found under name ".$name, $this);

	return $mate;
}

function getMates($status)
{
	if($status == NULL){
		$keys = R::$adapter->getCol("SELECT mate.id FROM mate;", NULL);
	}
	else {
		$keys = R::$adapter->getCol("SELECT mate.id FROM mate JOIN statuslog ON mate.id = statuslog.mate_id WHERE statuslog.endTimestamp IS NULL AND statuslog.status = ? ", array($status) );
	}
	$mates = R::batch("mate", $keys);

	return  $mates;
}

function getStatusLogs($matename)
{
	$keys = R::$adapter->getCol("SELECT statuslog.id FROM statuslog JOIN mate ON mate.id = statuslog.mate_id WHERE mate.name = ? ORDER BY statuslog.startTimestamp DESC ", array($matename) );
	$logs = R::batch("statuslog", $keys);

	return $logs;
}

function getLastStatusLog($matename)
{
	$mate = getMate($matename);
	$lastStatusLogs = Finder::where("statuslog", " mate_id = ".$mate->id." AND endTimestamp IS NULL ORDER BY startTimestamp DESC");

	if($GLOBALS['debug'])
	echo 'Last status log: '.print_r(current($lastStatusLogs));

	if(count($lastStatusLogs)==0)
	throw new Exception("A mate with no closed states has been found");
	else if(count($lastStatusLogs)>1)
	throw new Exception("A mate with more than an open state has been found");
	else
	return current($lastStatusLogs);
}

function suspendMate($name, $comment)
{
	$mate = getMate($name);
	$lastStatusLog = getLastStatusLog($name);

	if($lastStatusLog->status =='ACTIVE')
	{
		//Suspend him
		$lastStatusLog->endTimestamp = date("Y-m-d H:i:s");
		R::Store($lastStatusLog);

		$newStatusLog = R::dispense("statuslog");
		$newStatusLog->status = "SUSPENDED";
		$newStatusLog->startTimestamp = date("Y-m-d H:i:s");
		$newStatusLog->comment = $comment;
		R::link( $newStatusLog, $mate );
		R::store($newStatusLog);
	}
}

function closeMate($matename, $comment)
{
	$mate = getMate($matename);
	$lastStatusLog = getLastStatusLog($matename);

	if(($lastStatusLog->status =='ACTIVE') or ($lastStatusLog->status =='SUSPENDED'))
	{
		//Suspend him
		$lastStatusLog->endTimestamp = date("Y-m-d H:i:s");
		R::Store($lastStatusLog);

		$newStatusLog = R::dispense("statuslog");
		$newStatusLog->status = "CLOSED";
		$newStatusLog->startTimestamp = date("Y-m-d H:i:s");
		$newStatusLog->comment = $comment;
		R::link( $newStatusLog, $mate );
		R::store($newStatusLog);
	}
}

function reactivateMate($matename, $comment)
{
	$mate = getMate($matename);
	$lastStatusLog = getLastStatusLog($matename);

	if($lastStatusLog->status =='SUSPENDED')
	{
		//re activate him
		$lastStatusLog->endTimestamp = date("Y-m-d H:i:s");
		R::Store($lastStatusLog);

		$newStatusLog = R::dispense("statuslog");
		$newStatusLog->status = "ACTIVE";
		$newStatusLog->startTimestamp = date("Y-m-d H:i:s");
		$newStatusLog->comment = $comment;
		R::link( $newStatusLog, $mate );
		R::store($newStatusLog);
	}
}


function addExpense($matename ,$amount, $type, $comment, $matesnames)
{
	$expense = R::dispense("expense");
	$expense->amount = $amount;
	$expense->type = $type;
	$expense->comment = $comment;
	$expense->fromWho = $matename;
	$expense->timestamp = date("Y-m-d H:i:s");
	foreach ($matesnames as $matesname)
	{
		$mate = getMate($matesname);
		R::associate( $expense, $mate );
	}

	return R::store($expense);
}

function getExpenses($matename)
{
	if($matename == NULL)
	{
		$query = "SELECT id FROM expense ORDER BY timestamp DESC;";
		$keys = R::$adapter->getCol($query);
		$expenses = R::batch("expense", $keys);
	}
	else
	{
		$expenses = Finder::where("expense", " fromWho = '".$matename."' ORDER BY timestamp DESC");
	}
	return $expenses;
}

function getExpenseMates($expenseId)
{
	$expense = R::load("expense", $expenseId);
	$mates = R::related($expense, "mate");
	$who = getMate($expense->fromWho);
	array_push($mates, $expense->fromWho);

	return $mates;
}

function parseDate($date)
{
	list($day,$month,$year) = explode("/", $date);
	if(!checkdate($month, $day, $year))
	throw new Exception("The date ".$date." is not valid", null);

	$retval = $year.'-'.$month.'-'.$day;
	return $retval;
}

function addBill($matename ,$amount, $type, $comment, $matesnames, $billType, $emissionDate, $fromDate, $toDate)
{
	$id = addExpense($matename ,$amount, $type, $comment, $matesnames);
	$expense = R::load("expense", $id);
	$bill = R::dispense("bill");
	$bill->billType = $billType;
	$bill->fromDate = $fromDate;
	$bill->toDate = $toDate;
	$bill->emissionDate = $emissionDate;
	R::link( $bill, $expense );
	R::store($bill);
}

function getBills($matename)
{
	if($matename == NULL)
	{
		$query = "SELECT * FROM expense JOIN bill ON expense.id = bill.expense_id ORDER BY timestamp DESC;";
		$expensesTable = R::getAll($query);
	}
	else
	{
		$query = "SELECT * FROM expense JOIN bill ON expense.id = bill.expense_id WHERE expense.fromWho = '".$matename."' ORDER BY timestamp DESC;";
		$expensesTable = R::getAll($query);
	}

	$bills = array();
	foreach($expensesTable as $expenseRow)
	{
		$bill->fromWho = $expenseRow['fromWho'];
		$bill->amount = $expenseRow['amount'];
		$bill->type = $expenseRow['type'];
		$bill->comment = $expenseRow['comment'];
		$bill->timestamp = $expenseRow['timestamp'];
		$bill->billType = $expenseRow['billType'];
		$bill->fromDate = $expenseRow['fromDate'];
		$bill->toDate = $expenseRow['toDate'];
		$bill->emissionDate = $expenseRow['emissionDate'];
		array_push($bills, $bill);
	}

	return $bills;
}

function getExpensesAndBills()
{
	$query = "SELECT * FROM expense LEFT JOIN bill ON expense.id = bill.expense_id ORDER BY timestamp DESC;";
	$expensesTable = R::getAll($query);

	$bills = array();
	foreach($expensesTable as $expenseRow)
	{
		$bill->fromWho = $expenseRow['fromWho'];
		$bill->amount = $expenseRow['amount'];
		$bill->type = $expenseRow['type'];
		$bill->comment = $expenseRow['comment'];
		$bill->timestamp = $expenseRow['timestamp'];
		$bill->billType = $expenseRow['billType'];
		$bill->fromDate = $expenseRow['fromDate'];
		$bill->toDate = $expenseRow['toDate'];
		$bill->emissionDate = $expenseRow['emissionDate'];

		array_push($bills, $bill);
		unset($bill);
	}


	return $bills;
}


function addPayment($fromWho, $toWho, $amount, $comment)
{
	$payment = R::dispense("payment");
	$payment->fromWho = $fromWho;
	$payment->toWho = $toWho;
	$payment->amount = $amount;
	$payment->comment = $comment;
	$payment->timestamp = date("Y-m-d H:i:s");
	R::store($payment);
}

function getPayments($fromMateName)
{
	if($fromMateName != NULL)
	$payments = Finder::where("payment", " fromWho = '".$fromMateName."' ORDER BY timestamp DESC");
	else
	{
		$keys = R::$adapter->getCol("SELECT * FROM payment ORDER BY timestamp DESC " );
		$payments = R::batch("payment", $keys);
	}
	return $payments;
}

function getPaid($fromMate, $toMate)
{
	$query = "SELECT SUM(amount) FROM payment WHERE fromWho = '".$fromMate."' AND toWho = '".$toMate."' GROUP BY fromWho;";
	$payms = R::$adapter->getCol($query);

	$payms = current($payms);
	return  $payms;
}

function getOwnsExpended($fromMate, $toMate)
{
	//expenses that must be paid and amount
	$query = "SELECT expense.id, expense.amount FROM expense JOIN expense_mate ON expense_mate.expense_id = expense.id JOIN mate ON expense_mate.mate_id = mate.id WHERE mate.name = '".$fromMate."' AND expense.fromWho = '".$toMate."';";
	$expenses = R::$adapter->get($query);

	$debt = 0;
	foreach($expenses as $expense)
	{
		//how many people are involved per expense
		$query = "SELECT COUNT(*) FROM expense_mate WHERE expense_id = '".$expense['id']."' GROUP BY expense_id;";
		$people = R::$adapter->get($query);
		$people = current($people);
		$peopleNum = $people['COUNT(*)']+1; //it's the ones is shared amongst plus the payer

		//so mate owns to fromWho the amount / people
		$debt += $expense['amount'] /$peopleNum ;
	}

	return $debt;
}

function getDebt($from, $to)
{
	if($GLOBALS['debug'])
	{
		echo $from." owns ".getOwnsExpended($from, $to)." to ".$to." in expenses<br>";
		echo $from." owns ".getpaid($to, $from)." to ".$to." in payments<br>";
		echo $from." expects ".getOwnsExpended($to, $from)." from ".$to." in expenses<br>";
		echo $from." expects ".getpaid($from, $to)." from ".$to." in payments<br>";
	}
	//owns what to has shared
	$debt = getOwnsExpended($from, $to);
	//minus what from has shared
	$debt -= getOwnsExpended($to, $from);
	//minus what has been paid
	$debt -= getpaid($from, $to);
	//plus what has been given
	$debt += getpaid($to, $from);

	return $debt;
}


?>