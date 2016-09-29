<?php

include_once(dirname(__FILE__) . '/common/constructor.inc.php');
require_once(dirname(__FILE__) . '/libraries/functions/url_validation.function.php');
require_once(dirname(__FILE__) . '/libraries/functions/adminFactcheckList.function.php');

$action = $_POST['action'];
$id_factcheck = $_POST['id_factcheck'];
$id_link = $_POST['id_link'];
$msg = '';
$extra_uri = '';
$separator = '%$';
//echo '<pre>'; print_r($_POST); echo '</pre>';
$_POST['link'] = trim($_POST['link']);

$link_identifier = str_ireplace(array('http://www.', 'http://', 'https://', 'https://www.'), '', $_POST['link']);
$substring_last_slash = substr($link_identifier, -1, 1);
if ($substring_last_slash == '/') {
    $link_identifier = substr($link_identifier, 0, strlen($link_identifier) - 1);
}

if ($id_factcheck) {
    if ($action == 'disable_link') {
        $sql_check_link_exists = "SELECT id_link FROM factcheck_content2links WHERE id_post = '" . $db_read->escape($_POST['id_post']) . "' AND id_factcheck = '" . $id_factcheck . "'  AND id_link = '" . $db_read->escape($_POST['id_link']) . "' ";
        $res_check_link_exists = $db_read->queryOne($sql_check_link_exists);
        if (!empty($res_check_link_exists)) {
            $id_link = $res_check_link_exists;
            $table_fields_values['status'] = 'disabled';
            $result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' ");
        } else {
            echo 'linkNoExists' . $separator . '0' . $separator . '';
            exit();
        }
    }
    if ($action == 'add_link') { // add link
        $link = isValidURL($_POST['link']);
        if (empty($link)) {
            $msg = 'addlink' . $separator . '0';
            exit();
        }
        $sql_check_link_exists = "SELECT id_link FROM factcheck_content2links WHERE id_post = '" . $db_read->escape($_POST['id_post']) . "' AND id_factcheck = '" . $id_factcheck . "'   AND link_identifier = '$link_identifier'";
        $res_check_link_exists = $db_read->queryOne($sql_check_link_exists);
        if (!empty($res_check_link_exists)) {
            //daca exista in db , dar este disable, il activam, dar facem update si pe snippet
            $id_link = $res_check_link_exists;
            $table_fields_values['status'] = 'active';
            $table_fields_values['snippet'] = $db_write->escape(strip_tags($_POST['snippet']));
            $result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' ");
            echo 'linkActivated' . $separator . $result . $separator . adminFactcheckList($id_factcheck, $_POST['id_post'], $db_read);
            exit();
        } else {
            $id_link = $db_write->nextId('factcheck_content2links');
            $table_fields_values = array(
                'id_link' => $id_link,
                'id_post' => $db_write->escape($_POST['id_post']),
                'link_content' => $db_write->escape($_POST['link']),
                'id_factcheck' => $id_factcheck,
                'link_identifier' => $link_identifier,
                'md5_link_identifier' => md5($link_identifier),
//					'unique_key' => $_POST['id_post'].md5($link_identifier), //Unique_key(id_post+md5_link_identifier)
                'insert_datetime' => date("Y-m-d H:i:s")
            );
            if (!empty($_POST['snippet'])) {
                $table_fields_values['snippet'] = $db_write->escape(strip_tags($_POST['snippet']));
            }
            $result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_INSERT);
        }
    }

    if ($action == 'modify_link') { // update link
        $sql_check_link_exists = "SELECT * FROM factcheck_content2links WHERE id_post = '" . $db_read->escape($_POST['id_post']) . "' AND id_factcheck = '" . $id_factcheck . "'  AND id_link = '" . $db_read->escape($_POST['id_link']) . "'  ";
        $row_check_link_exists = $db_read->queryRow($sql_check_link_exists);
        if (!empty($row_check_link_exists)) {
            $id_link = $row_check_link_exists['id_link'];

            if (!empty($_POST['snippet'])) {
                $table_fields_values['snippet'] = $db_write->escape($_POST['snippet']);
            }
            if (!empty($table_fields_values)) {
                $result = $db_write->extended->autoExecute('factcheck_content2links', $table_fields_values, MDB2_AUTOQUERY_UPDATE, " id_link = '$id_link' ");
            } else {
                $result = 0;
            }
//				}
        }
    }

    if ($action == 'add_link') {
        $msgAction = 'insertlink';
    } else if ($action == 'modify_link') {
        $msgAction = 'modifylink';
    } elseif ($action == 'disable_link') {
        $msgAction = 'linkDisabled';
    }
    $msg = $msgAction . $separator . $result . $separator . adminFactcheckList($id_factcheck, $_POST['id_post'], $db_read);
}

echo $msg;
