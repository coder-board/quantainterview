<?php
    $db = new mysqli('localhost','root','','quanta_interview') or die('Error: '.mysqli_error($db));
    // $db = new mysqli('120.24.78.54','root','76a4aa3224','quanta_interview') or die('Error: '.mysqli_error($db));
	$db->query('set names utf8');
?>
