<?php
include_once(dirname(__FILE__) . '/common/constructor.inc.php');
require_once(dirname(__FILE__)."/libraries/classes/PasswordHash.class.php");

if(isset($_POST)) {
	foreach($_POST as $key => $value) {
		$_SESSION['user'][$key] = trim($value); 
		if (is_int(strpos($key, "check_"))) {
            $credentialsArray[] = str_replace("check_", "", $key);
        }
	}
	$_SESSION['user']['password'] = $_POST['password'];
	//	echo '<pre>'; print_r($_SESSION); echo '</pre>'; exit();
	// Base-2 logarithm of the iteration count used for password stretching
	$hash_cost_log2 = 8;
	// Do we require the hashes to be portable to older systems (less secure)?
	$hash_portable = FALSE;
}

if (isset($_POST['addUser']) && !is_numeric($_POST['edit_id'])) {
    //adding user
    if ((strlen($_POST['username']) < 4) || (strlen($_POST['password']) < 4)) {
		$_SESSION['user']['error'] = 'Username and password must contain at least 4 characters!';
        header("Location: admin_users_add_modify.php");
        exit();
    }

    $sql_check_username = "SELECT id_user, is_active FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users WHERE username='" . $db_write->escape(trim($_POST['username'])) . "' ";
    $row_check_username = $db_write->queryRow($sql_check_username);
    if ($row_check_username) { // the username already exists
		if($row_check_username['is_active'] == 'Y') { //the user is active
			$_SESSION['user']['error'] = 'Username already exists!';
			header("Location: admin_users_add_modify.php");
			exit();
		} else { // the user is disabled	
			$_SESSION['user']['error'] = 'Username already exists, it is disabled! If you want to enable/activate use the link top right. ';
			header("Location: admin_users_add_modify.php?edit_id=" . $row_check_username['id_user']);
			exit();
		}
    }
	
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
	$hash = $hasher->HashPassword($_SESSION['user']['password']);
	if (strlen($hash) < 20) {
		$_SESSION['user']['error'] = 'Please fill in the fields again. Error creating the password.';
		header("Location: admin_users_add_modify.php?edit_id=" . $_POST['edit_id']);
		exit();
	}
	unset($hasher);
    $id = $db_write->nextId("{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users");
    $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $table_fields_values = array(
        'id_user' => $id,
        'username' => $_POST['username'],
        'password' => $hash,
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'cookie_user_login' => md5($_POST['password'] . $id . $_POST['username'])
    );
    $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);
	
    $sql_del_cred = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $id . "'";
    $db_write->query($sql_del_cred);
	if(isset($credentialsArray)  && !empty($credentialsArray) ) {
		foreach ($credentialsArray as $value) {
			$table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages";
			$table_fields_values = array(
				'id_user' => $id,
				'page_id' => $value
			);
			$db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);
		}
	}
	
	$_SESSION['user']['success'] = 'The user is created!';
    header("Location: admin_users_add_modify.php?edit_id=" . $id);
	exit();
}

//delete user
if (is_numeric($_GET['del_id'])) {
    //deleting user
    $sql_del_user = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users WHERE id_user='" . $_GET['del_id'] . "'";
    $sql_del_user_2_pages = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $_GET['del_id'] . "'";
    $db_write->query($sql_del_user);
    $db_write->query($sql_del_user_2_pages);
    header("Location: admin_users.php");
	exit();
}

//modify user
if (isset($_POST['addUser']) && is_numeric($_POST['edit_id'])) {    //editing user
    $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $table_fields_values = array(
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    );
	if(!empty($_SESSION['user']['password'])) {
		if ((strlen($_POST['password']) < 4)) {
			$_SESSION['user']['error'] = 'Password must contain at least 4 characters!';
			header("Location: admin_users_add_modify.php?edit_id=" . $_POST['edit_id']);
			exit();
		}	
		$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
		$hash = $hasher->HashPassword($_SESSION['user']['password']);
		if (strlen($hash) < 20) {
			$_SESSION['user']['error'] = 'Please fill in the fields again. Error creating the password.';
			header("Location: admin_users_add_modify.php?edit_id=" . $_POST['edit_id']);
			exit();
		}
		unset($hasher);
		$table_fields_values['password'] = $hash;
		$table_fields_values['cookie_user_login'] = md5($_POST['password'] . $id . $_POST['username']);
	}
    $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_UPDATE, "id_user='" . $_POST['edit_id'] . "'");
	
    $sql_del_cred = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $_POST['edit_id'] . "'";
    $db_write->query($sql_del_cred);	
	if(isset($credentialsArray) && !empty($credentialsArray)) {
		foreach ($credentialsArray as $value) {
			$table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages";
			$table_fields_values = array(
				'id_user' => $_POST['edit_id'],
				'page_id' => $value
			);
			$db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);
		}
	}
	
	$_SESSION['user']['success'] = 'The user is modified!';
    header("Location: admin_users_add_modify.php?edit_id=" . $_POST['edit_id']);
    exit();
}

if (($_GET['action'] == 'activate') && !empty($_GET['id_user']) && is_numeric($_GET['id_user'])) {
    $admin_users_table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $admin_users_table_fields_values_array = array(
        'is_active' => 'Y'
    );
    //echo '<pre>'; print_r($admin_users_table_fields_values_array); echo '</pre>';
    $db_write->extended->autoExecute($admin_users_table_name, $admin_users_table_fields_values_array, MDB2_AUTOQUERY_UPDATE, "id_user='" . $_GET['id_user'] . "'");

    $_SESSION['success'] = 'The user has been successfully activated!';
    header('Location: admin_users_add_modify.php?edit_id=' . $_GET['id_user'] . '&page_no=' . $_GET['page_no']);
    exit();
} elseif (($_GET['action'] == 'inactivate') && !empty($_GET['id_user']) && is_numeric($_GET['id_user'])) {
    $admin_users_table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $admin_users_table_fields_values_array = array(
        'is_active' => 'N'
    );
    //echo '<pre>'; print_r($admin_users_table_fields_values_array); echo '</pre>';
    $db_write->extended->autoExecute($admin_users_table_name, $admin_users_table_fields_values_array, MDB2_AUTOQUERY_UPDATE, "id_user='" . $_GET['id_user'] . "'");

    $_SESSION['success'] = "The user has been successfully inactivated!";
    header('Location: admin_users_add_modify.php?edit_id=' . $_GET['id_user'] . '&page_no=' . $_GET['page_no']);
    exit();
}
?>
