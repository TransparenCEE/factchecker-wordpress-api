<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');
require_once(dirname(__FILE__) . '/libraries/functions/url_validation.function.php');

$action = $_POST['action'];
$id_factcheck = $_POST['id_factcheck'];
$msg = '';
$extra_uri = '';

if($action == 'add_link' && $id_factcheck) {
	//check link url
	$link = isValidURL($_POST['link']);
	echo '$link=' . $link;
	if(empty($link)) {
		$msg = 'addlink-0';
	} else {
		$link_identifier = str_ireplace(array('http://www.', 'http://', 'https://', 'https://www.'), '', $_POST['link']);
		$substring_last_slash = substr($link_identifier, -1, 1);
		if ($substring_last_slash == '/') {
			$link_identifier = substr($link_identifier, 0, strlen($link_identifier) - 1);
		}
		echo $link_identifier; 
		
		$table_fields_values = array(
			'id_post' => $_POST['id_post'],
			'link_content' => $_POST['link'],
			'id_factcheck' => $id_factcheck,
			'link_identifier' => $link_identifier,
			'md5_link_identifier' => md5($link_identifier),
			'status' => $_POST['status'],
			'unique_key' => $_POST['id_post'].md5($link_identifier), //Unique_key(id_post+md5_link_identifier)
			'insert_datetime' => date("Y-m-d H:i:s")
		);
		if(!empty($_POST['snipped'])) {
			$table_fields_values['snipped'] = $_POST['snipped'];
		}
		$import_result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_INSERT);
		$msg = 'insertlink-' . $import_result;
	}
	
	
	$extra_uri = "#fact_$id_factcheck";
}

header("Location: factchecks_list.php?id_factcheck=$id_factcheck&msg=$msg$extra_uri");
exit();