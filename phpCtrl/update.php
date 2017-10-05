<?php
	require 'header.php';
	if (isset($_POST['itv_code']) && isset($_POST['name'])) {
		$itv_code = $_POST['itv_code'];
		 //save personal info
		$sql = "UPDATE interviewee_info SET 
				name = '{$_POST['name']}',
				sex = '{$_POST['sex']}',
				first_will = '{$_POST['first_will']}',
				second_will = '{$_POST['second_will']}',
				phone = '{$_POST['phone']}',
				email = '{$_POST['email']}',
				class = '{$_POST['class']}',
				other_depart = '{$_POST['other_depart']}'
				WHERE itv_code = '$itv_code'";
		$result1 = $db->query($sql);

		// save fir_itv
		$sql = "UPDATE first_itv SET 
				fir_result = '{$_POST['fir_result']}',
				fir_comment = '{$_POST['fir_comment']}',
				fir_grade = '{$_POST['fir_grade']}'
				WHERE itv_code = '$itv_code'";
		$result2 = $db->query($sql);

		// save sec_itv
		// check whether exist the result
		$sql = "SELECT second_id FROM second_itv WHERE itv_code='$itv_code'";
		$result3 = $db->query($sql);
		$result4 = '';
		if ($result3->num_rows == 0) {
			$sql = "INSERT INTO second_itv (itv_code, sec_comment, sec_grade, sec_result, sec_interviewer)
			VALUES ( '$itv_code', '{$_POST['sec_comment']}','{$_POST['sec_grade']}','{$_POST['sec_result']}','{$_SESSION['name']}')";
			$result4 = $db->query($sql);

		} else if ($result3->num_rows == 1) {
			$sql = "UPDATE second_itv SET 
				sec_comment = '{$_POST['sec_comment']}', 
				sec_grade = '{$_POST['sec_grade']}', 
				sec_result= '{$_POST['sec_result']}', 
				sec_interviewer = '{$_POST['sec_interviewer']}'
				WHERE itv_code = '$itv_code'";
			$result4 = $db->query($sql);

		} else {
			$api = ["result"=>-1, "msg"=>"更新未成功，请重试", "data"=>[]];
			echo json_encode($api);
		}

		if ($result4) {
			$api = ["result"=>1, "msg"=>"更新成功", "data"=>[]];
			echo json_encode($api);
		}
	}
	
?>
