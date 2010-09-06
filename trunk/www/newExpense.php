

<h2>Nuevo gasto</h2>


<?php

if(isset($_POST['newExpense']))
{
	//resulting from submit
	$matesnames= array();
	foreach($_POST['mates'] as $mate)
	array_push($matesnames, $mate);

	if(isset($_POST['isBill']) && $_POST['isBill']== true)
	{
		addBill($_POST['mate'] ,$_POST['amount'], $_POST['type'], $_POST['comment'], $matesnames, $_POST['billType'], $_POST['emissionDate'], $_POST['fromDate'], $_POST['toDate']);
	}
	else
	{
		addExpense($_POST['mate'] ,$_POST['amount'], $_POST['type'], $_POST['comment'], $matesnames);
	}
	echo'Nuevo gasto insertado ';
	echo'pulsa <a href="index.php">aquí</a> para volver a la home';
}
else {
?>

<form name="expenseForm" action="index.php?section=newExpense"
	method="post">
<table id="expenseTable" border="1">
	<tr>
		<td>Pagado por</td>
		<td>Tipo</td>
		<td>Cuantía</td>
		<td>Comentario</td>
		<td>Compartido con</td>
		<td>Factura</td>
	</tr>
	<tr>
		<td><select id="fromMate" name="mate">
		<?php
		$activemates = getMates("ACTIVE");
		$firstmate = current($activemates);

		foreach ($activemates as $mate)
		{
			echo'<option value="'.$mate->name.'">'.$mate->name.'</option>';
		}
		?>
		</select></td>
		<td><input type="text" name="type" /></td>
		<td><input type="text" name="amount" /></td>
		<td><input type="text" name="comment" /></td>
		<td id="toMates"><?php 
		$allmates = getMates(NULL);
		foreach ($allmates as $mate)
		{
			if($mate->name != $firstmate->name)
			echo'<input type="checkbox" name= "mates[]" value = "'.$mate->name.'">'.$mate->name.'';
		}
		?></td>
		<td><input type="checkbox" name="isBill" id="isBill" value="isBill" /></td>
	</tr>
</table>

<table id="billTable" border="1" style="display: none;">
	<tr>
		<td>Tipo</td>
		<td>Fecha emisión</td>
		<td>Desde</td>
		<td>Hasta</td>
	</tr>
	<tr>
		<td><input type="text" name="billType"/></td>
		<td><input type="text" class="w8em format-d-m-y divider-slash"
			id="emissionDate" name="emissionDate" value="09/09/2010"
			maxlength="10" /></td>
		<td><input type="text" class="w8em format-d-m-y divider-slash"
			id="fromDate" name="fromDate" value="09/09/2010" maxlength="10" /></td>
		<td><input type="text" class="w8em format-d-m-y divider-slash"
			id="toDate" name="toDate" value="09/09/2010" maxlength="10" /></td>
	</tr>
</table>
<input type="submit" name="newExpense" value="Hecho" /></form>
		<?php } ?>


<script type="text/javascript">

var selectmenu=document.getElementById("fromMate");
selectmenu.onchange=function(){
var chosenoption=this.options[this.selectedIndex];
 //New cell
 var cell = document.createElement("td");
 cell.setAttribute("id", "toMates");
<?php 
$allmates = getMates(NULL);
foreach ($allmates as $mate)
{
	echo'var chkbox = document.createElement(\'input\');   
		 chkbox.type = "checkbox";
		 chkbox.name= "mates[]";
		 chkbox.value = "'.$mate->name.'";
		 if(chosenoption.value != "'.$mate->name.'")
		 {	cell.appendChild(chkbox);
		 	var mateName = document.createTextNode("'.$mate->name.'");
		 	cell.appendChild(mateName);
		 }';
}
?> 
 var toMates = document.getElementById("toMates");
 toMates.parentNode.replaceChild(cell, toMates);
}

var bill = document.getElementById("bill");
var isBill = document.getElementById("isBill");
isBill.onclick =function(){
	
	if(isBill.checked)
	{
		document.getElementById("billTable").style.display = 'inherit';		
	}
	else
	{
		document.getElementById("billTable").style.display = 'none';
	}	
}

</script>
