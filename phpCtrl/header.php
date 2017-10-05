<?php
	header("Content-type: text/html;charset=utf-8");
	session_start();
    if (empty($_SESSION['name']) || empty($_SESSION['org'])){
        $api = ["result"=>-1, "msg"=>"no right", "data"=>[]];
        echo json_encode($api);
        exit();
    }
    $session = ['master'=>$_SESSION['name'], 'org'=>$_SESSION['org']];
    require './conn.php';
?>