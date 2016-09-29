<?php
require_once(dirname(__FILE__) . "/common/constructor.inc.php");

require_once(dirname(__FILE__) . "/libraries/functions/misc_functions.php");
require_once(dirname(__FILE__) . "/libraries/classes/tree_structure.class.php");
$action = $_REQUEST['action'];
require_once(dirname(__FILE__) . "/libraries/classes/celko_pear.class.php");
// </includes and settings>
if (is_numeric($_GET['edit_id'])) {
    $sql_page = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE id='" . $_GET['edit_id'] . "'";
    $res_page = & $db_write->query($sql_page);
    $row_pages = & $res_page->fetchRow();
}
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
//------------- Begin Get Root Category (name='root_section')
$sql_get_root_section = "SELECT " . $celko->FieldID . " FROM " . $celko->TableName . " WHERE " . $celko->FieldLevel . "='0'";
$res_get_root_section = & $db_write->query($sql_get_root_section);
//errors_handler($res_get_root_section);
if ($row_get_root_section = & $res_get_root_section->fetchRow()) {
    $root_section_id = $row_get_root_section[$celko->FieldID];
}
if (empty($root_section_id)) {
    $othercols = "name='root_section', url_identifier=''";
    $root_section_id = $celko->AddRootNode($othercols);
}
//	echo "<br>root_section_id = $root_section_id";
//------------- End Get Root Category
$rs_nodes = $celko->SelectSubNodesDFS($root_section_id);
$sections_select = "<select name=\"category_id\" size=\"1\">";
$sections_select .= '<option value="1">Root</option>';
if ($rs_nodes) {
    while ($row_nodes = & $rs_nodes->fetchRow(DB_FETCHMODE_ASSOC)) {
        $selected = "";
        if (!is_numeric($_GET['edit_id'])) {
            if ($_GET['category_id'] == $row_nodes["id"]) {
                $selected .= "selected";
            }
        } else {
            if ($row_pages['id'] == $row_nodes['id']) {
                continue;
            }
            if ($row_pages['parent_id'] == $row_nodes["id"]) {
                $selected .= "selected";
            }
        }
        $sections_select .= '<option value="' . $row_nodes["id"] . '" ' . $selected . '>' . str_repeat('&nbsp;', ($row_nodes['NSLevel']) * 3) . trim(stripslashes($row_nodes['name'])) . '</option>';
    }
}
$sections_select .= "</select>";

$rs_nodes = $celko->SelectMainSubNodesDFS($root_section_id);
$sections_select_no_inherit = "<select name=\"category_id_inherited\" size=\"1\">";
if ($rs_nodes) {
    while ($row_nodes = & $rs_nodes->fetchRow(DB_FETCHMODE_ASSOC)) {
        if ($row_pages['id_page_inherited_rights'] == $row_nodes["id"]) {
            $sections_select_no_inherit .= '<option value="' . $row_nodes["id"] . '" ' . selected($_GET['category_id'], $row_nodes["id"]) . ' selected>' . str_repeat('&nbsp;', ($row_nodes['NSLevel'] - 1) * 3) . trim(stripslashes($row_nodes['name'])) . '</option>';
        } else {
            $sections_select_no_inherit .= '<option value="' . $row_nodes["id"] . '" ' . selected($_GET['category_id'], $row_nodes["id"]) . '>' . str_repeat('&nbsp;', ($row_nodes['NSLevel'] - 1) * 3) . trim(stripslashes($row_nodes['name'])) . '</option>';
        }
    }
}

$sections_select_no_inherit .= "</select>";
$celko->EndTransaction();
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case'invalid_extension':
            $error_msg = "Invalid file extension";
            break;

        case'blank_field':
            $error_msg = "The file name field cannot be blank";
            break;

        case'not_corresponding':
            $error_msg = "The pages number doesnt fit with the  file names number";
            break;
    }
}

include_once(dirname(__FILE__) . '/common/header.inc.php');
?>
<?php //echo($_GET['page_url']);   ?>
<!-- Start Main Content -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="td_menu" valign="top">
            <!-- Start Menu -->
            <?php include_once('common/menu.inc.php'); ?>
            <!-- End Menu -->
        </td>
        <td style="background: #FFFFFF;" valign="top">
            <!-- Start Category List -->
            <TABLE id="Table1" cellSpacing="0" cellPadding="0" width="100%" border="0"  >
                <!-- start section title -->
                <TR>
                    <TD align="center"> <DIV class="pageTitle">
                            <span id="lblSection">
                                <?php
                                if (!empty($_GET['edit_id'])) {
                                    echo 'Modify page';
                                } else {
                                    echo 'Add page';
                                }
                                ?>
                                <?php echo str_repeat("&nbsp;", 20); ?>
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_pages_add_modify.php" class="white">Add page</a>
                                <?php echo str_repeat("&nbsp;", 4); ?>|<?php echo str_repeat("&nbsp;", 4); ?>
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_pages.php" class="white">Back to Pages List</a>

                            </span>
                        </DIV></TD>
                </TR>
                <!-- end section title -->
                <TR>
                <TR>
                    <TD align="center">
                        <script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/common.js" ></script>
                        <table class="grid" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <form action="admin_pages_exec.php" method="post">
                                <input type="hidden" name="parent_id" value="<?php echo $_GET['parent_id']; ?>">
                                <input type="hidden" name="edit_id" value="<?php echo $_GET['edit_id']; ?>">
                                <?php
                                if (isset($error_msg)) {
                                    ?>
                                    <tr>
                                        <td colspan="2"><span style="color: #FF0000"><?php echo $error_msg; ?></span></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>Parent:</td>
                                    <td>
                                        <?php
                                        echo $sections_select;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Page name:</td>
                                    <td>
                                        <?php
                                        if (is_numeric($_GET['edit_id'])) {
                                            ?><input type="hidden" name="edit_id" value="<?php echo $_GET['edit_id']; ?>"><?php
                                        }
                                        ?>
                                        <input style="width: 400px;"  class="input"  type="text" value="<?php echo(is_numeric($_GET['edit_id'])) ? $row_pages['name'] : $_SESSION['txt_name']; ?>" name="txt_name"></td>
                                </tr>
                                <tr>
                                    <td>File name:</td>
                                    <td><input style="width: 400px;"  class="input"  type="text" value="<?php echo(is_numeric($_GET['edit_id'])) ? $row_pages['url'] . "?" . $row_pages['get'] : $_SESSION['txt_url']; ?>" name="txt_url"></td>
                                </tr>
                                <tr id="visible_form" <?php if ($row_pages['id_page_inherited_rights']) { ?>style="display: none;"<?php } ?>>
                                    <td>Visible:</td>
                                    <td>Yes
                                        <input type="radio" name="is_visible" <?php echo (($row_pages['is_visible'] == 'Y') || empty($row_pages['is_visible'])) ? 'checked' : ''; ?> value="Y">
                                        No
                                        <input type="radio" name="is_visible" <?php echo($row_pages['is_visible'] == 'N') ? 'checked' : ''; ?> value="N"> </td>
                                </tr>
                                <tr>
                                    <td>Is independent file:</td>
                                    <td>
                                        Yes
                                        <input onClick="displayInheritFrom(2, 'inherit_from');
                                                displayInheritFrom(1, 'visible_form');" type="radio" name="inherits" <?php echo (empty($row_pages['id_page_inherited_rights'])) ? 'checked' : ''; ?> value="Y">
                                        No
                                        <input onClick="displayInheritFrom(1, 'inherit_from');
                                                displayInheritFrom(2, 'visible_form');" type="radio" name="inherits" <?php echo (!empty($row_pages['id_page_inherited_rights'])) ? 'checked' : ''; ?> value="N">
                                    </td>
                                </tr>
                                <tr id="inherit_from" <?php if (empty($row_pages['id_page_inherited_rights'])) { ?>style="display: none;"<?php } ?>>
                                    <td>Inherit rights from:</td>
                                    <td>
                                        <?php
                                        echo $sections_select_no_inherit;
                                        ?>
                                    </td>
                                </tr>
                                <?php unset($_SESSION['admin_module_expanded']); ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="old_order" value="<?php echo $row_pages['NSOrder']; ?>">
                                        <input type="submit" value="Submit" class="input" name="addItem">
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </form>
                        </table>
                    </TD>
                </TR>
                <TR> </TR>
            </TABLE>

        </td>
    </tr>
</table>
<!-- End Main Content -->
<?php
include_once(dirname(__FILE__) . "/common/footer.inc.php");
?>