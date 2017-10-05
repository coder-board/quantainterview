<?php
	header("Content-type: text/html;charset=utf-8");
	require './conn.php';
	if (isset($_POST['check']) && $_POST['check'] == 'joinInterview') {
		$org_name = $_POST['name'];
		$org_pwd = md5($_POST['pwd']);
		$sql = "SELECT org_id FROM interview_org WHERE org_name='$org_name'";
		$result = $db->query($sql);
		if ($row = $result->fetch_assoc() > 0) {
			$api = ["result"=>-100, "msg"=>"该社团已被注册", "data"=>[]];
			echo json_encode($api);
			exit();
		}	
		$sql = "INSERT INTO interview_org (org_pwd, org_name, regDate) VALUES ('$org_pwd', '$org_name', NOW())";
		$result = $db->query($sql);
		if ($db->affected_rows > 0) {
			$api = ["result"=>1, "msg"=>"注册成功，现在前往登录", "data"=>[]];

		} else {
			$api = ["result"=>-2, "msg"=>"新增失败", "data"=>[]];
		}
		echo json_encode($api);

	} else {
		$api = ["result"=>-1, "msg"=>"请输入有效的注册码", "data"=>[]];
		echo json_encode($api);
	}
?>