<h2>Nueva devolución</h2>

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

<form name="paymentForm" action="index.php?section=newPayment"
	method="post">
	<p>Los decimales van con el punto!! (ej: 30.5 euros)</p>
<table id="paymentTable" border="1">
	<tr>
		<td>Desde</td>
		<td>A</td>
		<td>Cuantía</td>
		<td>Comentario</td>
	</tr>
	<tr>
		<td><select name="fromWho" id="fromWho">
		<?php
		$allmates = getMates(NULL);
		$firstmate = current($activemates);

		foreach ($allmates as $mate)
		{
			echo'<option value="'.$mate->name.'">'.$mate->name.'</option>';
		}
		?>
		</select></td>
		<td><select name="toWho" id="toWho">
		<?php
		$allmates = getMates(NULL);
		foreach ($allmates as $mate)
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
$allmates = getMates(NULL);
foreach ($allmates as $mate)
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