<?php
require_once(dirname(__FILE__) . "/common/constructor.inc.php");

$sql_get_users = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users where is_active='Y'";
$sql_get_users .= " ORDER BY admin_order DESC,first_name, last_name ";
//echo '<br>'.$sql_get_users;
$res_get_users = & $db_write->query($sql_get_users);
include_once(dirname(__FILE__) . '/common/header.inc.php');
?>
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
            <TABLE id="Table1" cellSpacing="0" cellPadding="0" width="100%" border="0">
                <!-- start section title -->
                <TR>
                    <TD  align="center"> <DIV class="pageTitle">
                            <span id="lblSection">
                                Admin users
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_users_add_modify.php" class="white">Add user</a>
                            </span>
                        </DIV></TD>
                </TR>
                <!-- end section title -->
                <tr>
                    <td align="left" style="padding-left:35px;">Allows the admin user management, by adding new users, assigning user rights, defining password.</td>
                </tr>
                <TR>
                    <TD align="center">
                        <table border='0' width="100%" cellspacing='0' cellpadding='0' class="grid">
                            <tr>
                                <th class="head">Username</th>
                                <th class="head">Name</th>
                                <th class="head">Email</th>
                                <th class="head">Is active?</th>
                                <th class="head" align="center">Actions</th>
                            </tr>
                            <?php
                            while ($row_get_users = $res_get_users->FetchRow()) {
                                ?>
                                <tr>
                                    <td><?php echo $row_get_users['username']; ?>&nbsp;</td>
                                    <td><?php echo $row_get_users['first_name'] . ' ' . $row_get_users['last_name']; ?>&nbsp;</td>
                                    <td><?php echo $row_get_users['email']; ?>&nbsp;</td>
                                    <td width="20%" align="center">
                                        <?php
                                        if ($row_get_users['is_active'] == 'Y') {
                                            ?><span class="activeItem">active</span><?php
                                        } else {
                                            ?><span class="inactiveItem">inactive</span><?php
                                        }
                                        ?>
                                    </td>
                                    <td width="20%" align="center">
                                        <a href="admin_users_add_modify.php?edit_id=<?php echo $row_get_users['id_user']; ?>">edit</a>
                                        &nbsp;&nbsp;&nbsp;
                                        <a onClick="return confirm('Are you sure you want to delete this?');" href="admin_users_exec.php?del_id=<?php echo $row_get_users['id_user']; ?>">delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
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