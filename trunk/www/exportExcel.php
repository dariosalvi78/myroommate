<?php

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header("Content-Disposition: attachment;filename=myroommate.xlsx");
header("Content-Transfer-Encoding: binary ");

require_once('../libs/phpExcel/PHPExcel.php');


require_once('../MNGR.php');

function setNiceText($sheet, $col, $row, $Text, $color)
{
	$objRichText = new PHPExcel_RichText();
	$objPayable = $objRichText->createTextRun($Text);
	$objPayable->getFont()->setBold(true);
	if($color == "red")
	$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
	if($color == "green")
	$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_GREEN ) );
	if($color == "darkred")
	$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKRED ) );
	if($color == "darkgreen")
	$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN) );

	$sheet->setCellValueByColumnAndRow($col, $row, $objRichText);
}


$objPHPExcel = new PHPExcel();

$expensesSheet = $objPHPExcel->getActiveSheet();
$expensesSheet->setTitle("Gastos");

$row = 1;

setNiceText($expensesSheet, 0, $row, "Gastos", "red");
$row++;

setNiceText($expensesSheet, 0, $row, "Fecha", NULL);
setNiceText($expensesSheet,1, $row,"Tipo", NULL);
setNiceText($expensesSheet,2, $row,"Cuantia", NULL);
setNiceText($expensesSheet,3, $row,"Pagado por", NULL);
setNiceText($expensesSheet,4, $row,"Comentario", NULL);
setNiceText($expensesSheet,5, $row,"Tipo recibo", NULL);
setNiceText($expensesSheet,6, $row,"Emision", NULL);
setNiceText($expensesSheet,7, $row,"Desde", NULL);
setNiceText($expensesSheet,8, $row,"Hasta", NULL);
$row++;

$expenses = getExpensesAndBills();
foreach($expenses as $expense)
{
	$expensesSheet->setCellValueByColumnAndRow(0, $row,$expense->timestamp);
	$expensesSheet->setCellValueByColumnAndRow(1, $row,$expense->type);
	$expensesSheet->setCellValueByColumnAndRow(2, $row,$expense->amount);
	$expensesSheet->setCellValueByColumnAndRow(3, $row,$expense->fromWho);
	$expensesSheet->setCellValueByColumnAndRow(4, $row,$expense->comment);


	if($expense->billType != null)
	{
		$expensesSheet->setCellValueByColumnAndRow(5, $row,$expense->billType);
		$expensesSheet->setCellValueByColumnAndRow(6, $row,$expense->emissionDate);
		$expensesSheet->setCellValueByColumnAndRow(7, $row,$expense->fromDate);
		$expensesSheet->setCellValueByColumnAndRow(8, $row,$expense->toDate);
	}
	$row++;
}

$row = 1;

$paymentsSheet = $objPHPExcel->createSheet(1);
$paymentsSheet->setTitle("Ingresos");

setNiceText($paymentsSheet,0, $row,"Ingresos", "green");

$row++;
setNiceText($paymentsSheet,0, $row,"Fecha", NULL);
setNiceText($paymentsSheet,1, $row,"Cuantia", NULL);
setNiceText($paymentsSheet,2, $row,"De", NULL);
setNiceText($paymentsSheet,3, $row,"A", NULL);
setNiceText($paymentsSheet,4, $row,"Comentario", NULL);
$row++;

$payments = getPayments(null);
foreach($payments as $payment)
{
	$paymentsSheet->setCellValueByColumnAndRow(0, $row,$payment->timestamp);
	$paymentsSheet->setCellValueByColumnAndRow(1, $row,$payment->amount);
	$paymentsSheet->setCellValueByColumnAndRow(2, $row,$payment->fromWho);
	$paymentsSheet->setCellValueByColumnAndRow(3, $row,$payment->toWho);
	$paymentsSheet->setCellValueByColumnAndRow(4, $row,$payment->comment);

	$row++;
}

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

$objPHPExcel->disconnectWorksheets();
unset($objPHPExcel);
?>