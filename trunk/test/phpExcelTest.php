<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="test.xlsx"');
header('Cache-Control: max-age=0');



require_once('../libs/phpExcel/PHPExcel.php');

$objPHPExcel = new PHPExcel();

$expensesSheet = $objPHPExcel->getActiveSheet();
$expensesSheet->setTitle("Gastos");
$expensesSheet->setCellValue("A1", "Test");
$expensesSheet->setCellValue("A2", 32.44);

//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save("test.xlsx");

//$expensesSheet =  $objPHPExcel->createSheet(0);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output'); 

?>