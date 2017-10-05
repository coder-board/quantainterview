<?php
	require 'header.php';
	if (isset($_POST['first_will'])) {
		//录入个人信息
		$first_will = $_POST['first_will'];
		$second_will = $_POST['second_will'];
		$name = $_POST['name'];
		$sex = $_POST['sex'];
		$class = $_POST['class'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$other_depart = $_POST['other_depart'];
		$year = date("Y", time());

		switch ($first_will) {
			case 1:
				$depart = 'C';
				break;
			case 2:	
				$depart = 'D';
				break;
			case 3:	
				$depart = 'O';
				break;
			case 4:	
				$depart = 'B';
				break;
			case 5:
				$depart = 'A';
				break;
			case 6:
				$depart = 'I';
				break;
			default:
				$depart = 'O';
				break;
		}
		$query = "SELECT COUNT(itv_id) AS count FROM interviewee_info WHERE first_will=$first_will ";
		$result = $db->query($query);
		$count = $result->fetch_assoc();
		$itv_code = $count['count'] + 1;
		$tmp_code = $depart.$itv_code;
		// 排查重复
		$query = "SELECT itv_code FROM interviewee_info WHERE itv_code = '$tmp_code'";
		$result = $db->query($query);
		while ($result->num_rows > 0) {
			$itv_code++;
			$tmp_code = $depart.$itv_code;
			$query = "SELECT itv_code FROM interviewee_info WHERE itv_code = '$tmp_code'";
			$result = $db->query($query);
		}
		$itv_code = $depart.$itv_code;

		$year = date("Y", time());
		$query = "INSERT INTO interviewee_info (name, sex, itv_code, class, email, phone, first_will, second_will, other_depart, year, org_id) 
				VALUES ('$name','$sex','$itv_code','$class','$email','$phone','$first_will','$second_will','$other_depart', '$year', '{$_SESSION['org']}')";
		$result = $db->query($query);
		if ($db->affected_rows > 0) {
			// 录入一面结果
			$fir_comment = $_POST['fir_comment'];
			$fir_grade = $_POST['fir_grade'];
			$fir_result = $_POST['fir_result'];
			$fir_interviewer = $_SESSION['name'];

			$query = "INSERT INTO first_itv (itv_code, fir_comment, fir_result, fir_grade, fir_interviewer)
				VALUES ('$itv_code', '$fir_comment', '$fir_result', '$fir_grade', '$fir_interviewer')";
			$result1 = $db->query($query);

			// 录入二面结果
			$sql = "INSERT INTO second_itv (itv_code, sec_comment, sec_grade, sec_result, sec_interviewer)
				VALUES ( '$itv_code', '{$_POST['sec_comment']}', '{$_POST['sec_grade']}', '{$_POST['sec_result']}', '{$_SESSION['name']}')";
			$result2 = $db->query($sql);
			if ($db->affected_rows > 0) {
				$data = ["itv_code"=>$itv_code, "first_will"=>$first_will];
				$api = ["result"=>1, "msg"=>"新增成功，获得新的查询码：$itv_code", "data"=>$data];

			} else {
				$api = ["result"=>-1, "msg"=>"新增失败", "data"=>[]];
			}
			

		} else {
			$api = ["result"=>-1, "msg"=>"新增失败", "data"=>[]];
		}

		echo json_encode($api);

	} else {
		$api = ["result"=>-1, "msg"=>"无参数", "data"=>[]];
		echo json_encode($api);
	}
?>