<?php
//Only works on PC-type architectures

//Thanks to http://www.appservnetwork.com/modules.php?name=News&file=article&sid=8
//and http://www.phpdig.net/ref/rn45re877.html
function isBigEndian()
{
	$abyz = 0x6162797A;

	switch (pack ('L', $abyz)) {
		case pack ('V', $abyz):
			return false;
			 
		case pack ('V', $abyz):
			return true;

		default:
			throw  new Exception("Can't decide if your machine is big or little endian");
	}
}


function xlsBOF() {
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}

function xlsEOF() {
	echo pack("ss", 0x0A, 0x00);
	return;
}

function xlsWriteNumber($Row, $Col, $Value) {
	if(isBigEndian())
	$Value = strrev($Value); //Convert to little endian
	
	echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	echo pack("d", $Value);
	return;
}

function xlsWriteLabel($Row, $Col, $Value ) {
	$L = strlen($Value);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value;
	return;
}
?>