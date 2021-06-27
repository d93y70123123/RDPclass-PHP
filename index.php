<?php
	header("Access-Control-Allow-Origin: *");//這個必寫，否則報錯
	// $mysqli=new mysqli('localhost','root','passwd','table');//根據自己的資料庫填寫

	// $sql="select * from users";
	// $res=$mysqli->query($sql);

	// $arr=array();
	// while ($row=$res->fetch_assoc()) {
	// 	$arr[]=$row;
	// }
	// $res->free();
	// //關閉連線
	// $mysqli->close();
	
	// echo(json_encode($arr));//這裡用echo而不是return
    echo true;
?>
