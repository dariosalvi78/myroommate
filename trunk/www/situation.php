<h2>Situación</h2>

<table border="1">
	<tr>
		<td><a href="index.php?mates=active">Compañeros activos</a></td>
		<td><a href="index.php?mates=all">También compañeros antiguos</a></td>
	</tr>
</table>

<table border="1">

<?php

if(isset($_GET['mates']))
{
	if($_GET['mates']=="ACTIVE")
	$mates = getMates("ACTIVE");
	else if($_GET['mates']=="all")
	$mates = getMates(NULL);
	else //default
	$mates = getMates("ACTIVE");
}
else //default
$mates = getMates("ACTIVE");

//First row
echo'<tr><td>De/A:</td>';
foreach($mates as $mate)
{
	echo'<td>'.$mate->name.'</td>';
}
echo'</tr>';

//Situation
foreach($mates as $from)
{
	echo'<td>'.$from->name.'</td>';
	foreach ($mates as $to)
	{
		if($from->name != $to->name)
		echo'<td>'.round( getDebt($from->name, $to->name) , 2).'</td>';
		else echo '<td></td>';
	}
	echo '</tr>';
}

?>

</table>
