<?php
	require './header.php';
	$sql = "SELECT * FROM org_depart WHERE org_id='{$_SESSION['org']}'";
    $result = $db->query($sql);
    $depart = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $depart[$i] = $row;
        $i++;
    }

    $y = date("Y", time());
    $sql = "SELECT DISTINCT year FROM interviewee_info WHERE org_id='{$_SESSION['org']}' ORDER BY year DESC";
    $result = $db->query($sql);
    $year = [];
    if ($result->num_rows == 0) {
        $year[0] = ["tag"=>$y, "id"=>$y];
    } else {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            if ($i == 0 && $row['year'] != $y) {
                $row = ["tag"=>$y, "id"=>$y];
            } else {
                $row = ["tag"=>$row['year'], "id"=>$row['year']];
            }
            $year[$i] = $row;
            $i++;
        }
    }
    
    /* 输出最终筛选结果 */
    $data = ["depart"=>$depart, "year"=>$year];
    $api = ["result"=>1, "msg"=>"success", "data"=>$data];
    echo json_encode($api);
?>