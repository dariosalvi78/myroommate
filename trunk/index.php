<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<script type="text/javascript" src="libs/date-picker/js/datepicker.js"></script>
<link href="libs/date-picker/css/datepicker.css" rel="stylesheet"
	type="text/css" />
<link rel="stylesheet" type="text/css" href="www/style.css">
</head>
<body>
<h1>Mi compañero de piso</h1>
<table border="1">
	<tr>
		<td><a href="index.php?section=situation">Situación</a></td>
		<td><a href="index.php?section=newExpense">Añadir gasto</a></td>
		<td><a href="index.php?section=newPayment">Añadir devolución</a></td>
		<td><a href="index.php?section=see">Ver gastos y pagos</a></td>
		<td><a href="index.php?section=adminMates">Administración compañeros</a></td>
	</tr>
</table>
<?php
require_once('MNGR.php');

if(isset($_GET["section"]))
{
	if($_GET["section"]=="situation")
	include './www/situation.php';
	else if ($_GET["section"]=="newExpense")
	include './www/newExpense.php';
	else if ($_GET["section"]=="newPayment")
	include './www/newPayment.php';
	else if ($_GET["section"]=="see")
	include './www/viewExpensesAndPayments.php';
	else if ($_GET["section"]=="adminMates")
	include './www/adminMates.php';
	else //default
	include './www/situation.php';
}
else //default
include './www/situation.php';
?>
</body>
</html>

