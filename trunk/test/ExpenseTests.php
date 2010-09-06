<?php

require_once('../libs/simpletest/autorun.php');
require_once '../MNGR.php';

class MateTestst extends UnitTestCase {

	function setUp() {

	}
	function tearDown() {
		R::exec("DELETE FROM mate;", null);
		R::exec("DELETE FROM statuslog;", null);
		R::exec("DELETE FROM expense;", null);
		R::exec("DELETE FROM expense_mate;", null);
		R::exec("DELETE FROM payment;", null);
		R::exec("DELETE FROM bill;", null);
	}


	function test_addexpense()
	{
		addNewMate("TESTMATE1","new mate");
		addNewMate("TESTMATE2","new mate");

		addExpense("TESTMATE1", 100, "Compras", "detalles", array("TESTMATE2"));
		
		$expenses = getExpenses("TESTMATE1");
		$this->assertEqual(1, count($expenses), "Only one expense");
		$this->assertEqual(100, current($expenses)->amount, "amount is 100");
	}
	
	function test_mates_expense()
	{
		addNewMate("TESTMATE1","new mate");
		addNewMate("TESTMATE2","new mate");
		addNewMate("TESTMATE3","new mate");
		
		addExpense("TESTMATE1", 100, "Compras", "detalles", array("TESTMATE2"));
		$expense = current(getExpenses("TESTMATE1"));
		
		$mates =getExpenseMates($expense->id);
		
		$this->assertEqual(2, count($mates), "Two mates");
	}
	
	
	function test_bill()
	{
		addNewMate("TESTMATE1","new mate");
		addNewMate("TESTMATE2","new mate");
		
		addBill("TESTMATE1", 100, "recibo", "detalles", array("TESTMATE2"), "Luz","2010-09-03", "2010-09-01", "2010-09-30");
		
		$bills = getBills(null);
		$bill = $bills[0];
		
		$this->assertEqual("TESTMATE1", $bill->fromWho, "From mate name");
		$this->assertEqual(100, $bill->amount, "Amount is 100");
		$this->assertEqual("recibo", $bill->type, "Type is recibo");
		$this->assertEqual("detalles", $bill->comment, "Comment is detalles");
		$this->assertEqual("Luz", $bill->billType, "Bill type is Luz");
		$this->assertEqual("2010-09-03", $bill->emissionDate, "Bill emission date");
		$this->assertEqual("2010-09-01", $bill->fromDate, "Bill fromDate");
		$this->assertEqual("2010-09-30", $bill->toDate, "Bill to Date");
	}
}