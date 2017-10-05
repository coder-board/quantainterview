<?php
require 'header.php';
if (isset($_GET['itv_code'])){
    $itv_code = $_GET['itv_code'];

    //Get the personal infomation
    $sql = "SELECT * FROM interviewee_info WHERE itv_code='$itv_code' AND org_id='{$_SESSION['org']}'";
    $result = $db->query($sql);
    $info = $result->fetch_assoc();

    // Get the first interview infomation
    $sql = "SELECT * FROM first_itv WHERE itv_code='$itv_code'";
    $result = $db->query($sql);
    $fir_info = $result->fetch_assoc();

    // Get the second interview infomation
    $sql =  "SELECT * FROM second_itv WHERE itv_code='$itv_code'";
    $result = $db->query($sql);
    if ($result->num_rows == 1) {
        $sec_info = $result->fetch_assoc();
    	$data = $info + $fir_info + $sec_info + $session;

    } else {
    	$data = $info + $fir_info + $session;
    }
    $api = ["result"=>1, "msg"=>"success", "data"=>$data];
    echo json_encode($api);

} else {
    $api = ["result"=>100, "msg"=>"无对应字段，空结果", "data"=>$session];
    echo json_encode($api);  
}
?>