<?php

require_once('conf.inc.php');


$now = (new \DateTime())->format('Y-m-d H:i:s');
$post = print_r($_POST, true);
$files = print_r($_FILES, true);
$log = implode("\n", [$now, 'POST:', $post, 'FILES:', $files]);
file_put_contents('api.log', $log, FILE_APPEND);

echo ( handler(getReqData()))? 'Command execute success' : 'Fail execute command';


function getReqData(){
	$data = array();
	foreach( $_POST as $key=>$value ){
		$data[$key] = $value;
	}
	
	foreach( $_GET as $key=>$value ){
		$data[$key] = $value;
	}
	
	return $data;
}

function handler($data){
	if ( !isset($data['optype'])) return false;
	$optype = $data['optype'];
	
	$result = false;
	switch ($optype){
		case 'save': $result = save($data); break;
		case 'delete': $result = delete($data); break;
		case 'load': $result = load($data);break;
	}
	return $result;
}


function save($data){	
	$str = trim($data['data']);
	if (recordExists($str)) return true;
	$f = fopen(FILE, 'a+');
	$result = false;
	if ($f){
		$tmp_arr = explode(';', $str);
		$email = $tmp_arr[2];
		if ( $image_name = uploadPhoto($email) ){
			$str .= ';' . $image_name;
		}
		$result = (fwrite($f, trim(convert(SOURCE, SOURCE, $str)) . "\n" ))? true : false;
		fclose($f);
	}
	
	return $result;
}

function uploadPhoto($email){
	$filename = md5($email);
	if ( isset( $_FILES['photo'] ) )
	{
		$file = $_FILES['photo'];
		$name = $file['name'];
		$savename = UPLOAD_DIR . '/' . $filename . '.' . array_pop(explode('.', $name));
		$tmpname = $file['tmp_name'];
		if ( move_uploaded_file( $tmpname, $savename) )
		{
			return $savename;
		}
		else
		{
			return false;
		}	
	}
}

function load($data){
	$str = trim($data['data']);
	$f = fopen(FILE, 'a+');
	$result = array();
	if ($f){
		while( $row = fgets($f)){
			if (strlen($row) == 0) continue;
			$result[] = $row;
		}
		fclose($f);
	}
	return implode($SEPARATOR,$result);
}

function delete($data){
	$str = trim($data['data']);
	$f = fopen(FILE, 'a+');
	$rows = array();
	if ($f){
		deletePhoto($str);
		while( $row = fgets($f)){
			if (isNeedRow($row,$str)) continue;
			$rows[] = $row;
		}
		fclose($f);
	}
	$f = fopen(FILE, 'w');
	if ($f){
		foreach ($rows as $row){
			fwrite($f, trim($row)."\n");
		}
		fclose($f);
		return true;
	}
	return false;
}


function deletePhoto($str){
	$tmp_arr = explode(';', $str);
	$name = (isset($tmp_arr[2]))? md5($tmp_arr[2]): '';
	$dir = UPLOAD_DIR;
	$files = scandir($dir);
	if (!is_array($files)) return false;
	foreach($files as $file){
		$filename = substr($file, 0, strrpos('.', $file));
		if ( $filename == $name )
			if ( file_exists($dir . '/' . $file) )
				unlink($dir . '/' . $file);
	}
}

function isNeedRow($row, $str){
	$arrRow = explode(';', $row);
	$arrStr = explode(';', $str);
	if ( isset($arrRow[2]) && isset($arrStr[2]) && $arrRow[2] == $arrStr[2] ) return true;
	return false;
}

function recordExists($str){
	$exists = false;
	$arrStr = explode(';', $str);
	$f = fopen(FILE, 'r');
	if ( $f ){
		while( $row = fgets($f)){
			if ( strlen($row) == 0 ) continue;
			$arrRow = explode(';', $row);
			if ( isset($arrRow[2]) && isset($arrStr[2]) && $arrRow[2] == $arrStr[2] ) $exists = true;	
		}
		fclose($f);
	}
	return $exists;
}

function convert( $source, $dest, $str ){
	return iconv( $source, $dest.'//IGNORE', $str );
}
