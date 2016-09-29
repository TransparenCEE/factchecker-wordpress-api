<?php
$make_audit = "N";
function adminAudit($user_id, $page_name, $time = NULL)	{
	global $db_write, $cfg_array, $make_audit;
	if($make_audit == "Y")	{
		$currentDate = date('Y-m-d H:i:s');
		$timestamp = (!is_null($time))? $time : time();
		$ident = (!is_null($time))? $time : $_COOKIE[$cfg_array['cookie_user_login'].'_time'];

		$table_name = "{$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_audit";
		$table_fields_values = array(
		'audit_date' => $currentDate,
		'audit_user_id' => $user_id,
		'audit_ip' => $_SERVER['REMOTE_ADDR'],
		'audit_page_name' => $page_name,
		'audit_timestamp' => $timestamp,
		'audit_session_ident' => $ident
		);
		$db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);

		return true;
	}
}
?>