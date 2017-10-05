<?php
	require './header.php';
	$year = date("Y", time());

	/* 输出所有部门，默认置0 */
	$sql = "SELECT depart_name FROM org_depart WHERE org_id='{$_SESSION['org']}'";
	$result = $db->query($sql);
	$i = 0;
	$depart = [];
	while ($row = $result->fetch_assoc()) {
		$depart[$i]['key'] = $row['depart_name'];
		$depart[$i]['value'] = 0;
		$i++;
	}

	/* 数数，然后修改对应的部门人数 */
	$sql = "SELECT depart_name, COUNT(itv_code) AS count FROM interviewee_info JOIN org_depart ON first_will=depart_id WHERE year=$year GROUP BY depart_id";
	$result = $db->query($sql);
	while ($row = $result->fetch_assoc()) {
		foreach ($depart as $key => $value) {
			if ($value['key'] == $row['depart_name']) {
				$depart[$key]['value'] = $row['count'];
			}
		}
	}

	$api = ["result"=>1, "msg"=>"success", "data"=>$depart];
	echo json_encode($api);
?>