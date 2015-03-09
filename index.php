<?php
	require_once('conf.inc.php');
	
	$f = fopen(FILE, 'r');
	if ( $f ){
		$rec = array();
		while( $str = fgets( $f ) ){
			if ( strlen(trim($str)) == 0 ) continue;
			$rec[] = explode(';', trim($str));
		}
		fclose( $f );
	}
	
?>
	
<!DOCTYPE html>
<html>
	<head>
	</head>
	<style>
	table, td, tr, th
	{
		border: 1px solid #ccc;
		background-color: #eee;
	}
	
	td, th
	{
		padding: 10px;
		text-align: center;
	}
	
	th
	{
		background-color: #cc0;
	}
	
	img
	{
		width: 200px;
	}
	</style>
	
	<body>
		<table>
			<tr>
				<th>Имя</th>
				<th>Фамилия</th>
				<th>email</th>
				<th>Пол</th>
				<th>Дата рожд.</th>
				<th>Зарегистрирован</th>
				<th>Фото</th>
			</tr>
			<?php if (is_array($rec) && count($rec)): ?>
			<?php foreach( $rec as $row ): ?>
			<tr>
				<td><?=$row[0]?></td>
				<td><?=$row[1]?></td>
				<td><?=$row[2]?></td>
				<td><?=$row[3]?></td>
				<td><?=$row[4]?></td>
				<td><?=$row[5]?></td>
				<td><?php if(isset($row[6])):?><img src="<?=$row[6]?>"/> <?php endif;?></td>
			</tr>
			<?php endforeach; ?>
			<?php else: ?>
			<tr>
				<td colspan="7">Нет записей</td>
			</tr>
			<?php endif; ?>
		</table>
	</body>
</html>