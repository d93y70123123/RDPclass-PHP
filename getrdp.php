<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");
	// usestatus： 0. 沒有使用 1. 使用中 2. 實體機開機中 3. 虛擬機開機中 4. 確認機器中 5. 機器開啟錯誤
	$Username = $_REQUEST['username'];
	$UserIP=getip();
	$UserPort = rand(10000,30000);

	$servername = 'localhost';
	$username = 'root';
	$password = '2727175#356';
	$DBname = 'RDPclass';
	$mysqli = new mysqli($servername, $username, $password, $DBname);
	$sql = "select * from userinfo where username='$Username'";
	$res = $mysqli->query("$sql");

	$sql = "select * from userinfo where username='$Username'";
	$res = $mysqli->query("$sql");
	//printf($res->num_rows);

	// 1. 判斷有沒有用過
	if( $res->num_rows == 1 ){
		// echo "<br />登入過，現有紀錄：";
		$row = $res->fetch_array();
		$seat = $row[3];
		$seatIP = $row[4];

		$sql = "update userinfo set IP='${UserIP}',port='${UserPort}',seat='${seat}',seatIP='${seatIP}',usestatus=4 where username='${Username}'";
		$res = $mysqli->query("$sql");

		$usestatus = shell_exec("sudo /bin/sh /root/script/RDPclass/single_scan.sh $seat $seatIP");
		// I. 檢查使用過的機器是否有人使用
		if( $usestatus == 0 ){
			exec("sudo /bin/sh /root/script/RDPclass/openconnect.sh $Username $UserIP $UserPort $seat $seatIP");
			// echo "exec('/bin/sh /root/script/RDPclass/openconnect.sh $Username $UserIP $UserPort $seat $seatIP')";
		}else{ // II. 如果有人使用，就檢查有沒有預備機
			$sql = "select * from pre_open";
			$res = $mysqli->query("$sql");
			// printf($res->num_rows);
			// III. 如果有預備機，就用預備機
			$num=$res->num_rows;
			echo '有紀錄，要判斷有沒有預備機<br/>';;
			if( $num == 1 ){
				echo '有紀錄，也有預備機';
				$row = $res->fetch_array();
				$seat = $row[1];
				$seatIP = $row[3];
				$usestatus = shell_exec("sudo /bin/sh /root/script/RDPclass/single_scan.sh $seat $seatIP");
				if( $usestatus == 0 ){
					echo '有預備機，可以直接使用';
					exec("sudo /bin/sh /root/script/RDPclass/openconnect.sh $Username $UserIP $UserPort $seat $seatIP");
				}else {
					echo '有預備機但有人使用，所以自動尋找新的';
					exec("echo 'sudo /bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort' |at now");
				}
			}else{ // IV. 如果沒有預備機，就自動尋找新的機器
				echo '沒有預備機，所以自動尋找新的';
				exec("echo 'sudo /bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort' |at now");
			}
			// echo "exec('/bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort $seat $seatIP')";
		}
		$arr = array($seat, $seatIP, $usestatus);
        $json = json_encode($arr);
		echo $json;
	}else{// 2. 如果沒有使用過，接著判斷有沒有預備機
		echo '沒有使用紀錄，判斷有沒有預備機<br/>';
		$sql = "select * from pre_open";
		$res = $mysqli->query("$sql");
		// printf($res->num_rows);
		if( $res->num_rows == 1 ){
			echo '沒有紀錄，有預備機';
			$row = $res->fetch_array();
			$seat = $row[1];
			$seatIP = $row[3];
			
			$sql = "update userinfo set IP='${UserIP}',port='${UserPort}',seat='${seat}',seatIP='${seatIP}',usestatus=4 where username='${Username}'";
			$res = $mysqli->query("$sql");
			
			$usestatus = shell_exec("sudo /bin/sh /root/script/RDPclass/single_scan.sh $seat $seatIP");
			if( $usestatus == 0 ){
				echo '預備機可用，直接連線';
				exec("sudo /bin/sh /root/script/RDPclass/openconnect.sh $Username $UserIP $UserPort $seat $seatIP");
			}else {
				echo '預備機不能用，自動尋找新連線';
				exec("echo 'sudo /bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort' |at now");
			}
		}else{ // 3. 如果沒有使用過，也沒有預備機，自動尋找新的機器
			echo "echo 'sudo /bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort' |at now";
			exec("echo 'sudo /bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort' |at now");
		}
		// 這段沒用到，測試完後刪掉
		// $pre_open = shell_exec("sudo /bin/sh /root/script/RDPclass/single_scan.sh $seat $seatIP");
		// if( $pre_open ){
		// 	$usestatus = shell_exec("/bin/sh /root/script/RDPclass/single_scan.sh $seat $seatIP");
		// 	if( $usestatus == 0 ){
		// 		exec("/bin/sh /root/script/RDPclass/openconnect.sh $Username $UserIP $UserPort $seat $seatIP");
		// 	}else {
		// 		exec("/bin/sh /root/script/RDPclass/autocreate.sh $Username $UserIP $UserPort $seat $seatIP");
		// 	}
		// }
	}

	mysqli_close($mysqli);

	function getip(){
		//取得IP
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$myip = $_SERVER['HTTP_CLIENT_IP'];
		}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$myip = $_SERVER['REMOTE_ADDR'];
		} 
		return $myip;
	}
?>