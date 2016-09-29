<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');
require_once(dirname(__FILE__)."/libraries/functions/audit.function.php");


$page = "logout.php";
//verific pe ce user sunt logat
$sql_auth = " SELECT id_user, username, password
				FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users
				WHERE cookie_user_login='" .$_SESSION['cookie_user_login']. "'";

$res_auth = $db_write->query($sql_auth);
$row_auth = $res_auth->fetchRow();
$user_id =	$row_auth['id_user'];
adminAudit($user_id, $page);
unset($_SESSION[$cfg_array['cookie_user_login']]);
//setcookie($cfg_array['cookie_user_login'], '', time()-3600, '/', $cfg_array['cookie_domain']);
header("Location: ".$cfg_array['site_root_path']."index.php");
exit();
?>