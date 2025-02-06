<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
$m_pub_modal	= "/website/smarty/templates/".$site_db."/pub_modal";

$sid = "";
if (isset($_GET['sid']))
	$sid = $_GET['sid'];


//程式分類
$ch = empty($_GET['ch']) ? 'default' : $_GET['ch'];
switch($ch) {
	case 'designreport_01':
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func02/designreport_ms/designreport_01.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		break;
	case 'designreport_02':
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func02/designreport_ms/designreport_02.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		break;
	case 'designreport_03':
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func02/designreport_ms/designreport_03.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		break;
	default:
		if (empty($sid))
			$sid = "mbpjitem";
		$modal = $m_location."/sub_modal/project/func02/designreport_ms/designreport.php";
		include $modal;
		break;
};

?>