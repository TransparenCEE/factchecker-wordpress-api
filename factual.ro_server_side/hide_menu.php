<?php
require_once(dirname(__FILE__)."/common/constructor.inc.php");

//include_once(dirname(__FILE__).'/admin_libraries/functions/authentication.function.php');
//$user_info = admin_autentification($_COOKIE[$cfg_array['cookie_user_login']]);

if(isset($_GET['do']))	{
	switch($_GET['do'])	{
		case 'hide':
			$action = 'Y';
			break;
		case 'show':
			$action = 'N';
			break;
		default:
			$action = 'Y'; 
			echo 'do=';
			exit();
	}
}

$admin_pages_array = array("'admin_pages_add_modify.php'", "'admin_pages.phpx'", "'admin_pages.php'", "'admin_pages_exec.php'", "'admin_sessions.php'", "'admin_session_details.php'");

$sql = "UPDATE {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_pages SET is_blocked='" .$action. "' WHERE url IN (".implode(",", $admin_pages_array).") ";
print $sql;
$db_write->query($sql);

?>
