<?php
require_once(dirname(__FILE__) . "/common/constructor.inc.php");

require_once(dirname(__FILE__) . "/libraries/classes/celko_pear.class.php");

require_once(dirname(__FILE__) . "/libraries/classes/celko_pear.class.php");
//begin celko
$celko2 = new CCelkoNastedSet();

$celko2->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
$celko2->FieldID = "id"; //Field name for table ID
$celko2->FieldIDParent = "parent_id"; //Field name for table IDParent
$celko2->FieldLeft = "NSLeft"; //Field name for table nasted set left field
$celko2->FieldRight = "NSRight"; //Field name for table nasted set right field
$celko2->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
$celko2->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
$celko2->FieldOrder = "NSOrder"; //Field name for table nasted set order field
$celko2->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

$celko2->InitializeTransaction();
$celko2->BeginTransaction();

//------------- Begin Get Root Category (name='root_category')
$sql_get_root_category = "SELECT " . $celko2->FieldID . " FROM " . $celko2->TableName . " WHERE " . $celko2->FieldLevel . "='0'";
$res_get_root_category = & $db_write->query($sql_get_root_category);

if ($row_get_root_category = & $res_get_root_category->fetchRow()) {
    $root_category_id = $row_get_root_category[$celko2->FieldID];
}
if (empty($root_category_id)) {//daca nu exista root node, il inserez
    $othercols = "name='root_category', url_identifier='', visible='N'";
    $root_category_id = $celko2->AddRootNode($othercols);
}
//------------- End Get Root Category
unset($rs_nodes);
$rs_nodes2 = & $celko2->SelectSubNodesNotInheritedNotInvisible(1);
$celko2->EndTransaction();


if (is_numeric($_GET['edit_id'])) {
    $sql_edit = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users WHERE id_user='" . $_GET['edit_id'] . "'";
    $res_edit = $db_write->query($sql_edit);
    $row_edit = $res_edit->fetchRow();
}

$deal_rights_array = array();
include_once(dirname(__FILE__) . '/common/header.inc.php');
?>
<!-- Start Main Content -->
<script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/common.js" ></script>

<script type="text/javascript">
    function trim(str)
    {
        return str.replace(/^\s*|\s*$/g, "");
    }

// validates that the field value string has one or more characters in it
    function isEmpty(elem) {
        var str = elem.value;
        str = trim(str);
        var re = /.+/;
        if (str.match(re)) {
            return false;
        } else {
            return true;
        }
    }


// validates that the entry is formatted as an email address
    function isEMailAddr(elem) {
        var str = elem.value;
        str = trim(str);
        var re = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        if (!str.match(re)) {
            return false;
        } else {
            return true;
        }
    }

    function validateUserManagForm(frm) {
        //validate the email
        //*********************************************
        if (isEmpty(frm.email)) {
            alert('Please fill in user email address!');
            frm.email.focus();
            return false;
        } //end if

        if (!isEMailAddr(frm.email)) {
            alert('Please fill an valid email address!');
            frm.email.focus();
            return false;
        } //end if

        //validate the password
        //*********************************************
        if (isEmpty(frm.first_name)) {
            alert('Please fill in user first name!');
            frm.first_name.focus();
            return false;
        } //end if

        //validate the password
        //*********************************************
        if (isEmpty(frm.last_name)) {
            alert('Please fill in user last name!');
            frm.last_name.focus();
            return false;
        } //end if

		<?php if(!isset($row_edit['id_user'])) { ?>

        //validate the username
        //*********************************************
        if (isEmpty(frm.username)) {
            alert('Please fill in the username!');
            frm.username.focus();
            return false;
        } //end if
		
        //validate the password
        //*********************************************
        if (isEmpty(frm.password)) {
            alert('Please fill in the password!');
            frm.password.focus();
            return false;
        } //end if
        //*********************************************
        return true;
		
		<?php } ?>
    }
//end function --> validateLoginForm()
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="td_menu" valign="top">
            <!-- Start Menu -->
            <?php include_once('common/menu.inc.php'); ?>
            <!-- End Menu -->
        </td>
        <td style="background: #FFFFFF;" valign="top">
            <!-- Start Category List -->
            <TABLE id="Table1" cellSpacing="0" cellPadding="0" width="100%" border="0" >
                <!-- start section title -->
                <TR>
                    <TD align="center"> <DIV class="pageTitle">
                            <span id="lblSection">
                                <?php
                                if (!empty($_GET['edit_id'])) {
                                    echo 'Modify user';
                                } else {
                                    echo 'Add user';
                                }
                                ?>
                                <?php echo str_repeat("&nbsp;", 20); ?>
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_users_add_modify.php" class="white">Add user</a>
                                <?php echo str_repeat("&nbsp;", 4); ?>|<?php echo str_repeat("&nbsp;", 4); ?>
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_users.php" class="white">Back to Users List</a>
                            </span>
                        </DIV></TD>
                </TR>
                <!-- end section title -->
                <TR>
                <TR>
                    <TD align="center">
                        <?php
                        if (!empty($_SESSION['user']['error'])) {
                            ?>
                            <div class="message_box"><div class="error_message"><?php echo $_SESSION['user']['error']; ?></div></div>
                            <?php
                        }
                        ?>
                        <?php
                        if (!empty($_SESSION['user']['success'])) {
                            ?>
                            <div class="message_box"><div class="success_message"><?php echo $_SESSION['user']['success']; ?></div></div>
                            <?php
                        }
                        ?>
                        <table border='0' width="100%" cellspacing='0' cellpadding='0' class="grid">
                            <form action="admin_users_exec.php" method="post" onsubmit="return validateUserManagForm(this);">
                                <tr>
                                    <td colspan="2" align="right">
                                        <?php if (!empty($_GET['edit_id'])) { ?>
                                            <?php if ($row_edit['is_active'] == 'Y') { ?>
                                                <img src="<?php echo $cfg_array['site_root_path']; ?>design/images/ico_suspend_contract.gif" width="20" height="20"><a href="admin_users_exec.php?id_user=<?php echo $_GET['edit_id']; ?>&action=inactivate&page_no=<?php echo $page_no; ?>" onclick="return confirm('Are you sure you want to inactivate this user?');"><b>disable the user (he is active)</b></a>
                                            <?php } else { ?>
                                                <img src="<?php echo $cfg_array['site_root_path']; ?>design/images/ico_activate_contract.gif" width="20" height="20"><a href="admin_users_exec.php?id_user=<?php echo $_GET['edit_id']; ?>&action=activate&page_no=<?php echo $page_no; ?>" onclick="return confirm('Are you sure you want to activate this user?');"><b>activate the user (he is disabled)</b></a>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Username:</td>
									<td>
										<?php if(!isset($row_edit['id_user'])) { ?>
										
										<input type="text" class="input" value="<?php echo $_SESSION['user']['username']; ?>" name="username" >
										
										<?php } else { ?>
										
										<b> <?php echo $row_edit['username']; ?>
										
										<?php } ?>
									</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" class="input" value="<?php echo( is_numeric($_GET['edit_id']) ) ? $row_edit['email'] : $_SESSION['user']['email']; ?>" name="email"></td>
                                </tr>
                                <tr>
                                    <td>First Name:</td>
                                    <td><input type="text" class="input" value="<?php echo( is_numeric($_GET['edit_id']) ) ? $row_edit['first_name'] : $_SESSION['user']['first_name']; ?>" name="first_name"></td>
                                </tr>
                                <tr>
                                    <td>Last Name:</td>
                                    <td><input type="text" class="input" value="<?php echo( is_numeric($_GET['edit_id']) ) ? $row_edit['last_name'] : $_SESSION['user']['last_name']; ?>" name="last_name"></td>
                                </tr>
                                <tr>
									<td><?php if(is_numeric($_GET['edit_id'])) { ?> Add new password <?php } else { ?>  Password: <?php } ?></td>
                                    <td><input class="input" type="password" value="" name="password"></td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <?php
                                        if (is_numeric($_GET['edit_id'])) {
                                            ?><input type="hidden" name="edit_id" value="<?php echo $_GET['edit_id'] ?>"><?php
                                        }
                                        if ($user_info['id_user'] == $_GET['edit_id']) {
                                            ?><input type="hidden" name="locked" value="true"><?php
                                        }
                                        ?>
                                        <fieldset>
                                            <legend style="margin: 5px;">Credentials</legend>
                                            <table class="tree" border='0' cellspacing='0' cellpadding='0'>
                                                <?php
                                                $categories_tree2 = array();
                                                $categories_tree2[] = array("id" => $root_category_id, "text" => "<strong>pages</strong>", "category_parent_id" => -1);
                                                $rs_nodes = $celko2->SelectSubNodesNotInherited($root_category_id); //iau numai categoriile de nivel 2(adica subcateg. root-ului)
                                                if ($rs_nodes2) {
                                                    while ($row_nodes2 = & $rs_nodes2->fetchRow(DB_FETCHMODE_ASSOC)) {
                                                        $categories_tree2[] = array("id" => $row_nodes2['id'], "text" => trim(stripslashes($row_nodes2['name'])), "category_parent_id" => $row_nodes2['parent_id']);
                                                    }
                                                }
                                                //building the tree
                                                //var_dump($categories_tree2);
                                                $tree2 = new tree_structure($categories_tree2, "id", "text", "category_parent_id", "design/images");

                                                // transform the linear tab to the tab ordered in tree order
                                                $tree2->transform($tree2->get_idroot());
                                                ?>
                                                <table class="tree" border='0' width="100%" cellspacing='0' cellpadding='0'>
                                                    <?php
                                                    for ($y = 0; $y < $tree2->height(); $y = $tree2->get_next_line_tree($y)) {
                                                        ?>
                                                        <tr>
                                                            <td height="16">
                                                                <?php
                                                                // the $tree2_static_part part is the static part of tree
                                                                // the $current_symbol part is the last part of the tree, the part which looks like + or - in windows looking tree
                                                                // the $node_text part is the text of the node
                                                                // the $current_category_id part is the id of the node
                                                                list($tree2_static_part, $current_symbol, $node_text, $current_category_id) = $tree2->get_line_display($y);
                                                                echo $tree2_static_part;
                                                                if ($tree2->tree_tab[$y]["symbol"] == "plus") { // if node is "+" => expand it
                                                                    echo "$current_symbol";
                                                                } else {
                                                                    if ($tree2->tree_tab[$y]["symbol"] == "moins") // if node is "-" => expand to father
                                                                        echo "$current_symbol";
                                                                    else // else the node have static tree
                                                                        echo $current_symbol;
                                                                }
                                                                echo $node_text;
                                                                //begin make down/up links
                                                                $current_nb_brothers = count($tree2->get_brothers($current_category_id));
                                                                $current_nb_children = count($tree2->get_list_childs($current_category_id));
                                                                $father = end($tree2->get_fathers($current_category_id));
                                                                //echo "<br>current_category_id = $current_category_id";
                                                                global $res;
                                                                $res = array();
                                                                $children = $tree2->get_list_childs_DFS($current_category_id);
                                                                //print_r($children);
                                                                foreach ($children as $key => $child) {
                                                                    if ($current_category_id == $child) {
                                                                        if (!empty($children[$key + 1])) {
                                                                            $down_brother = $children[$key + 1];
                                                                        } else {
                                                                            $down_brother = '';
                                                                        }
                                                                        if (!empty($children[$key - 1])) {
                                                                            $up_brother = $children[$key - 1];
                                                                        } else {
                                                                            $up_brother = '';
                                                                        }
                                                                    }
                                                                }
                                                                $functionArgs = "";
                                                                $functionArgs .= $current_category_id;
                                                                if (count($children) > 0)
                                                                    $functionArgs .= ",";
                                                                foreach ($children as $value) {
                                                                    if ($value != end($children))
                                                                        $functionArgs .= $value . ",";
                                                                    else
                                                                        $functionArgs .= $value;
                                                                }
                                                                //echo '&nbsp;&nbsp;current_category_id='.$current_category_id.' down_brother='.$down_brother.' up_brother='.$up_brother.' ';
                                                                //checking if its checked (count = 1)
                                                                $sql_check_credentials = "SELECT COUNT(*)as checked FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE page_id='" . $current_category_id . "' AND id_user='" . $_GET['edit_id'] . "'";
                                                                //echo $sql_check_credentials;
                                                                $res_check_credentials = $db_write->query($sql_check_credentials);
                                                                $row_check_credentials = $res_check_credentials->fetchRow();
                                                                //echo '<br>checked = '.$row_check_credentials['checked'];
                                                                //checking if its father is checked (count=1)
                                                                $sql_check_father_credentials = "SELECT COUNT(*)as checked_father FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users_2_pages WHERE page_id='" . $father . "' AND id_user='" . $_GET['edit_id'] . "'";
                                                                $res_check_father_credentials = $db_write->query($sql_check_father_credentials);
                                                                $row_check_father_credentials = $res_check_father_credentials->fetchRow();
                                                                //checking if it deals with rights
                                                                if ($user_info['id_user'] == $_GET['edit_id']) {
                                                                    $sql_deals_rights = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE id='" . $current_category_id . "'";
                                                                    $res_deals_rights = $db_write->query($sql_deals_rights);
                                                                    $row_deals_rights = $res_deals_rights->fetchRow();
                                                                    if ($row_deals_rights['deals_with_rights'] == 'Y') {
                                                                        $deal_rights_array[] = $row_deals_rights['id'];
                                                                    }
                                                                }
                                                                if (($current_category_id > 0) && ($current_category_id != $root_category_id)) {
                                                                    if (($user_info['id_user'] == $_GET['edit_id'] && $row_deals_rights['deals_with_rights'] == 'Y')) {
                                                                        /* ?>
                                                                          <input type="hidden" id="check_<?php     echo $current_category_id ;?>" name="check_<?php     echo $current_category_id ;?>" value="on">
                                                                          <?php */
                                                                    } else {
                                                                        ?><input onClick="enableCheckbox(<?php echo $functionArgs; ?>);" type="checkbox" <?php echo ( ($row_check_father_credentials['checked_father'] == 0 && $father != '1')) ? 'disabled' : ''; ?> <?php echo ($row_check_credentials['checked'] != 0) ? 'checked' : ''; ?> id="check_<?php echo $current_category_id; ?>" name="check_<?php echo $current_category_id; ?>"><?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        unset($num_articles);
                                                    }
                                                    ?>
                                                </table>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center"><input type="hidden" name="dealers" value="<?php foreach ($deal_rights_array as $value) {
                                                        echo $value . ",";
                                                    } ?>"><input type="submit" name="addUser" class="input" value="Submit"></td>
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
unset($_SESSION['user']);
include_once(dirname(__FILE__) . "/common/footer.inc.php");
?>