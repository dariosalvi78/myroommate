<h2>Nuevo ingreso</h2>

<?php

if(isset($_POST['newPayment']))
{
	//resulting from submit
	addPayment($_POST['fromWho'], $_POST['toWho'], $_POST['amount'], $_POST['comment']);
	echo'Nueva devolución insertada ';
	echo'pulsa <a href="index.php">aquí</a> para volver a la home';
}
else {
	?>

<table border="1">
	<tr>
		<td><a href="index.php?section=newPayment&mates=active">Compañeros
		activos</a></td>
		<td><a href="index.php?section=newPayment&mates=all">También
		compañeros antiguos</a></td>
	</tr>
</table>

<?php
	function whichMates()
	{
		if(isset($_GET['mates']))
		{
			if($_GET['mates']=="ACTIVE")
			return getMates("ACTIVE");
			else if($_GET['mates']=="all")
			return getMates(NULL);
			else //default
			return getMates("ACTIVE");
		}
		else //default
		return getMates("ACTIVE");
	}
?>

<form name="paymentForm" action="index.php?section=newPayment"
	method="post">
<p>Los decimales van con el punto!! (ej: 30.5 euros)</p>
<table id="paymentTable" border="1">
	<tr>
		<td>Desde</td>
		<td>A</td>
		<td>Importe</td>
		<td>Comentario</td>
	</tr>
	<tr>
		<td><select name="fromWho" id="fromWho">
		<?php
		$mates = whichMates();
			
		$firstmate = current($mates);

		foreach ($mates as $mate)
		{
			echo'<option value="'.$mate->name.'">'.$mate->name.'</option>';
		}
		?>

		</select></td>
		<td><select name="toWho" id="toWho">
		<?php
		$mates = whichMates();
		
		foreach ($mates as $mate)
		{
			if($mate->name != $firstmate->name)
			echo'<option value="'.$mate->name.'">'.$mate->name.'</option>';
		}
		?>
		</select></td>
		<td><input type="text" name="amount" /></td>
		<td><input type="text" name="comment" /></td>
	</tr>
</table>
<input type="submit" name="newPayment" value="Hecho" /></form>


<script type="text/javascript">
var fromWho = document.getElementById("fromWho");
fromWho.onchange=function(){
var chosenoption=this.options[this.selectedIndex];
 
 var toWho = document.getElementById("toWho");

 //remove existing
 for(var i=toWho.length-1 ; i >=0 ; i--)
 {
	toWho.remove(i);
 }
 //create a new select
<?php 
$mates = whichMates();
foreach ($mates as $mate)
{
	echo'if(chosenoption.value != "'.$mate->name.'")
	{ toWho.add(new Option("'.$mate->name.'", "'.$mate->name.'"), null); }
	';
}
?>

}
<?php 
}
?>
</script>
