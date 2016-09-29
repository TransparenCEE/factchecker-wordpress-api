<?php
include_once(dirname(__FILE__) . "/common/header.inc.php");

$sql_last_post_id_imported = "SELECT var_value FROM `general_values` WHERE var_name = 'last_post_id_imported' ";
$last_post_id_imported = $db_read->queryOne($sql_last_post_id_imported);

$sql_no_factcheck2import = "SELECT COUNT(ID) FROM {$cfg_db_array['db_wp']}.`wp_posts` WHERE ID > '$last_post_id_imported' AND {$cfg_extra['factcheck_publish_cond']} ";
$sql_no_factcheck2import .= $cfg_extra['factcheck_order2import'];
$no_factcheck2import = $db_read->queryOne($sql_no_factcheck2import);
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
            <table id="table1" cellSpacing="0" cellPadding="0" width="100%" border="0">
                <tr>
                    <td align="center">
                        <div class="pageTitle">
                            Import Factchecks     														
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <!--Start content-->						 
                        <div style="margin:30px 0 0 35px;">

                            <?php
                            if ($_GET['msg'] && !empty($_GET['msg'])) {
                                list($msg_type, $msg_value) = explode('-', $_GET['msg']);
                                ?>

                                <div class="message_box">
                                    <div class="<?php if ($msg_value == 0) { ?> error_message <?php } else { ?> success_message <?php } ?>"><?php echo $msg_value . ' factchecks ' . ($msg_type == 'import' ? 'imported' : 'updated'); ?></div>
                                </div>

                                <?php
                            }
                            ?>

                            <fieldset style="width:450px;float:left;margin-left:0px;">
                                <legend>Import Last Factchecks</legend>
                                <?php if ($no_factcheck2import > 0) { ?>

                                    <form action="factcheck_content_exec.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="import">
                                        <input type="hidden" name="last_id" value="<?php echo $last_post_id_imported; ?>">
                                        <table border='0' class="list" style="width:450px;float:left;">
                                            <tr>
                                                <td><input type="submit" value="Factchecks import (<?php echo $no_factcheck2import; ?> new)"></td>
                                            </tr>	
                                        </table>
                                    </form>

                                <?php } else { ?>

                                    No factchecked to import!

                                <?php } ?>

                            </fieldset>   

                            <fieldset style="width:450px;float:left;">
                                <legend>Update Factchecks</legend>
                                <form action="factcheck_content_exec.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update">
                                    <table border='0' class="list" style="width:450px;float:left;">
                                        <tr>
                                            <td><input type="submit" value="Factchecks update"></td>
                                        </tr>	
                                    </table>
                                </form>
                            </fieldset>      

                        </div>
                        <!--End content-->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- End Main Content -->
<?php
include_once(dirname(__FILE__) . "/common/footer.inc.php");
?>                         