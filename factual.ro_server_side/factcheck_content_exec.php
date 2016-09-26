<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');

$arr_meta_names  = [
	'context',
	'declaratie',
	'concluzie',
	'ce_verificam',
	'verificare',
	'categoria',
	'sursa_afirmatiei_link', // nu exista la toate posturile
];
$sql_stare2post = "SELECT post_id, meta_value FROM `wp_postmeta` WHERE meta_key = 'stare' ";
$arr_stare = $db_read->queryAll($sql_stare2post, null, MDB2_FETCHMODE_FLIPPED, true);
//echo '<pre>$arr_stare'; print_r($arr_stare); echo '</pre>';
$msg = '';

$action = $_POST['action'];
if($action == 'import') {
	$last_post_id_imported = $_POST['last_id'];
	$sql_factchecks = "SELECT ID, post_date, post_title, post_type, post_name, post_modified FROM {$cfg_db_array['db_wp']}.`wp_posts` WHERE ID > '$last_post_id_imported' AND {$cfg_extra['factcheck_publish_cond']} ";
	$sql_factchecks .= $cfg_extra['factcheck_order2import'];
	//$sql_factchecks .= "LIMIT 5";
	$res_factchecks = $db_read->query($sql_factchecks);
	$import_no = 0;
	while($row_factcheck = $res_factchecks -> fetchRow()) {
	//	echo '<pre>'; print_r($row_factcheck); echo '</pre>';
		$factcheck_link = 'http://www.factual.ro/' . $row_factcheck['post_type'] . '/' . $row_factcheck['post_name'] . '/';
		$table_fields_values = array(
			'ID' => $row_factcheck['ID'],
			'post_datetime' => $row_factcheck['post_date'],
			'post_modified_datetime' => $row_factcheck['post_modified'],
			'factcheck_link' => $factcheck_link,
			'post_title' => $row_factcheck['post_title'],
			'post_type' => $row_factcheck['post_type'],
			'post_name' => $row_factcheck['post_name'],
			'import_datetime' => date("Y-m-d H:i:s")
		);
		$sql_postmeta2factcheck = "SELECT meta_key, meta_value FROM `wp_postmeta` WHERE post_id = '{$row_factcheck['ID']}' ";
		$array_postmeta2factcheck = $db_read->queryAll($sql_postmeta2factcheck, null, MDB2_FETCHMODE_FLIPPED);
	//	echo '<pre>$array_postmeta2factcheck'; print_r($array_postmeta2factcheck); echo '</pre>';
		foreach ($array_postmeta2factcheck[0] as $key => $meta_name) {
			if(in_array($meta_name,$arr_meta_names)) {
				if($meta_name == 'sursa_afirmatiei_link') {
					$table_fields_values['url_sursa']  = $array_postmeta2factcheck[1][$key];
				} else {
					$table_fields_values[$meta_name]  = $array_postmeta2factcheck[1][$key];
				}
			}
			if($meta_name == 'status') {
				$table_fields_values['status'] = $arr_stare[$array_postmeta2factcheck[1][$key]];
			}
		}
	//	echo '<pre>$table_fields_values'; print_r($table_fields_values); echo '</pre>';
		$last_post_id = $row_factcheck['ID'];
		$import_result = $db_write->extended->autoExecute('factcheck_content', $table_fields_values, MDB2_AUTOQUERY_INSERT);
	//	echo '<pre>$import_result ='; print_r($import_result); echo '</pre>'; 
		unset($table_fields_values);
		$import_no++;
	}

	$sql_update_last_id = "UPDATE `general_values` SET var_value = '$last_post_id' WHERE var_name = 'last_post_id_imported'"; 
	$do_update_last_id = $db_write->query($sql_update_last_id);
	//echo '<pre>$do_update_last_id= '; print_r($do_update_last_id); echo '</pre>';
	
	$msg = "import-$import_no";
}

if($action == 'update') {
	//Se face update pt posturile care nu au modificat in wp_posts data update-ului
	$sql_factchecks = "SELECT  a.ID, b.post_title, b.post_name, b.post_type, b.post_modified FROM  factcheck_content as a, {$cfg_db_array['db_wp']}.wp_posts as b WHERE a.ID = b.ID AND b.post_modified != a.post_modified_datetime ORDER BY a.id_factcheck ASC";
	
	//Se face update pt posturile care nu au modificat in wp_posts data update-ului sau statusul nu mai este publish
//	$sql_factchecks = "SELECT  a.ID, b.post_title, b.post_name, b.post_type, b.post_modified, b.post_status FROM  factcheck_content as a, {$cfg_db_array['db_wp']}.wp_posts as b WHERE a.ID = b.ID AND (b.post_modified != a.post_modified_datetime OR b.post_status != 'publish') ORDER BY a.id_factcheck ASC";
	
//	echo $sql_factchecks; exit();
	
	$arr_ids2update = $db_read->queryAll($sql_factchecks);
	$export_no = 0;
	if(!empty($arr_ids2update)) {
		foreach($arr_ids2update as $factcheck) {
			$factcheck_link = 'http://www.factual.ro/' . $factcheck['post_type'] . '/' . $factcheck['post_name'] . '/';		
			$table_fields_values = array(
				'factcheck_link' => $factcheck_link,
				'post_title' => $factcheck['post_title'],
				'post_type' => $factcheck['post_type'],
				'post_name' => $factcheck['post_name'],
				'post_modified_datetime' => $factcheck['post_modified'],
				'update_import_datetime' => date("Y-m-d H:i:s")
			);
			$sql_postmeta2factcheck = "SELECT meta_key, meta_value FROM `wp_postmeta` WHERE post_id = '{$factcheck['ID']}' ";
			$array_postmeta2factcheck = $db_read->queryAll($sql_postmeta2factcheck, null, MDB2_FETCHMODE_FLIPPED);
	//		echo '<pre>$array_postmeta2factcheck'; print_r($array_postmeta2factcheck); echo '</pre>';
			foreach ($array_postmeta2factcheck[0] as $key => $meta_name) {
				if($meta_name == 'sursa_afirmatiei_link') {
					$table_fields_values['url_sursa']  = $array_postmeta2factcheck[1][$key];
				} else {
					$table_fields_values[$meta_name]  = $array_postmeta2factcheck[1][$key];
				}
				if($meta_name == 'status') {
					$table_fields_values['status'] = $arr_stare[$array_postmeta2factcheck[1][$key]];
				}
			}
			//echo '<pre>$table_fields_values ='; print_r($table_fields_values); echo '</pre>';  exit();
			$import_result = $db_write->extended->autoExecute('factcheck_content', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " ID = {$factcheck['ID']} ");
	//		echo '<pre>$import_result ='; print_r($import_result); echo '</pre>'; exit();
			$export_no++;
		}
	}
	$msg = "update-$export_no";
}

header("Location:factcheck_content.php?msg=$msg");
exit();
