<?php
session_start();
date_default_timezone_set("Europe/Bucharest");

include_once(dirname(__FILE__) . "/config.inc.php");

$cfg_extra = [
    'factcheck_publish_cond' => ' `post_status` = \'publish\' AND `post_type` = \'declaratii\' '
    , 'factcheck_order2import' => ' ORDER BY ID ASC '
];

include_once(dirname(__FILE__) . "/../libraries/functions/set_magic_quotes_off.function.php");
set_magic_quotes_off();

include_once(dirname(__FILE__) . "/../libraries/functions/errors_handler.function.php");
set_error_handler("php_errors_handler");

ini_set('session.gc_maxlifetime', 2 * 60 * 60);
ini_set('error_reporting', E_ALL ^ E_NOTICE); //Report all errors except E_NOTICE
//pear path
$a = ini_get('include_path');
$b = $cfg_array['phisic_pear_path'];
$c = $a . ':' . $b; //; for localhost
$c = $b;
ini_set('include_path', $c);

include_once(dirname(__FILE__) . "/../libraries/functions/database_connect.function.php");
//$db_write = database_connect($cfg_db_array['db_write_user'], $cfg_db_array['db_write_passwd'], $cfg_db_array['db_write_host'], $cfg_db_array['db_main']);
$db_write = database_connect($cfg_db_array['db_user'], $cfg_db_array['db_passwd'], $cfg_db_array['db_host'], $cfg_db_array['db_database']);
$db_write->query("SET NAMES 'utf8'");
$db_main = $db_read = $db_write;

if (($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path'] . 'login.php') &&
        ($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path'] . 'login_exec.php') &&
        ($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path_front'] . 'index.php') &&
        ($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path'] . 'setscroll.php') && ($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path'] . 'hide_menu.php') && ($_SERVER['SCRIPT_NAME'] != $cfg_array['site_root_path'] . 'logout.php')) {
    include_once(dirname(__FILE__) . '/../libraries/functions/authentication.function.php');
    $user_info = admin_autentification($_SESSION[$cfg_array['cookie_user_login']]);
}

$admin_labels_file = $_SERVER['PHP_SELF'];
if (substr($admin_labels_file, 0, strlen($cfg_array['site_root_path'])) == $cfg_array['site_root_path']) {
    $admin_labels_file = substr($admin_labels_file, strlen($cfg_array['site_root_path']));
}

if (substr($admin_labels_file, -strlen('.php')) == '.php') {
    $admin_labels_file = substr($admin_labels_file, 0, -strlen('.php'));
}
$cfg_admin_labels_array = array();
unset($admin_labels_file);


