<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');

$action = $_POST['action'];
$id_link = $_POST['id_link'];
$msg = '';
$separator = '%$';
$snippet = '';
$msgAction = '';

if($id_link ) {
	$sql_verify = "SELECT status FROM factcheck_content2links WHERE id_link = '".$db_write->escape($id_link) . "' ";
	$link_verify = $db_read->queryOne($sql_verify);
	if(!empty($link_verify)) {
		if($action == 'disable_link') {
			$table_fields_values['status'] = 'disabled';
			$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' " );
			$msgAction = 'linkDisabled';
		}
		if($action == 'modify_snippet') {
			$snippet = $db_write->escape(strip_tags($_POST['snippet']));
			$table_fields_values['snippet'] = $snippet;
			$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' " );
			$msgAction = 'snippetModified';
		}
	} else {
		$msgAction = 'linkNoExists';
		$result = 0;
	}
	$msg = $msgAction . $separator . $result . $separator . $snippet;
}

echo $msg;