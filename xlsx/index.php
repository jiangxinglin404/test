<?php 
//引入Xlsx类
require './xlsxwriter/xlsxwriter.class.php';
$writer = new XLSXWriter();


$type =  isset($_GET['type'])?$_GET['type']:'simple';

switch ($type) {
	case 'aaa':
		echo 'aaa';
		break;
	case 'simple':
	default:
		

		break;
}







 ?>