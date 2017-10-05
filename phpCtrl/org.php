<?php
	require './conn.php';
	$sql = "SELECT org_id, org_name FROM interview_org";
    $result = $db->query($sql);
    $org = [];
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $org[$i] = $row;
        $i++;
    }
    /* 输出最终筛选结果 */
    $api = ["result"=>1, "msg"=>"success", "data"=>$org];
    echo json_encode($api);
?>