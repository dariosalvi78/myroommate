<?php

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header("Content-Disposition: attachment;filename=test.xls ");
header("Content-Transfer-Encoding: binary ");

require_once('../libs/excel.php');
require_once('../MNGR.php');


$row = 0;
xlsBOF();
xlsWriteLabel($row,0,"-----Gastos");
$row++;
xlsWriteLabel($row,0,"Fecha");
xlsWriteLabel($row,1,"Tipo");
xlsWriteLabel($row,2,"Cuantia");
xlsWriteLabel($row,3,"Pagado por");
xlsWriteLabel($row,4,"Comentario");
xlsWriteLabel($row,5,"Tipo recibo");
xlsWriteLabel($row,6,"Emision");
xlsWriteLabel($row,7,"Desde");
xlsWriteLabel($row,8,"Hasta");
$row++;

$expenses = getExpensesAndBills();
foreach($expenses as $expense)
{
	xlsWriteLabel($row,0,$expense->timestamp);
	xlsWriteLabel($row,1,$expense->type);
	xlsWriteNumber($row,2,$expense->amount);
	xlsWriteLabel($row,3,$expense->fromWho);
	xlsWriteLabel($row,4,$expense->comment);
	
	if($expense->billType != null)
	{
		xlsWriteLabel($row,5,$expense->billType);
		xlsWriteLabel($row,6,$expense->emissionDate);
		xlsWriteLabel($row,7,$expense->fromDate);
		xlsWriteLabel($row,8,$expense->toDate);
	}
	$row++;
}


$row++;
$row++;
xlsWriteLabel($row,0,"-----Ingresos");
$row++;
xlsWriteLabel($row,0,"Fecha");
xlsWriteLabel($row,1,"Cuantia");
xlsWriteLabel($row,2,"De");
xlsWriteLabel($row,3,"A");
xlsWriteLabel($row,4,"Comentario");
$row++;

$payments = getPayments(null);
foreach($payments as $payment)
{
	xlsWriteLabel($row,0,$payment->timestamp);
	xlsWritenUMBER($row,1,$payment->amount);
	xlsWriteLabel($row,2,$payment->fromWho);
	xlsWriteLabel($row,3,$payment->toWho);
	xlsWriteLabel($row,4,$payment->comment);
	$row++;
}

xlsEOF();
exit();

?>