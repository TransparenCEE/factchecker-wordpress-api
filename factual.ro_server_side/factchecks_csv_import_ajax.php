<?php
include_once(dirname(__FILE__).'/common/constructor.inc.php');
require_once(dirname(__FILE__) . '/libraries/functions/url_validation.function.php');
require_once(dirname(__FILE__) . '/libraries/functions/adminFactcheckList.function.php');


$id_factcheck = $_POST['id_factcheck'];
$ID = $_POST['ID'];
$page_no = $_POST['page'];
//import_csv
$extra_uri = "?page_no=$page_no&id_f=$id_factcheck";
$file_type_array = ['text/comma-separated-values', 'text/csv', 'application/csv'];

//echo '<pre>'; print_r($_POST); echo '</pre>';
//echo '<pre>'; print_r($_FILES); echo '</pre>';
//exit();

if(in_array($_FILES['import_csv']['type'],$file_type_array)) {
	$links = 0;
	$row = 1;
	if (($handle = fopen($_FILES['import_csv']['tmp_name'], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
//			echo "<p> $num fields in line $row: <br /></p>\n";
			$row++;
			for ($c=0; $c < $num; $c++) {
				echo $data[$c] . "<br />\n";
				$link = $data[0];
				$snipped = $data[1];
//				echo '$link = ' . $link . ' ---' . $snipped . '<br>';
				
				$link_validate = isValidURL($link); 
				if(!empty($link_validate)) {						
					$link_identifier = str_ireplace(array('http://www.', 'http://', 'https://', 'https://www.'), '', $link);
					$substring_last_slash = substr($link_identifier, -1, 1);
					if ($substring_last_slash == '/') {
						$link_identifier = substr($link_identifier, 0, strlen($link_identifier) - 1);
					}

					$sql_check_link_exists = "SELECT id_link FROM factcheck_content2links WHERE id_post = '" . $db_read->escape($ID) . "' AND id_factcheck = '" . $id_factcheck . "'   AND link_identifier = '$link_identifier'";	
//					echo $sql_check_link_exists . '<br>';
					$res_check_link_exists = $db_read->queryOne($sql_check_link_exists);
//					echo 'id_link exists: ' . ($res_check_link_exists) . '<br>'; 
					if(empty($res_check_link_exists)) { //import link
						$id_link = $db_write->nextId('factcheck_content2links');
						$table_fields_values = array(
							'id_link' => $id_link,
							'id_post' => $db_write->escape($ID),
							'link_content' => $db_write->escape($link),
							'id_factcheck' => $id_factcheck,
							'link_identifier' => $link_identifier,
							'md5_link_identifier' => md5($link_identifier),
							'insert_datetime' => date("Y-m-d H:i:s")
						);
						if(!empty($snipped)) {
							$table_fields_values['snipped'] = $db_write->escape(strip_tags($snipped));
						}
//						echo '<pre>'; print_r($table_fields_values); echo '</pre>'; exit(); 
						$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_INSERT);
					} else {
						$table_fields_values['status'] = 'active';
						$result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$res_check_link_exists' " );
					}
//					echo '<br>---$result=' . $result. '<br>';
					if($result > 0) {
						$links++;
					}
				} else {
					//invalid link
				}
			}
		}
		$extra_uri .= "&msg=linksImportedActivated&result=$links";
		fclose($handle);
	} else {
		$extra_uri .= "&msg=fileOpenError";
	}
} else {
	$extra_uri .= "&msg=fileNoCsv";
}
header("Location: factchecks_list.php$extra_uri#fact_$id_factcheck");
exit();

