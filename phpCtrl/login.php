<?php
    session_start();
    require './conn.php';
    if (isset($_POST['name']) && isset($_POST['check']) && 
        isset($_POST['org'])) {

        $org_id = $_POST['org'];
        $org_pwd = md5($_POST['check']);
        $sql = "SELECT * FROM interview_org WHERE org_id=$org_id AND org_pwd='$org_pwd'";
        $result = $db->query($sql);
        if ($result->num_rows == 1) {
            $info = $result->fetch_assoc();
            $_SESSION['name'] = $_POST['name'];
            $_SESSION['org'] = $info['org_id'];
            $api = ["result"=>1, "msg"=>"success", "data"=>[]];
            echo json_encode($api);

        }else {
            $sql = "SELECT * FROM interview_org WHERE org_id=$org_id";
            $result = $db->query($sql);
            if ($result->num_rows == 1) 
                $api = ["result"=>-1, "msg"=>"密码有误", "data"=>[]];
            else
                $api = ["result"=>-2, "msg"=>"该社团尚未注册", "data"=>[]];
            echo json_encode($api);
        }

    } else {
    	$api = ["result"=>-404, "msg"=>"no right", "data"=>[]];
        echo json_encode($api);
    }
?>