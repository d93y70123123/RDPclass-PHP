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
	$sql = "select * from userinfo where username='$Username'";
	$res = $mysqli->query("$sql");
	//printf($res->num_rows);

	// 判斷有沒有用過
	if( $res->num_rows == 1 ){
		// echo "<br />登入過，現有紀錄：";
		$row = $res->fetch_array();
		$User_IP = $row[1];
		$user_port = $row[2];
		$seat = $row[3];
		$seatIP = $row[4];

		$usestatus = shell_exec("sudo /bin/sh /root/script/RDPclass/disconnect.sh $Username $User_IP $user_port $seat $seatIP");
		// echo "sudo /bin/sh /root/script/RDPclass/disconnect.sh $Username $User_IP $user_port $seat $seatIP";
		
		// $arr = array($seat, $seatIP, $usestatus);
        // $json = json_encode($arr);
		// echo $json;

		$sql = "update userinfo set IP='${User_IP}',port='${user_port}',seat='${seat}',seatIP='${seatIP}',usestatus=0 where username='${Username}'";
		$disconnect = $mysqli->query("$sql");
	}

	mysqli_close($mysqli);
?>
