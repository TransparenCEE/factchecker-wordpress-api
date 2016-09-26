<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');

$action = $_POST['action'];
$id_link = $_POST['id_link'];
$msg = '';
$separator = '%$';
$snipped = '';
$msgAction = '';
//echo '<pre>'; print_r($_POST); echo '</pre>';


if($id_link ) {
	$sql_verify = "SELECT status FROM factcheck_content2links WHERE id_link = '".$db_write->escape($id_link) . "' ";
	$link_verify = $db_read->queryOne($sql_verify);
	if(!empty($link_verify)) {
		if($action == 'disable_link') {
			$table_fields_values['status'] = 'disabled';
			$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' " );
			$msgAction = 'linkDisabled';
		}
		if($action == 'modify_snipped') {
			$snipped = $db_write->escape(strip_tags($_POST['snipped']));
			$table_fields_values['snipped'] = $snipped;
			$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' " );
			$msgAction = 'snippedModified';
		}
	} else {
		$msgAction = 'linkNoExists';
		$result = 0;
	}
	$msg = $msgAction . $separator . $result . $separator . $snipped;
}

echo $msg;
