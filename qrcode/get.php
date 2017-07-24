<?php

include "qrlib.php";
$errorCorrectionLevel = 'L';
if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H'))) {
	$errorCorrectionLevel = $_REQUEST['level'];
}
$matrixPointSize = 10;
if (isset($_REQUEST['size'])) {
	$matrixPointSize = min(max((int) $_REQUEST['size'], 1), 10);
}
$content = 'Default Content';
if (isset($_REQUEST['data'])) {
	//it's very important!
	if (trim($_REQUEST['data']) != '') {
		$content = trim($_REQUEST['data']);
	}
} 
echo QRcode::png($content, false, $errorCorrectionLevel, $matrixPointSize, 2);