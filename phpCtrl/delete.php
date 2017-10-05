<?php
if (isset($_GET['itv_code'])) {
	require 'header.php';
	$itv_code = $_GET['itv_code'];
	$sql = "DELETE FROM interviewee_info WHERE itv_code = '$itv_code'";
	$query = "SELECT * FROM interviewee_info WHERE itv_code='$itv_code'";
	$info = $db->query($query);
	$info = $info->fetch_assoc();
	$result = $db->query($sql);
	if ($db->affected_rows == 1) {
		$api = ["result"=>1, "msg"=>"删除成功", "data"=>$info];

	} else if ($db->affected_rows > 0) {
		$api = ["result"=>403, "msg"=>"严重错误，删除了多于一条的数据", "data"=>[]];
		
	} else {
		$api = ["result"=>-1, "msg"=>"未成功删除，请重试", "data"=>[]];
	}
	$sql = "DELETE FROM first_itv WHERE itv_code = '$itv_code'";
	$db->query($sql);
	$sql = "DELETE FROM second_itv WHERE itv_code = '$itv_code'";
	$db->query($sql);
	echo json_encode($api);	// 输出

} else {
	$api = ["result"=>-1, "msg"=>"无法进行删除操作，请传递所要删除的记录id", "data"=>[]];
	echo json_encode($api);	
}
?>