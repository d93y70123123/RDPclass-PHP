<?php
//http://120.114.140.44/config/idcheck.php?id=kai&pw=kaikai
    header("Access-Control-Allow-Origin: *");//這個必寫，否則報錯，對VUE來說
    header("Access-Control-Allow-Headers: *");
    
    $id = str_replace("'","''",$_REQUEST['id']);
    $pw = str_replace("'","''",$_REQUEST['pw']);
    

    // 連線資訊
    $ldaphost = "ldap://127.0.0.1";  // your ldap server
    $ldapport = 389;                    // your ldap server's port number
    $ldapconn = ldap_connect($ldaphost,636) or die("That LDAP-URI was not parseable");
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    // echo ldap_error($ldap);

    // 進行身份驗證
    $ldapbind = ldap_bind($ldapconn,"uid=".$id.",ou=people,dc=dic,dc=ksu",$pw);
    if ( $ldapbind != 1 ){
        die("認證失敗".$ldapbind." id: ".$id);
    }

    // 查詢使用者相關資料
    $result = ldap_search($ldapconn, "uid=".$id.",ou=people,dc=dic,dc=ksu", "(cn=*)");
    $info = ldap_get_entries($ldapconn,$result);
    
    // 取得使用者相關資料
    $Username = $info[0]["cn"][0];
    $Gid = $info[0]["gidnumber"][0];
    $Homedir = $info[0]["homedirectory"][0];
    $DisplayName = $info[0]["displayname"][0];
    $Description = $info[0]["description"][0];

    if ( $Description == null ){
        echo '第一次登入';
        // 透過此指令新增description : dsidm idserver user modify kai "add:description:Alice Test User"
    }else{
        // echo "認證成功".$ldapbind." id: ".$id;
        // 記得安裝php-json
        $arr = array($ldapbind,$Username, $Gid, $Homedir, $DisplayName, $Description);
        $json = json_encode($arr);
        echo $json;
    }

    // 關閉LDAP連線
    ldap_close($ldapconn);
?>