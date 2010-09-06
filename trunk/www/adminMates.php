<h2>Compañeros de piso</h2>
<?php 

if(isset($_POST['addMate']))
{
	addNewMate($_POST['name'], $_POST['comment']);
}

if(isset($_POST['suspend']))
{
	if($_POST['mate'] != null)
		suspendMate($_POST['mate'], $_POST['comment']);
}
if(isset($_POST['close']))
{
	if($_POST['mate'] != null)
		closeMate($_POST['mate'], $_POST['comment']);
}
if(isset($_POST['reactivate']))
{
	if($_POST['mate'] != null)
		reactivateMate($_POST['mate'], $_POST['comment']);
}
?>

<form name="adminMatesForm" action="index.php?section=adminMates" method="post">
<table border="1">
  <tr>
  	<th></th>
    <th>Nombre</th>
    <th>Estado</th>
    <th>Desde</th>
  </tr>
<?php
$allmates = getMates(null);

foreach($allmates as $mate)
{
	$lastStatus = getLastStatusLog($mate->name);
	echo'<tr>
	<td><input type="radio" name="mate" value="'.$mate->name.'"/></td>
    <td>'.$mate->name.'</td>
    <td>'.$lastStatus->status.'</td>
    <td>'.$lastStatus->startTimestamp.'</td>
  </tr>';
}
?>
</table>
<div>
<input type="submit" value="Ver historial" name="viewLog"/>
<p>Cambia estado:</p>
<input type="submit" value="Suspende" name="suspend"/>
<input type="submit" value="Cierra" name="close"/>
<input type="submit" value="Reactiva" name="reactivate"/>
<br>
Comentario: <input type="text" name="comment"/>
<br>
</div>


<br>
<div>
Añadir compañero:
<br>
Nombre: <input name="name" type="text"/><br>
Comentario: <input name="comment" type="text"/>
<input type="submit" name="addMate" value="addMate"/>
</div>
</form>

<?php 
if(isset($_POST['viewLog']))
{
?>
<table border="1">
  <tr>
    <th>Desde</th>
    <th>Hasta</th>
    <th>Estado</th>
    <th>Comentario</th>
  </tr>
<?php 
	$logs = getStatusLogs($_POST['mate']);
	foreach($logs as $log)
		echo '<tr><td>'.$log->startTimestamp.'</td><td>'.$log->endTimestamp.'</td><td>'.$log->status.'</td><td>'.$log->comment.'</td></tr>';
?>
</table>
<?php
}
?>