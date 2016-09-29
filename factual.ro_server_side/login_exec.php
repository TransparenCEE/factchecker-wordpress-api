<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');
require_once(dirname(__FILE__)."/libraries/functions/audit.function.php");


//$policy = 'P3P: policyref="'.$cfg_array['policy_ref'].'", CP="'.$cfg_array['policy_CP'].'"';header("$policy");
//print_r($_POST);

if(empty($_POST['txt_username']) || empty($_POST['txt_password'])){
	$msg = 'login_error';
	$url = $cfg_array['site_url']."login.php?msg=$msg";
	if(!empty($_POST['txt_username'])){
		$url .= "&txt_username=".trim(strtolower($_POST['txt_username']));
	}
	header("Location: $url");
	exit();
}

$sql_login = "SELECT cookie_user_login, id_user FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users WHERE md5(username)=md5(TRIM(LOWER('".$db_write->escape($_POST['txt_username'])."'))) AND md5(password)=md5('".$db_write->escape($_POST['txt_password'])."') AND is_active='Y'";
$res_login =& $db_write->query($sql_login);
//errors_handler($res_login);

if ($row_login =& $res_login->fetchRow()){
	$cookie_user_login = $row_login['cookie_user_login'];

	$id_user = $row_login['id_user'];
	$page = "login.php";
	$time = time();
	$_SESSION[$cfg_array['cookie_user_login']] = $cookie_user_login;
	//setcookie($cfg_array['cookie_user_login'], $cookie_user_login, 0, '/');
	//setcookie($cfg_array['cookie_user_login']."_time", $time, 0, '/');
	adminAudit($id_user, $page, $time);
	//echo $_COOKIE['admin_cookie_login_next_url_engine'];exit();
	if(!empty($_COOKIE['admin_cookie_login_next_url_engine'])){
		$url = $_COOKIE['admin_cookie_login_next_url_engine'];
		setcookie("admin_cookie_login_next_url_engine", '', time()-3600, "/");
	}else{
		$url = $cfg_array['site_url']."index.php";
	}
}else{
	$url = $cfg_array['site_url']."login.php?msg=login_error";
	if(!empty($_POST['txt_username'])){
		$url .= "&txt_username=".trim(strtolower($_POST['txt_username']));
	}
}
//echo $url;exit();
header("Location: $url");
exit;
?>