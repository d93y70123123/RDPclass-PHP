<?php
$check = $_REQUEST['check'];
// if($check == "1"){
    session_start();
    $id = str_replace("'","''",$_REQUEST['id']);
    $pw = str_replace("'","''",$_REQUEST['pw']);
    $_SESSION['id'] = $id;
    //$_SESSION['check'] = $check;
    define('ROOT_PATH', dirname(__FILE__));
    //echo " ---- Test by Bianjie. ---- <br>";
    $ldaphost = "127.0.0.1";  // your ldap server
    $ldapport = 389;                    // your ldap server's port number
    $ldapconn = ldap_connect($ldaphost);
    $saltpassword;
    if($ldapconn){
        //echo "[info]: Connect to LDAP server successful.<br>";
        if (ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3)){
            //echo "[info]: Using LDAP v3.<br>";
        }else{
           //echo "[info]: Failed to set version to protocol 3.<br>";
        }
        $ldapbind = ldap_bind($ldapconn,"cn=admin,dc=dic,dc=ksu","2727175");
        if ($ldapbind){
            //echo "[info]: LDAP bind successful.<br>";
            $result = ldap_search($ldapconn,"cn=".$id.",ou=login,dc=dic,dc=ksu","(cn=*)");
            if($result){
                $info = ldap_get_entries($ldapconn,$result);
                for ($i=0; $i<$info["count"]; $i++)   {
                /*echo "[info]: dn=   ".$info[$i]["dn"]."<br>";
                echo "[info]: cn=   ".$info[$i]["cn"][0]."<br>";
                echo "[info]: sn=   ".$info[$i]["sn"][0]."<br>";
                echo "[info]: Uidnumber= ".$info[$i]["uidnumber"][0]."<br />";
                echo "[info]: Gidnumber= ".$info[$i]["gidnumber"][0]."<br />";
                echo "[info]: DisplayName= ".$info[$i]["displayname"][0]."<br />";*/
                $user_id = $info[$i]["cn"][0];
                $saltpassword = $info[$i]["userpassword"][0];
                $title = $info[$i]["title"][0];
                $check_id = $info[$i]["cn"][0];
                $check2 = $info[$i]["displayname"][0];
                $_SESSION['name'] = $info[$i]["sn"][0];
                /*echo $check2.'<br/>';
                echo $check_id.'<br/>';
                echo "[info]: userPassword= " . $saltpassword. "<br>";*/
                }
            }else{
                //echo "[info]: LDAP bind failed.<br>";
            }
        }else{
            //echo "[info]: <font color=red>LDAP bind failed.</font><br>";
            //echo "[info]: " . ldap_error($ldapconn);
        }
    }else {
        //echo "[info]: <font color=red>Could not connect to LDAP server.</font><br>";
    }
    $str = $pw;
    $check = ssha_check($str, $saltpassword);
    if($check && $id == $user_id){
        //echo '[info]: verify successful.<br>';
        if($check2 == $check_id){
            echo '這要修改東西<br/>';
            if($title == 'teacher' ){
                $_SESSION['power'] = 2;
                echo '<meta http-equiv = "refresh" content = "0;url=inf.php">';
                echo '1這是老師請稍候頁面轉跳中';
            }
            elseif($title == 'student' ){
                $_SESSION['power'] = 3;
                echo '<meta http-equiv = "refresh" content = "0;url=inf.php">';
                echo '1這是學生請稍候頁面轉跳中';
            }
        }elseif($check2 == 1){
            if($title == 'superadm' ){
                $_SESSION['power'] = 1;
                echo '這是管理者請稍候頁面轉跳中';
                echo '<meta http-equiv = "refresh" content = "0;url=super/super.php">';
            }elseif($title == 'teacher' ){
                $_SESSION['power'] = 2;
                echo '這是老師請稍候頁面轉跳中';
                echo '<meta http-equiv = "refresh" content = "0;url=teacher/teacher.php">';
            }elseif($title == 'student' ){
                $_SESSION['power'] = 3;
                echo '這是學生請稍候頁面轉跳中';
                echo '<meta http-equiv = "refresh" content = "0;url=user/user.php">';
            }
        }       
    }else{
        echo '此帳號密碼有問題，請重新確認<a href="index.html">重新輸入</a>';
        echo '<meta http-equiv = "refresh" content = "2;url=index.html">';
    }
// }else{
//     echo '<meta http-equiv = "refresh" content = "0;url=index.html">';
// }

ldap_close($ldapconn);
//下列是SSHA的檢查
function ssha_check($str, $saltpassword) {
    $ohash = base64_decode(substr($saltpassword,6));
    $osalt = substr($ohash,20);
    $ohash = substr($ohash,0,20);
    $nhash = pack("H*",sha1($str.$osalt));
    return $ohash == $nhash;
}
?>

