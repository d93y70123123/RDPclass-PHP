<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");
	// usestatus： 1. ok 2. hostopen 3. vmopen

	$Username = $_REQUEST['username'];
	$servername = 'localhost';
	$username = 'root';
	$password = '2727175#356';
	$DBname = 'RDPclass';
	$mysqli = new mysqli($servername, $username, $password, $DBname);
	$sql = "select * from pre_open";
	$res = $mysqli->query("$sql");
	$pre_open = $res->num_rows;
	// printf($pre_open);


	$sql = "select * from userinfo where username='$Username'";
	$res = $mysqli->query("$sql");
	// printf('<br/>'.$res->num_rows);

	// 1. 判斷有沒有用過
	if( $res->num_rows == 1 ){
		// echo "<br />登入過，現有紀錄：";
		$row = $res->fetch_array();
		$seat = $row[3];
		$seatIP = $row[4];
		$usestatus = $row[5];
		$arr = array($pre_open, $seat, $seatIP, $usestatus);
	}else{
		$arr = array($pre_open, null, null, null);
	}
	
	// $arr = array($pre_open, $seat, $seatIP, $usestatus);
	$json = json_encode($arr);
	echo $json;
	mysqli_close($mysqli);
?>
