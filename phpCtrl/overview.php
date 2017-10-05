<?php
    require 'header.php';
    // $year = date("Y", time());
    // Get personal infomation
    if (isset($_GET['year'])) {
        $year = $_GET['year'];
    } else {
        $year = date("Y", time());
    }
    $sql = "SELECT * FROM interviewee_info AS main LEFT JOIN first_itv AS fir ON main.itv_code=fir.itv_code LEFT JOIN second_itv AS sec ON main.itv_code=sec.itv_code WHERE year=$year AND org_id={$_SESSION['org']}";

    if (isset($_GET['department']) && !empty($_GET['department'])) {
        $dept = $_GET['department'];
        $sql .= " AND first_will = '$dept'";
    }

    if (isset($_GET['sex']) && !empty($_GET['sex']) && ($_GET['sex'] == 2 || $_GET['sex'] == 1)) {
        $sex = $_GET['sex'];
        $sql .= " AND sex = '$sex'";
    }

    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        $sql .= " AND (name LIKE '%".$keyword."%' OR class LIKE '%".$keyword."%' OR phone LIKE '%".$keyword."%' OR email LIKE '%".$keyword."%')";
    }

    if (isset($_GET['turn']) && isset($_GET['turn_result']) && 
        !empty($_GET['turn']) && !empty($_GET['turn_result'])) {
        $keyword = $_GET['turn'] == 1 ? 'fir_result' : 'sec_result';
        $turn_result = $_GET['turn_result'];
        $sql .= " AND $keyword = '$turn_result'";
        // $_GET['turn'] == 2 && $sql .= ' ORDER BY sec_grade DESC';
    }
    if ($_GET['turn'] == 2) {
        $sql .= ' ORDER BY sec_grade DESC';
    }

    $result = $db->query($sql);
    $overview = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $overview[$i] = $row;
        $i++;
    }

    /* 输出最终筛选结果 */
    $api = ["result"=>1, "msg"=>"success", "data"=>$overview];
    echo json_encode($api);
?>
