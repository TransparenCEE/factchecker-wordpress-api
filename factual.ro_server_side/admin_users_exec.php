<?php

include_once(dirname(__FILE__) . '/common/constructor.inc.php');

if (isset($_POST['addUser']) && !is_numeric($_POST['edit_id'])) {
    //adding user
    if ((strlen($_POST['username']) < 4) || (strlen($_POST['password']) < 4)) {
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['password'] = $_POST['password'];
        ;
        header("Location: admin_users_add_modify.php?error=not_filled");
        exit();
    }
    $sql_check_username = "SELECT id_user FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users WHERE username='" . $db_write->escape(trim($_POST['username'])) . "'";
    $res_check_username = $db_write->query($sql_check_username);
    if ($res_check_username->numRows() > 0) {
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['password'] = $_POST['password'];
        header("Location: admin_users_add_modify.php?error=user_exists");
        exit();
    }
    foreach ($_POST as $key => $value) {
        if (is_int(strpos($key, "check_"))) {
            $credentialsArray[] = str_replace("check_", "", $key);
        }
    }

    $id = $db_write->nextId("{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users");
    $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $table_fields_values = array(
        'id_user' => $id,
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'cookie_user_login' => md5($_POST['password'] . $id . $_POST['username'])
    );
    $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);

    $sql_del_cred = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $id . "'";
    $db_write->query($sql_del_cred);

    foreach ($credentialsArray as $value) {
        $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages";
        $table_fields_values = array(
            'id_user' => $id,
            'page_id' => $value
        );
        $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);
    }

    header("Location: admin_users_add_modify.php?edit_id=" . $id);
}
if (is_numeric($_GET['del_id'])) {
    //deleting user
    $sql_del_user = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users WHERE id_user='" . $_GET['del_id'] . "'";
    $sql_del_user_2_pages = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $_GET['del_id'] . "'";
    $db_write->query($sql_del_user);
    $db_write->query($sql_del_user_2_pages);
    header("Location: admin_users.php");
}
if (isset($_POST['addUser']) && is_numeric($_POST['edit_id'])) {
    //editing user
    if ((strlen($_POST['username']) < 4) || (strlen($_POST['password']) < 4)) {
        header("Location: admin_users_add_modify.php?error=not_filled&edit_id=" . $_POST['edit_id']);
        exit();
    }
    $credentialsArray = array();
    foreach ($_POST as $key => $value) {
        if (is_int(strpos($key, "check_"))) {
            $credentialsArray[] = str_replace("check_", "", $key);
        }
    }
    //echo"<pre>"; var_dump($credentialsArray); echo "</pre>";die();
    $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
    $table_fields_values = array(
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'cookie_user_login' => md5($_POST['password'] . $id . $_POST['username'])
    );
    $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_UPDATE, "id_user='" . $_POST['edit_id'] . "'");
    $sql_del_cred = "DELETE FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE id_user='" . $_POST['edit_id'] . "'";
    $db_write->query($sql_del_cred);

    foreach ($credentialsArray as $value) {
        $table_name = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages";
        $table_fields_values = array(
            'id_user' => $_POST['edit_id'],
            'page_id' => $value
        );
        $db_write->extended->autoExecute($table_name, $table_fields_values, MDB2_AUTOQUERY_INSERT);
    }
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
