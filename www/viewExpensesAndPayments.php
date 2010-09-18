<h2>Ver gastos y pagos</h2>

<p>Gastos:</p>
<table border="1">
<tr>
	<td>Fecha</td>
	<td>Tipo</td>
	<td>Cuantía</td>
	<td>Pagado por</td>
	<td>Comentario</td>
	<td>Tipo recibo</td>
	<td>Emisión</td>
	<td>Desde</td>
	<td>Hasta</td>
</tr>
<?php

$expenses = getExpensesAndBills();
foreach($expenses as $expense)
{
	echo'<tr>';
	echo'<td>'.$expense->timestamp.'</td>';
	echo'<td>'.$expense->type.'</td>';
	echo'<td>'.$expense->amount.'</td>';
	echo'<td>'.$expense->fromWho.'</td>';
	echo'<td>'.$expense->comment.'</td>';
	if($expense->billType != null)
	{
		echo'<td>'.$expense->billType.'</td>';
		echo'<td>'.$expense->emissionDate.'</td>';
		echo'<td>'.$expense->fromDate.'</td>';
		echo'<td>'.$expense->toDate.'</td>';
	}
	else 
	echo '<td colspan="4"></td>';
	echo'</tr>';
	
}

?>
</table>

<p>Devoluciones:</p>
<table border="1">
<tr>
	<td>Fecha</td>
	<td>Cuantía</td>
	<td>Desde</td>
	<td>A</td>
	<td>Comentario</td>
</tr>
<?php

$payments = getPayments(null);
foreach($payments as $payment)
{
	echo'<tr>';
	echo'<td>'.$payment->timestamp.'</td>';
	echo'<td>'.$payment->amount.'</td>';
	echo'<td>'.$payment->fromWho.'</td>';
	echo'<td>'.$payment->toWho.'</td>';
	echo'<td>'.$payment->comment.'</td>';
	echo'</tr>';
}
?>
</table>
