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


$m_location = "/website/smarty/templates/" . $site_db . "/" . $templates;
$m_pub_modal = "/website/smarty/templates/" . $site_db . "/pub_modal";


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/" . $site_db . "_info_class.php");


//檢查是否為管理員及進階會員
$super_admin = "N";
$super_advanced = "N";
$mem_row = getkeyvalue2('memberinfo', 'member', "member_no = '$memberID'", 'admin,advanced');
$super_admin = $mem_row['admin'];
$super_advanced = $mem_row['advanced'];



$mDB = "";
$mDB = new MywebDB();


//載入案件編號 並只取年分
$Qry = "SELECT MIN(case_id) AS case_id
	FROM CaseManagement
	GROUP BY LEFT(case_id, 2)
	ORDER BY case_id;";

$mDB->query($Qry);


$year_dropdown = "";
$year_dropdown = "<select class=\"inline form-select\" name=\"case_year\" id=\"case_year\" style=\"width:auto;\">";
// $year_dropdown .= "<option>選取年份</option>";


if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$case_id = $row['case_id'];
		// 取出年份
		$case_year = substr($case_id, 0, 2);
		// 產生年份下拉選單
		$year_dropdown .= "<option value='$case_year'>20$case_year</option>";
	}
}
$year_dropdown .= "</select>";

$get_case_year = isset($_GET['case_year']) ? $_GET['case_year'] : ''; // 避免 Undefined index 錯誤


//載入狀態1
$Qry = "SELECT status1
FROM CaseManagement
WHERE status1 IS NOT NULL
GROUP BY status1
ORDER BY status1;";

$mDB->query($Qry);


$status1_dropdown = "";
$status1_dropdown = "<select class=\"inline form-select\" name=\"status1\" id=\"status1\" style=\"width:auto;\">";
$status1_dropdown .= "<option></option>";


if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$status1 = $row['status1'];
		// 產生年份下拉選單
		$status1_dropdown .= "<option value='$status1'>$status1</option>";
	}

}
$status1_dropdown .= "</select>";

$get_status1 = isset($_GET['status1']) ? $_GET['status1'] : ''; // 避免 Undefined index 錯誤

//載入狀態2
$Qry = "SELECT status2
FROM CaseManagement
WHERE status2 IS NOT NULL
GROUP BY status2
ORDER BY status2;";

$mDB->query($Qry);


$status2_dropdown = "";
$status2_dropdown = "<select class=\"inline form-select\" name=\"status2\" id=\"status2\" style=\"width:auto;\">";
// $status2_dropdown .= "<option></option>";


if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$status2 = $row['status2'];
		// 產生年份下拉選單
		$status2_dropdown .= "<option value='$status2'>$status2</option>";
	}

}
$status2_dropdown .= "</select>";
$get_status2 = isset($_GET['status2']) ? $_GET['status2'] : ''; // 避免 Undefined index 錯誤

if (!empty($get_case_year)) {
	$Qry = "SELECT 
        a.*,  
        b1.subcontractor_name AS subcontractor_name1,
        b2.subcontractor_name AS subcontractor_name2,
        b3.subcontractor_name AS subcontractor_name3,
        b4.subcontractor_name AS subcontractor_name4,
        c.builder_name AS builder_name,
        d.contractor_name AS contractor_name
    FROM CaseManagement a
    LEFT JOIN subcontractor b1 ON a.subcontractor_id1 = b1.subcontractor_id
    LEFT JOIN subcontractor b2 ON a.subcontractor_id2 = b2.subcontractor_id
    LEFT JOIN subcontractor b3 ON a.subcontractor_id3 = b3.subcontractor_id
    LEFT JOIN subcontractor b4 ON a.subcontractor_id4 = b4.subcontractor_id
    LEFT JOIN builder c ON a.builder_id = c.builder_id 
    LEFT JOIN contractor d ON a.contractor_id = d.contractor_id
    WHERE 1=1 
    AND a.case_id LIKE '$get_case_year_%'";

	if (!empty($get_status1)) {
		$Qry .= " AND a.status1 = '$get_status1'";
	}
	if (!empty($get_status2)) {
		$Qry .= " AND a.status2 = '$get_status2'";
	}

	$Qry .= " ORDER BY a.case_id";
} else {
	$Qry = "SELECT 
        a.*,  
        b1.subcontractor_name AS subcontractor_name1,
        b2.subcontractor_name AS subcontractor_name2,
        b3.subcontractor_name AS subcontractor_name3,
        b4.subcontractor_name AS subcontractor_name4,
        c.builder_name AS builder_name,
        d.contractor_name AS contractor_name
    FROM CaseManagement a
    LEFT JOIN subcontractor b1 ON a.subcontractor_id1 = b1.subcontractor_id
    LEFT JOIN subcontractor b2 ON a.subcontractor_id2 = b2.subcontractor_id
    LEFT JOIN subcontractor b3 ON a.subcontractor_id3 = b3.subcontractor_id
    LEFT JOIN subcontractor b4 ON a.subcontractor_id4 = b4.subcontractor_id
    LEFT JOIN builder c ON a.builder_id = c.builder_id 
    LEFT JOIN contractor d ON a.contractor_id = d.contractor_id
    ORDER BY a.case_id";
}

$mDB->query($Qry);
$casereport_list = "";

$casereport_list .= <<<EOT
<div class="w-100 m-auto px-3" style="min-height:300px;margin-bottom: 100px;">
	<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1760px;">
EOT;


$total = $mDB->rowCount();
if ($total > 0) {


	$casereport_list .= <<<EOT

	
</div>
	<table class="table table-bordered border-dark w-100">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">狀態(1)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">狀態(2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">區域</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">案件編號</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-建商名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包-營造廠名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">連絡人</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">案場位置</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">經辦人員</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">建物棟數</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">初評發送日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">預計回饋日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">初評狀態</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">備註</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">標準層模板數量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">屋突層模板數量(M2)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">材料金額</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">代工費用</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">報價金額(未稅)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">報價單是否送出</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">報價日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">預計進場日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">實際進場日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">完工日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">上包合約簽訂日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第一期預付款請款方式</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第一期預付預估日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第一期請款日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第二期預付款請款方式</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第二期預付預估日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第二期請款日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第三期預付款請款方式</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第三期預付預估日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">第三期請款日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特編號</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特報價</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下單志特時間</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">志特合約簽訂日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">鋁模材料</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">材料進口日期</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約號碼(ERP專案代號)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約承攬建物棟數</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下包代工</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施作樓層</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下包代工2</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施作樓層2</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)2</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下包放樣</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施作樓層3</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)3</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">下包放樣檢核</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施作樓層4</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約總價(含稅)4</th>
			


				
			</tr>
		</thead>
		<tbody class="table-group-divider">
EOT;

	while ($row = $mDB->fetchRow(2)) {
		$data = [];
		// $data['auto_seq'] = $row['auto_seq']; // 自動遞增編號
		$data['status1'] = $row['status1']; // 狀態1
		$data['status2'] = $row['status2']; // 狀態2
		$data['region'] = $row['region']; // 區域
		$data['case_id'] = $row['case_id']; // 案件編號
		$data['construction_id'] = $row['construction_id']; // 建築工程編號
		$data['builder_id'] = $row['builder_id']; // 建商ID
		$data['builder_name'] = $row['builder_name']; // 建商名稱
		$data['contractor_id'] = $row['contractor_id']; // 承包商ID
		$data['contractor_name'] = $row['contractor_name']; // 承包商名稱
		$data['contact'] = $row['contact']; // 聯絡人
		$data['site_location'] = $row['site_location']; // 工地位置
		$data['ContractingModel'] = $row['ContractingModel']; // 承包模式
		$data['Handler'] = $row['Handler']; // 負責人
		$data['buildings'] = $row['buildings']; // 建築物棟數
		$data['first_review_date'] = ($row['first_review_date'] == '0000-00-00') ? '' : $row['first_review_date']; // 初審日期
		$data['estimated_return_date'] = ($row['estimated_return_date'] == '0000-00-00') ? '' : $row['estimated_return_date']; // 預估回饋日期
		$data['preliminary_status'] = $row['preliminary_status']; // 初步狀態
		$data['remark'] = $row['remark']; // 備註
		$data['engineering_qty'] = $row['engineering_qty']; // 工程數量
		$data['std_layer_template_qty'] = $row['std_layer_template_qty']; // 標準模板數量
		$data['roof_protrusion_template_qty'] = $row['roof_protrusion_template_qty']; // 屋頂突出模板數量
		$data['material_amt'] = $row['material_amt']; // 材料金額
		$data['OEM_cost'] = $row['OEM_cost']; // 代工成本
		$data['quotation_amt'] = $row['quotation_amt']; // 報價金額
		$data['quotation_sended'] = $row['quotation_sended']; // 報價是否發送
		$data['quotation_date'] = ($row['quotation_date'] == '0000-00-00') ? '' : $row['quotation_date']; // 報價日期
		$data['estimated_arrival_date'] = ($row['estimated_arrival_date'] == '0000-00-00') ? '' : $row['estimated_arrival_date']; // 預計到貨日期
		$data['actual_entry_date'] = ($row['actual_entry_date'] == '0000-00-00') ? '' : $row['actual_entry_date']; // 實際進場日期
		$data['completion_date'] = ($row['completion_date'] == '0000-00-00') ? '' : $row['completion_date']; // 完工日期
		$data['contract_date'] = ($row['contract_date'] == '0000-00-00') ? '' : $row['contract_date']; // 合約日期
		$data['advance_payment1'] = $row['advance_payment1']; // 預付款1
		$data['estimated_payment_date1'] = ($row['estimated_payment_date1'] == '0000-00-00') ? '' : $row['estimated_payment_date1']; // 預估付款日期1
		$data['request_date1'] = ($row['request_date1'] == '0000-00-00') ? '' : $row['request_date1']; // 請款日期1
		$data['advance_payment2'] = $row['advance_payment2']; // 預付款2
		$data['estimated_payment_date2'] = ($row['estimated_payment_date2'] == '0000-00-00') ? '' : $row['estimated_payment_date2']; // 預估付款日期2
		$data['request_date2'] = ($row['request_date2'] == '0000-00-00') ? '' : $row['request_date2']; // 請款日期2
		$data['advance_payment3'] = $row['advance_payment3']; // 預付款3
		$data['estimated_payment_date3'] = ($row['estimated_payment_date3'] == '0000-00-00') ? '' : $row['estimated_payment_date3']; // 預估付款日期3
		$data['request_date3'] = ($row['request_date3'] == '0000-00-00') ? '' : $row['request_date3']; // 請款日期3
		$data['geto_no'] = $row['geto_no']; // GETO編號
		$data['geto_quotation'] = $row['geto_quotation']; // GETO報價
		$data['geto_order_date'] = ($row['geto_order_date'] == '0000-00-00') ? '' : $row['geto_order_date']; // GETO訂單日期
		$data['geto_contract_date'] = ($row['geto_contract_date'] == '0000-00-00') ? '' : $row['geto_contract_date']; // GETO合約日期
		$data['geto_formwork'] = $row['geto_formwork']; // GETO鋁板材料
		$data['material_import_date'] = ($row['material_import_date'] == '0000-00-00') ? '' : $row['material_import_date']; // 材料進口日期
		$data['ERP_no'] = $row['ERP_no']; // ERP編號
		$data['buildings_contract'] = $row['buildings_contract']; // 合約承攬建物棟數
		$data['total_contract_amt'] = $row['total_contract_amt']; // 總合約金額
		$data['subcontractor_id1'] = $row['subcontractor_id1']; // 分包商ID1
		$data['subcontractor_name1'] = $row['subcontractor_name1']; // 分包商ID1
		$data['construction_floor1'] = $row['construction_floor1']; // 建築樓層1
		$data['total_contract_amt1'] = $row['total_contract_amt1']; // 分包合約金額1
		$data['subcontractor_id2'] = $row['subcontractor_id2']; // 分包商ID2
		$data['subcontractor_name2'] = $row['subcontractor_name2']; // 分包商ID2
		$data['construction_floor2'] = $row['construction_floor2']; // 建築樓層2
		$data['total_contract_amt2'] = $row['total_contract_amt2']; // 分包合約金額2
		$data['subcontractor_id3'] = $row['subcontractor_id3']; // 分包商ID3
		$data['subcontractor_name3'] = $row['subcontractor_name3']; // 分包商ID3
		$data['construction_floor3'] = $row['construction_floor3']; // 建築樓層3
		$data['total_contract_amt3'] = $row['total_contract_amt3']; // 分包合約金額3
		$data['subcontractor_id4'] = $row['subcontractor_id4']; // 分包商ID4
		$data['subcontractor_name4'] = $row['subcontractor_name4']; // 分包商ID4
		$data['construction_floor4'] = $row['construction_floor4']; // 建築樓層4
		$data['total_contract_amt4'] = $row['total_contract_amt4']; // 分包合約金額4



		$casereport_list .= <<<EOT
			<tr>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['status1']}</th> 
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['status2']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['region']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['case_id']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['construction_id']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['builder_name']}<br>{$data['builder_id']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['contractor_name']}<br>{$data['contractor_id']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['contact']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['site_location']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['ContractingModel']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['Handler']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['buildings']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['first_review_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['estimated_return_date']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['preliminary_status']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['remark']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['engineering_qty']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['std_layer_template_qty']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['roof_protrusion_template_qty']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['material_amt']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['OEM_cost']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['quotation_amt']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['quotation_sended']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['quotation_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['estimated_arrival_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['actual_entry_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['completion_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['contract_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['advance_payment1']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['estimated_payment_date1']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['request_date1']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['advance_payment2']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['estimated_payment_date2']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['request_date2']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['advance_payment3']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['estimated_payment_date3']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['request_date3']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['geto_no']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['geto_quotation']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['geto_order_date']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['geto_contract_date']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['geto_formwork']}</th>
				<th class="text-center text-nowrap" style="width:5%;padding: 10px;">{$data['material_import_date']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['ERP_no']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['buildings_contract']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['total_contract_amt']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['subcontractor_name1']}<br>{$data['subcontractor_id1']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['construction_floor1']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['total_contract_amt1']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['subcontractor_name2']}<br>{$data['subcontractor_id2']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['construction_floor2']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['total_contract_amt2']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['subcontractor_name3']}<br>{$data['subcontractor_id3']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['construction_floor3']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['total_contract_amt3']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['subcontractor_name4']}<br>{$data['subcontractor_id4']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['construction_floor4']}</th>
				<th class="text-center" style="width:5%;padding: 10px;">{$data['total_contract_amt4']}</th>
			
			</tr>

EOT;

	}

	$casereport_list .= <<<EOT
		</tbody>
	</table>
EOT;


} else {

	$casereport_list .= <<<EOT
	<div class="size16 weight p-5 text-center">無任何符合查詢的資料</div>
EOT;

}

$casereport_list .= <<<EOT
		</div>
	</div>
</div>
EOT;


$mDB->remove();


$show_report = <<<EOT

<div class="mytable w-100 bg-white p-3 mt-3">
	<div class="myrow">
		<div class="mycell" style="width:20%;">
		</div>
		<div class="mycell weight pt-5 pb-4 text-center">
			<h3>年度工程紀錄表</h3>
					<div class="w-100 p-3 m-auto text-center">

		<div class="inline size12 weight text-nowrap vtop mb-2 me-2">年份 : $year_dropdown</div>
		<div class="inline size12 weight text-nowrap vtop mb-2 me-2">狀態(1) : $status1_dropdown</div>
		<div class="inline size12 weight text-nowrap vtop mb-2 me-2">狀態(2) : $status2_dropdown</div>

		<button type="button" class="btn btn-success" onclick="chdatetime();"><i class="fas fa-check"></i>&nbsp;查詢</button>
		</div>
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

$show_center = <<<EOT
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

$show_center = <<<EOT

$show_report

<script>

document.addEventListener("DOMContentLoaded", function () {
    const yearDropdown = document.getElementById("case_year");
    const status1Dropdown = document.getElementById("status1");
    const status2Dropdown = document.getElementById("status2");

    if (yearDropdown) {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedYear = urlParams.get("case_year");
        if (selectedYear) {
            yearDropdown.value = selectedYear;
        }
    }

    if (status1Dropdown) {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedStatus1 = urlParams.get("status1");
        if (selectedStatus1) {
            status1Dropdown.value = selectedStatus1;
        }
    }

    if (status2Dropdown) {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedStatus2 = urlParams.get("status2");
        if (selectedStatus2) {
            status2Dropdown.value = selectedStatus2;
        }
    }
});

function chdatetime() {
    var case_year = $('#case_year').val();
    var status1 = $('#status1').val();
    var status2 = $('#status2').val();

    window.location = '?ch=designreport_04&fm=designreport&case_year=' + case_year + '&status1=' + status1 + '&status2=' + status2;
    return false;
}


</script>
EOT;

?>