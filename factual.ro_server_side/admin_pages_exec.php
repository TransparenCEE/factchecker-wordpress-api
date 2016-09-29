<?php

require_once(dirname(__FILE__) . "/common/constructor.inc.php");

require_once(dirname(__FILE__) . "/libraries/classes/tree_structure.class.php");
require_once(dirname(__FILE__) . "/libraries/functions/url_generation.function.php");
require_once(dirname(__FILE__) . "/libraries/classes/celko_pear.class.php");

$action = $_REQUEST['action'];
if (isset($_POST['addItem']) && !is_numeric($_POST['edit_id'])) {

    if (empty($_POST['txt_url']) /* && empty($_POST['txt_name']) */) {
        session_start();
        $_SESSION['txt_name'] = $_POST['txt_name'];
        $_SESSION['txt_url'] = $_POST['txt_url'];
        header("Location: admin_pages_add_modify.php?error=blank_field&category_id=" . $_POST['category_id']);
        exit();
    }

    function createName($file) {
        $file = str_replace("_", " ", ucfirst($file));
        return $file;
    }

    //begin celko
    $celko = new CCelkoNastedSet();
    $celko->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
    $celko->FieldID = "id"; //Field name for table ID
    $celko->FieldIDParent = "parent_id"; //Field name for table IDParent
    $celko->FieldLeft = "NSLeft"; //Field name for table nasted set left field
    $celko->FieldRight = "NSRight"; //Field name for table nasted set right field
    $celko->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
    $celko->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
    $celko->FieldOrder = "NSOrder"; //Field name for table nasted set order field
    $celko->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

    $celko->InitializeTransaction();
    $celko->BeginTransaction();
    $_POST['txt_url'] = trim($_POST['txt_url']);
    $_POST['txt_name'] = trim($_POST['txt_name']);
    $nameLength = strlen($_POST['txt_name']);
    $fileLength = strlen($_POST['txt_url']);
    if (substr($_POST['txt_name'], $nameLength - 1, $nameLength) == ";") {
        $_POST['txt_name'] = substr($_POST['txt_name'], 0, $nameLength - 1);
    }
    if (substr($_POST['txt_url'], $fileLength - 1, $fileLength) == ";") {
        $_POST['txt_url'] = substr($_POST['txt_url'], 0, $fileLength - 1);
    }
    //die($_POST['txt_name']."---".$_POST['txt_url']);
    $explodedPages = explode(";", $_POST['txt_name']);
    $explodedFiles = explode(";", $_POST['txt_url']);
    if (count($explodedPages) != count($explodedFiles)) {
        session_start();
        $_SESSION['txt_name'] = $_POST['txt_name'];
        $_SESSION['txt_url'] = $_POST['txt_url'];
        header("Location: admin_pages_add_modify.php?error=not_corresponding&category_id=" . $_POST['category_id']);
        exit();
    }
    //print_r($explodedPages);
    foreach ($explodedPages as $key => $value) {
        $file = $explodedFiles[$key];
        $explode = explode(".", $file);
        $firstPart = $explode[0];
        $lastPart = end($explode);
        if (strpos($lastPart, '?')) {
            $explode2 = explode("?", $lastPart);
            $extension = $explode2[0];
            $get = end($explode2);
            $file = $firstPart . "." . $extension;
        } else {
            $file = $firstPart . "." . $lastPart;
            $extension = $lastPart;
            $get = "";
        }

        if ($extension == "php") {
            if (!is_file($file)) {
                $content = "";
                //$content = file_get_contents('admin_default_template.php');
                $cfgfile = str_replace(".php", "", $file);
                $fp = fopen($cfg_array['site_phisic_path'] . "config/langs/en/" . $cfgfile . ".ini.php", "w+");
                chmod($cfg_array['site_phisic_path'] . "config/langs/en/" . $cfgfile . ".ini.php", 0777);
                fwrite($fp, ";  <?php  exit('Unauthorised web access to *.ini file.\\n\\nPermission Denied.\\nTerminated.'); ?>");
                fclose($fp);
                $handle = fopen($file, 'a+');
                chmod($file, 0777);
                fwrite($handle, $content);
            }
        } else if ($extension != "phpx") {
            die($extension);
            session_start();
            $_SESSION['txt_name'] = $_POST['txt_name'];
            $_SESSION['txt_url'] = $_POST['txt_url'];
            header("Location: admin_pages_add_modify.php?error=invalid_extension&category_id=" . $_POST['category_id']);
            exit();
        }
        $pageName = (!empty($value)) ? $value : createName($firstPart);
        $filename = (!empty($value)) ? $value : createName($firstPart);

        $inherits = ($_POST['inherits'] == "Y") ? "0" : $_POST['category_id_inherited'];
        $othercols = "`get`='" . $get . "',name='" . $db_write->escape(trim($pageName)) . "', url='" . $db_write->escape(trim($file)) . "', id_zone='1', is_visible='" . $_POST['is_visible'] . "', id_page_inherited_rights='" . $inherits . "'";
        $res_category_id = $celko->AddNode($_POST['category_id'], $othercols);
        //echo "<br>".$_POST['category_id']."<br>".$othercols."<br>".$res_category_id;
    }
    $celko->EndTransaction();

    header("Location: admin_pages.php");
    exit();
}
if (isset($_POST['addItem']) && is_numeric($_POST['edit_id'])) {
    $celko = new CCelkoNastedSet();
    $celko->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
    $celko->FieldID = "id"; //Field name for table ID
    $celko->FieldIDParent = "parent_id"; //Field name for table IDParent
    $celko->FieldLeft = "NSLeft"; //Field name for table nasted set left field
    $celko->FieldRight = "NSRight"; //Field name for table nasted set right field
    $celko->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
    $celko->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
    $celko->FieldOrder = "NSOrder"; //Field name for table nasted set order field
    $celko->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

    $celko->InitializeTransaction();
    $celko->BeginTransaction();
    $inherits = ($_POST['inherits'] == "Y") ? "0" : $_POST['category_id_inherited'];
    $explode = explode("?", $_POST['txt_url']);
    $file = $explode[0];
    $get = $explode[1];
    $othercols = "name='" . $db_write->escape(trim($_POST['txt_name'])) . "', `get`='" . $get . "', url='" . $db_write->escape(trim($file)) . "', is_visible='" . $_POST['is_visible'] . "', id_page_inherited_rights='" . $inherits . "'";
    //echo $_POST['edit_id'].' '.$_POST['category_id'];
    $celko->MoveNode($_POST['edit_id'], $_POST['category_id'], $othercols);
    $celko->EndTransaction();

    //$db_write->query("UPDATE {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_pages SET NSOrder='" .$_POST['old_order']. "' WHERE id='" .$_POST['edit_id']. "'");
    header("Location: admin_pages.php");
    exit();
}
if (is_numeric($_GET['del_id'])) {
    $celko = new CCelkoNastedSet();

    $celko->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
    $celko->FieldID = "id"; //Field name for table ID
    $celko->FieldIDParent = "parent_id"; //Field name for table IDParent
    $celko->FieldLeft = "NSLeft"; //Field name for table nasted set left field
    $celko->FieldRight = "NSRight"; //Field name for table nasted set right field
    $celko->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
    $celko->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
    $celko->FieldOrder = "NSOrder"; //Field name for table nasted set order field
    $celko->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

    $celko->InitializeTransaction();
    $celko->BeginTransaction();
    if ($_POST['is_visible'] == 'N') {
        $rs_nodes = $celko->SelectSubNodesDFS($_POST['edit_id']);
        if ($rs_nodes) {
            while ($row_nodes = & $rs_nodes->fetchRow(DB_FETCHMODE_ASSOC)) {
                //echo "<br>".$row_nodes['id'];
                $db_write->exec("UPDATE {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages SET is_visible='N' WHERE id='" . $row_nodes['id'] . "'");
            }
        }
    }
    $celko->DeleteNode($_GET['del_id']);
    $celko->EndTransaction();

    header("Location: admin_pages.php?target_id=" . $_GET['target_id']);
    exit();
}
if (($_GET['action'] == 'move') && !empty($_GET['id_page1']) && is_numeric($_GET['id_page1']) && !empty($_GET['id_page2']) && is_numeric($_GET['id_page2'])) {
    $celko = new CCelkoNastedSet();

    $celko->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
    $celko->FieldID = "id"; //Field name for table ID
    $celko->FieldIDParent = "parent_id"; //Field name for table IDParent
    $celko->FieldLeft = "NSLeft"; //Field name for table nasted set left field
    $celko->FieldRight = "NSRight"; //Field name for table nasted set right field
    $celko->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
    $celko->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
    $celko->FieldOrder = "NSOrder"; //Field name for table nasted set order field
    $celko->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions


    $sql_get_items_order = "SELECT $celko->FieldOrder FROM {$celko->TableName} WHERE {$celko->FieldID}='" . $_GET['id_page1'] . "'";
    $res_get_items_order = $db_write->query($sql_get_items_order);
    if ($row_get_items_order = & $res_get_items_order->fetchRow()) {
        $items_order = $row_get_items_order["{$celko->FieldOrder}"];
    }
    $sql_get_up_items_order = "SELECT $celko->FieldOrder FROM {$celko->TableName} WHERE {$celko->FieldID}='" . $_GET['id_page2'] . "'";
    $res_get_up_items_order = & $db_write->query($sql_get_up_items_order);
    if ($row_get_up_items_order = & $res_get_up_items_order->fetchRow()) {
        $up_items_order = $row_get_up_items_order["{$celko->FieldOrder}"];
    }
    //echo 'sss='.$items_order.' '.$up_items_order;
    if (!empty($items_order) && !empty($up_items_order)) {
        $sql_update_items_order = "UPDATE {$celko->TableName} SET {$celko->FieldOrder}='$up_items_order' WHERE {$celko->FieldID}='" . $_GET['id_page1'] . "'";
        $db_write->exec($sql_update_items_order);
        //echo '<br>'.$sql_update_items_order;
        $sql_update_up_items_order = "UPDATE {$celko->TableName} SET {$celko->FieldOrder}='$items_order' WHERE {$celko->FieldID}='" . $_GET['id_page2'] . "'";
        $db_write->exec($sql_update_up_items_order);
        //echo '<br>'.$sql_update_up_items_order;
    }

    header("Location: admin_pages.php?target_id=" . $_GET['target_id']);
    exit();
}
?>