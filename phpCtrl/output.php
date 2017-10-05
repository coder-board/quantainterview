<?php
	/* 数据查询，得到最终筛选的数组 $overview */
	require 'header.php';
	$sql = "SELECT * FROM org_depart WHERE org_id={$_SESSION['org']}";
	$result = $db->query($sql);
	$tmp_depart = [];
	$depart = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $tmp_depart[$i] = $row;
        $i++;
    }

    /* $depart数组存储部门id及名称 */
    $depart[0] = '无';
	foreach ($tmp_depart as $key => $value) {
		$depart[$value['depart_id']] = $value['depart_name'];
	}
	function toResult($number) {
		switch ($number) {
			case 0: return '无';
			case 1: return '不通过';
			case 2: return '待定';
			case 3: return '通过';
			default: return '无';
		}
	}

    // Get personal infomation
    $sql = "SELECT * FROM interviewee_info AS main LEFT JOIN first_itv AS fir ON main.itv_code=fir.itv_code LEFT JOIN second_itv AS sec ON main.itv_code=sec.itv_code WHERE year='{$_GET['year']}' AND org_id={$_SESSION['org']}";

    if (isset($_GET['department']) && !empty($_GET['department'])) {
        $dept = $_GET['department'];
        $sql .= " AND first_will = '$dept'";
    }

    if (isset($_GET['turn']) && isset($_GET['turn_result']) && 
        !empty($_GET['turn']) && !empty($_GET['turn_result'])) {
        $keyword = $_GET['turn'] == 1 ? 'fir_result' : 'sec_result';
        $turn_result = $_GET['turn_result'];
        $sql .= " AND $keyword = '$turn_result'";
        $_GET['turn'] == 2 && $sql .= ' ORDER BY sec_grade DESC';
    }

    $result = $db->query($sql);
    $overview = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $overview[$i] = $row;
        $i++;
    }

	/* 开始导出Excel表 */
	require '../Lib/phpExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel
		->getProperties()
			->setCreator("面试系统")
			->setLastModifiedBy("admin")
			->setTitle("output")
			->setSubject("output")
			->setDescription("Interview document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("interview search file");

	$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "面试系统")
            ->setCellValue('A2', "NO")
            ->setCellValue('B2', "部门")
            ->setCellValue('C2', "编号")
            ->setCellValue('D2', "姓名")
            ->setCellValue('E2', "第二意向")
            ->setCellValue('F2', "班级")
            ->setCellValue('G2', "手机")
            ->setCellValue('H2', "一面结果")
            ->setCellValue('I2', "二面结果");

    foreach($overview as $i=>$v){
		$j=$i+3;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A{$j}", $i+1)
			->setCellValue("B{$j}", $depart[$v['first_will']])
			->setCellValue("C{$j}", $v['itv_code'])
			->setCellValue("D{$j}", $v['name'])
			->setCellValue("E{$j}", $depart[$v['second_will']])
			->setCellValue("F{$j}", $v['class'])
			->setCellValue("G{$j}", $v['phone'])
			->setCellValue("H{$j}", toResult($v['fir_result']))
			->setCellValue("I{$j}", toResult($v['sec_result']));
    	$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    	$objPHPExcel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	}
	$objStyle = $objPHPExcel->getActiveSheet();
	$objStyle->getColumnDimension('A')->setWidth(8);
	$objStyle->getColumnDimension('B')->setWidth(10);
	$objStyle->getColumnDimension('C')->setWidth(10);
	$objStyle->getColumnDimension('D')->setWidth(10);
	$objStyle->getColumnDimension('E')->setWidth(10);
	$objStyle->getColumnDimension('F')->setWidth(20);
	$objStyle->getColumnDimension('G')->setWidth(20);
	$objStyle->getColumnDimension('H')->setWidth(10);
	$objStyle->getColumnDimension('I')->setWidth(10);

	$objStyle->getStyle('A:I')->getAlignment()->setWrapText(true);
   	$objStyle->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objStyle->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objStyle->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
   	$objStyle->getStyle('A1')->getFont()->setBold(true)->setSize(13);

    $objStyle->setTitle('面试系统');
    $objPHPExcel->setActiveSheetIndex(0);

    ob_end_clean();
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');

	$department = '所有';
	$turn = '';
	if (isset($_GET['department']) && !empty($_GET['department'])) {
		$department = $depart[$_GET['department']];
	} 
	if (isset($_GET['turn']) && !empty($_GET['turn'])) {
		$turn = $_GET['turn'] == 1 ? '一面结果' : '二面结果';
	}
	header('Content-Disposition: attachment;filename="面试系统-部门('.$department.')-'.$turn.'.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>