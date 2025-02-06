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


//洽談中(未簽約已報價)
$Qry="SELECT a.*,b.engineering_name,c.builder_name,d.contractor_name,e.employee_name FROM CaseManagement a
LEFT JOIN construction b ON b.construction_id = a.construction_id
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
LEFT JOIN employee e ON e.employee_id = a.Handler
WHERE a.status1 = '未簽約' AND a.status2 = '已報價'
AND a.confirm3 = 'Y'
AND a.case_closed <> 'Y'
ORDER BY a.auto_seq";

$mDB->query($Qry);
$casereport_list = "";

$casereport_list.=<<<EOT
<div class="w-100 m-auto px-3" style="max-width:1200px;">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1100px;">
			<div class="text-start size16 weight">洽談中</div>
			<hr class="style_b">

EOT;

$total = $mDB->rowCount();
if ($total > 0) {

$casereport_list.=<<<EOT
			<table class="table table-bordered border-dark w-100">
				<thead class="table-light border-dark">
					<tr style="border-bottom: 1px solid #000;">
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">報價金額(未稅)</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">建物棟數</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
					</tr>
				</thead>
				<tbody class="table-group-divider">
EOT;

    while ($row=$mDB->fetchRow(2)) {
		$auto_seq = $row['auto_seq'];

		$region = $row['region'];
		$construction_id = $row['construction_id'];
		$engineering_name = $row['engineering_name'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		//報價金額(未稅)
		$quotation_amt = number_format($row['quotation_amt']);
		//建物棟數
		$buildings = $row['buildings'];
		//承攬模式
		$ContractingModel = $row['ContractingModel'];


$casereport_list.=<<<EOT
					<tr>
						<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$region</th>
						<th class="text-center" style="width:5%;padding: 10px;">$construction_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$builder_name<br>$builder_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$contractor_name<br>$contractor_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$quotation_amt</th>
						<th class="text-center" style="width:5%;padding: 10px;">$buildings</th>
						<th class="text-center" style="width:5%;padding: 10px;">$ContractingModel</th>
					</tr>
EOT;

	}

$casereport_list.=<<<EOT
				</tbody>
			</table>
EOT;

} else {

$casereport_list.=<<<EOT
			<div class="size12 text-center">無符合的資料</div>
EOT;
	
}

$casereport_list.=<<<EOT
		</div>
	</div>
</div>
EOT;


//已簽約執行中(未簽約已回簽+已簽約未進場)
$Qry="SELECT a.*,b.engineering_name,c.builder_name,d.contractor_name,e.employee_name FROM CaseManagement a
LEFT JOIN construction b ON b.construction_id = a.construction_id
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
LEFT JOIN employee e ON e.employee_id = a.Handler
WHERE ((a.status1 = '未簽約' AND a.status2 = '已回簽') or (a.status1 = '已簽約' AND a.status2 = '未進場'))
AND (a.confirm3 = 'Y' OR a.confirm4 = 'Y')
AND a.case_closed <> 'Y'
ORDER BY a.auto_seq";

$mDB->query($Qry);
$casereport_list2 = "";

$casereport_list2.=<<<EOT
<div class="w-100 m-auto px-3" style="max-width:1200px">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1100px;">
			<div class="text-start size16 weight">已簽約執行中</div>
			<hr class="style_b">

EOT;

$total = $mDB->rowCount();
if ($total > 0) {

$casereport_list2.=<<<EOT
			<table class="table table-bordered border-dark w-100">
				<thead class="table-light border-dark">
					<tr style="border-bottom: 1px solid #000;">
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">建物棟數</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
					</tr>
				</thead>
				<tbody class="table-group-divider">
EOT;

    while ($row=$mDB->fetchRow(2)) {
		$auto_seq = $row['auto_seq'];

		$region = $row['region'];
		$construction_id = $row['construction_id'];
		$engineering_name = $row['engineering_name'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		//合約總價(含稅)
		$total_contract_amt = number_format($row['total_contract_amt'],0);
		//建物棟數
		$buildings = $row['buildings'];
		//承攬模式
		$ContractingModel = $row['ContractingModel'];


$casereport_list2.=<<<EOT
					<tr>
						<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$region</th>
						<th class="text-center" style="width:5%;padding: 10px;">$engineering_name<br>$construction_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$builder_name<br>$builder_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$contractor_name<br>$contractor_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$total_contract_amt</th>
						<th class="text-center" style="width:5%;padding: 10px;">$buildings</th>
						<th class="text-center" style="width:5%;padding: 10px;">$ContractingModel</th>
					</tr>
EOT;

	}

$casereport_list2.=<<<EOT
				</tbody>
			</table>
EOT;

} else {

$casereport_list2.=<<<EOT
			<div class="size12 text-center">無符合的資料</div>
EOT;
	
}

$casereport_list2.=<<<EOT
		</div>
	</div>
</div>
EOT;



//進行中案件(已簽約進行中)
$Qry="SELECT a.*,b.engineering_name,c.builder_name,d.contractor_name,e.employee_name FROM CaseManagement a
LEFT JOIN construction b ON b.construction_id = a.construction_id
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
LEFT JOIN employee e ON e.employee_id = a.Handler
WHERE (a.status1 = '已簽約' AND a.status2 = '進行中')
AND (a.confirm3 = 'Y' OR a.confirm4 = 'Y')
AND a.case_closed <> 'Y'
ORDER BY a.auto_seq";

$mDB->query($Qry);
$casereport_list3 = "";

$casereport_list3.=<<<EOT
<div class="w-100 m-auto px-3" style="max-width:1200px">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1100px;">
			<div class="text-start size16 weight">進行中案件</div>
			<hr class="style_b">

EOT;

$total = $mDB->rowCount();
if ($total > 0) {

$casereport_list3.=<<<EOT
			<table class="table table-bordered border-dark w-100">
				<thead class="table-light border-dark">
					<tr style="border-bottom: 1px solid #000;">
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">建物棟數</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
					</tr>
				</thead>
				<tbody class="table-group-divider">
EOT;

    while ($row=$mDB->fetchRow(2)) {
		$auto_seq = $row['auto_seq'];

		$region = $row['region'];
		$construction_id = $row['construction_id'];
		$engineering_name = $row['engineering_name'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		//合約總價(含稅)
		$total_contract_amt = number_format($row['total_contract_amt'],0);
		//建物棟數
		$buildings = $row['buildings'];
		//承攬模式
		$ContractingModel = $row['ContractingModel'];


$casereport_list3.=<<<EOT
					<tr>
						<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$region</th>
						<th class="text-center" style="width:5%;padding: 10px;">$engineering_name<br>$construction_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$builder_name<br>$builder_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$contractor_name<br>$contractor_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$total_contract_amt</th>
						<th class="text-center" style="width:5%;padding: 10px;">$buildings</th>
						<th class="text-center" style="width:5%;padding: 10px;">$ContractingModel</th>
					</tr>
EOT;

	}

$casereport_list3.=<<<EOT
				</tbody>
			</table>
EOT;

} else {

$casereport_list3.=<<<EOT
			<div class="size12 text-center">無符合的資料</div>
EOT;
	
}

$casereport_list3.=<<<EOT
		</div>
	</div>
</div>
EOT;


//已完工案件
$Qry="SELECT a.*,b.engineering_name,c.builder_name,d.contractor_name,e.employee_name FROM CaseManagement a
LEFT JOIN construction b ON b.construction_id = a.construction_id
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
LEFT JOIN employee e ON e.employee_id = a.Handler
WHERE a.status1 = '已完工'
AND a.confirm7 = 'Y'
AND a.case_closed <> 'Y'
ORDER BY a.auto_seq";

$mDB->query($Qry);
$casereport_list4 = "";

$casereport_list4.=<<<EOT
<div class="w-100 m-auto px-3" style="max-width:1200px">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1100px;">
			<div class="text-start size16 weight">已完工案件</div>
			<hr class="style_b">

EOT;

$total = $mDB->rowCount();
if ($total > 0) {

$casereport_list4.=<<<EOT
			<table class="table table-bordered border-dark w-100">
				<thead class="table-light border-dark">
					<tr style="border-bottom: 1px solid #000;">
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">建物棟數</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
					</tr>
				</thead>
				<tbody class="table-group-divider">
EOT;

    while ($row=$mDB->fetchRow(2)) {
		$auto_seq = $row['auto_seq'];

		$region = $row['region'];
		$construction_id = $row['construction_id'];
		$engineering_name = $row['engineering_name'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		//合約總價(含稅)
		$total_contract_amt = number_format($row['total_contract_amt'],0);
		//建物棟數
		$buildings = $row['buildings'];
		//承攬模式
		$ContractingModel = $row['ContractingModel'];


$casereport_list4.=<<<EOT
					<tr>
						<th class="text-center text-nowrap" style="width:5%;padding: 10px;">$region</th>
						<th class="text-center" style="width:5%;padding: 10px;">$engineering_name<br>$construction_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$builder_name<br>$builder_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$contractor_name<br>$contractor_id</th>
						<th class="text-center" style="width:5%;padding: 10px;">$total_contract_amt</th>
						<th class="text-center" style="width:5%;padding: 10px;">$buildings</th>
						<th class="text-center" style="width:5%;padding: 10px;">$ContractingModel</th>
					</tr>
EOT;

	}

$casereport_list4.=<<<EOT
				</tbody>
			</table>
EOT;

} else {

$casereport_list4.=<<<EOT
			<div class="size12 text-center">無符合的資料</div>
EOT;
	
}

$casereport_list4.=<<<EOT
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
			<h3>禾登豐業績表</h3>
		</div>
		<div class="mycell text-end p-2 vbottom" style="width:20%;">
			<div class="btn-group print"  role="group" style="position:fixed;top: 10px; right:10px;z-index: 9999;">
				<button id="close" class="btn btn-info btn-lg" type="button" onclick="window.print();"><i class="bi bi-printer"></i>&nbsp;列印</button>
				<button id="close" class="btn btn-danger btn-lg" type="button" onclick="window.close();"><i class="bi bi-power"></i>&nbsp;關閉</button>
			</div>
		</div>
	</div>
</div>
<div style="margin-bottom: 30px;">$casereport_list</div>
<div style="margin-bottom: 30px;">$casereport_list2</div>
<div style="margin-bottom: 30px;">$casereport_list3</div>
<div style="margin-bottom: 30px;">$casereport_list4</div>
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