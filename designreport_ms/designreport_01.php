<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = "0";
} else {
	$isMobile = "1";
}


$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
$m_pub_modal	= "/website/smarty/templates/".$site_db."/pub_modal";


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


//檢查是否為管理員及進階會員
$super_admin = "N";
$super_advanced = "N";
$mem_row = getkeyvalue2('memberinfo','member',"member_no = '$memberID'",'admin,advanced');
$super_admin = $mem_row['admin'];
$super_advanced = $mem_row['advanced'];



$mDB = "";
$mDB = new MywebDB();


$Qry="SELECT a.*,b.engineering_name,c.builder_name,d.contractor_name,e.employee_name FROM CaseManagement a
LEFT JOIN construction b ON b.construction_id = a.construction_id
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
LEFT JOIN employee e ON e.employee_id = a.Handler
WHERE a.confirm5 = 'Y'
AND a.case_closed <> 'Y'
ORDER BY a.auto_seq";

$mDB->query($Qry);
$casereport_list = "";

$casereport_list.=<<<EOT
<div class="w-100 m-auto px-3" style="min-height:300px;margin-bottom: 100px;">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1760px;">
EOT;


$total = $mDB->rowCount();
if ($total > 0) {

$casereport_list.=<<<EOT
	<table class="table table-bordered border-dark w-100">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">狀態(1)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">狀態(2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特編號</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">預計進場日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">鋁模材料<br>利舊/新購</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">標準層模板數量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">屋突層模板數量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">材料用量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特報價</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下單志特日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特合約<br>簽訂日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">材料進口日期</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
EOT;

    while ($row=$mDB->fetchRow(2)) {
		$auto_seq = $row['auto_seq'];

		$status1 = $row['status1'];
		$status2 = $row['status2'];
		$region = $row['region'];
		$geto_no = $row['geto_no'];
		$construction_id = $row['construction_id'];
		$engineering_name = $row['engineering_name'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		//預計進場日期
		$estimated_arrival_date = $row['estimated_arrival_date'];
		if ($estimated_arrival_date == "0000-00-00")
			$estimated_arrival_date = "";
		//鋁模材料利舊/新購
		$geto_formwork = $row['geto_formwork'];
		//標準層模板數量(M2)
		$std_layer_template_qty = $row['std_layer_template_qty'];
		//屋突層模板數量(M2)
		$roof_protrusion_template_qty = $row['roof_protrusion_template_qty'];
		//材料用量(M2)
		$material_usage = $std_layer_template_qty+$roof_protrusion_template_qty;
		//志特報價
		$geto_quotation = $row['geto_quotation'];
		//下單志特日期
		$geto_order_date = $row['geto_order_date'];
		if ($geto_order_date == "0000-00-00")
			$geto_order_date = "";
		//志特合約簽訂日期
		$geto_contract_date = $row['geto_contract_date'];
		if ($geto_contract_date == "0000-00-00")
			$geto_contract_date = "";
		//材料進口日期
		$material_import_date = $row['material_import_date'];
		if ($material_import_date == "0000-00-00")
			$material_import_date = "";


		//$makeby = $row['makeby'];
		//$content = nl2br_skip_html(htmlspecialchars_decode($row['content']));


$casereport_list.=<<<EOT
			<tr>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$status1</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$status2</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$region</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$geto_no</th>
				<th class="text-center" style="width:5%;padding: 10px;">$construction_id</th>
				<th class="text-center" style="width:5%;padding: 10px;">$builder_name<br>$builder_id</th>
				<th class="text-center" style="width:5%;padding: 10px;">$contractor_name<br>$contractor_id</th>
				<th class="text-center" style="width:5%;padding: 10px;">$estimated_arrival_date</th>
				<th class="text-center" style="width:5%;padding: 10px;">$geto_formwork</th>
				<th class="text-center" style="width:5%;padding: 10px;">$std_layer_template_qty</th>
				<th class="text-center" style="width:5%;padding: 10px;">$roof_protrusion_template_qty</th>
				<th class="text-center" style="width:5%;padding: 10px;">$material_usage</th>
				<th class="text-center" style="width:5%;padding: 10px;">$geto_quotation</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$geto_order_date</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$geto_contract_date</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$material_import_date</th>
			</tr>

EOT;

	}

$casereport_list.=<<<EOT
		</tbody>
	</table>
EOT;


} else {

$casereport_list.=<<<EOT
	<div class="size16 weight p-5 text-center">無任何符合查詢的資料</div>
EOT;

}

$casereport_list.=<<<EOT
		</div>
	</div>
</div>
EOT;


$mDB->remove();


$show_report=<<<EOT
<div class="mytable w-100 bg-white p-3 mt-3">
	<div class="myrow">
		<div class="mycell" style="width:20%;">
		</div>
		<div class="mycell weight pt-5 pb-4 text-center">
			<h3>對志特採購總表</h3>
		</div>
		<div class="mycell text-end p-2 vbottom" style="width:20%;">
			<div class="btn-group print"  role="group" style="position:fixed;top: 10px; right:10px;z-index: 9999;">
				<button id="close" class="btn btn-info btn-lg" type="button" onclick="window.print();"><i class="bi bi-printer"></i>&nbsp;列印</button>
				<button id="close" class="btn btn-danger btn-lg" type="button" onclick="window.close();"><i class="bi bi-power"></i>&nbsp;關閉</button>
			</div>
		</div>
	</div>
</div>
<div style="margin-bottom: 150px;">
	$casereport_list
</div>
EOT;

$show_center=<<<EOT
<style>

table.table-bordered {
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > th {
	border:1px solid black;
}
table.table-bordered > tbody > tr > td {
	border:1px solid black;
}

@media print {
	.print {
		display: none !important;
	}
}

</style>

$show_report

EOT;



?>