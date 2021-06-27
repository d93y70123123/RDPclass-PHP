<?php
	header("Access-Control-Allow-Origin: *");//這個必寫，否則報錯
    $output = shell_exec('netstat -ant | grep 120.114.141.1:80 |grep EST | wc -l');

    echo $output;
?>
