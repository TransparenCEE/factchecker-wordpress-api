<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');
require_once(dirname(__FILE__)."/libraries/functions/audit.function.php");
require_once(dirname(__FILE__)."/libraries/classes/PasswordHash.class.php");


//$policy = 'P3P: policyref="'.$cfg_array['policy_ref'].'", CP="'.$cfg_array['policy_CP'].'"';header("$policy");
//print_r($_POST);

$_POST['txt_username'] = trim($_POST['txt_username']); 
$_POST['txt_password'] = trim($_POST['txt_password']); 

if(empty($_POST['txt_username']) || empty($_POST['txt_password'])){
	$url = $cfg_array['site_url']."login.php?msg=empty_fields";
	if(!empty($_POST['txt_username'])) {
		$url .= "&txt_username=".trim(strtolower($_POST['txt_username']));
	}
	header("Location: $url");
	exit();
}

$sql_login = "SELECT cookie_user_login, id_user, password FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users WHERE md5(username)=md5(TRIM(LOWER('".$db_write->escape($_POST['txt_username'])."'))) AND is_active='Y'";
$res_login =& $db_write->query($sql_login);
if ($row_login =& $res_login->fetchRow()) {
	// Base-2 logarithm of the iteration count used for password stretching
	$hash_cost_log2 = 8;
	// Do we require the hashes to be portable to older systems (less secure)?
	$hash_portable = FALSE;
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
	if ($hasher->CheckPassword($_POST['txt_password'], $row_login['password'])) {
		$what = 'Authentication succeeded';
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
	} else {
		$what = 'Authentication failed';
		$url = $cfg_array['site_url']."login.php?msg=authentication_failed";
		if(!empty($_POST['txt_username'])) {
			$url .= "&txt_username=".trim(strtolower($_POST['txt_username']));
		}
		header("Location: $url");
		exit();
	}	
} else {
	$url = $cfg_array['site_url']."login.php?msg=no_user";
	if(!empty($_POST['txt_username'])){
		$url .= "&txt_username=".trim(strtolower($_POST['txt_username']));
	}
}
//echo $url;exit();
header("Location: $url");
exit;
?>