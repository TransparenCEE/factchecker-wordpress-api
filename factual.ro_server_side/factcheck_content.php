<?php
include_once(dirname(__FILE__) . "/common/header.inc.php");

$sql_last_post_id_imported = "SELECT var_value FROM `general_values` WHERE var_name = 'last_post_id_imported' ";
$last_post_id_imported = $db_read->queryOne($sql_last_post_id_imported);

$sql_no_factcheck2import = "SELECT COUNT(ID) FROM {$cfg_db_array['db_wp']}.`wp_posts` WHERE ID > '$last_post_id_imported' AND {$cfg_extra['factcheck_publish_cond']} ";
$sql_no_factcheck2import .= $cfg_extra['factcheck_order2import'];
$no_factcheck2import = $db_read->queryOne($sql_no_factcheck2import);

$sql_no_factchecks2update = "SELECT  COUNT(a.ID)  FROM  factcheck_content as a, {$cfg_db_array['db_wp']}.wp_posts as b WHERE a.ID = b.ID AND b.post_modified != a.post_modified_datetime ";
$no_factchecks2update = $db_read->queryOne($sql_no_factchecks2update);

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
                    <td align="left" style="padding-left:35px;">This function allow the importing and updating of the factcheck items from the primary datatabase. 
                        <br>The pressing of the "import" button copy the data into the plugin database in a format optimal for the delivering of the data via the API.
                    </td>
                </tr>                
                <tr>
                    <td align="center" valign="top">
                        <!--Start content-->						 
                        <div style="margin:10px 0 0 35px;">

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
							
							
                            <fieldset style="width:450px;float:left;margin-left:0px; height: 100px;text-align: center;">
                                <legend style="font-weight: bold">Import new Factchecks</legend>
								<div style="text-align: left;text-align: left;width:450px;"> Import the new facts inserted in WordPress. Imported facts are listed on  <a href="factchecks_list.php">Factchecks list</a> page.</div>
								<br>
                                <?php if ($no_factcheck2import > 0) { ?>
								There are <?php echo $no_factcheck2import; ?> factchecks to import. <br/>
                                    <form action="factcheck_content_exec.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="import">
                                        <input type="hidden" name="last_id" value="<?php echo $last_post_id_imported; ?>">
                                        <div style="width:450px;"><input type="submit" value="Factchecks import (<?php echo $no_factcheck2import; ?> new)"></div>
                                    </form>
                                <?php } else { ?>
								<b>There is no new factcheck to import!</b>
                                <?php } ?>
                            </fieldset>   

                            <fieldset style="width:450px;float:left; height: 100px;text-align: center;">
                                <legend style="font-weight: bold">Update Factchecks</legend>
								<div style="text-align: left;text-align: left;width:450px;">When a fact was modified in WordPress, it should be also updated in Administraitve panel.</div>
								<br>
								<?php if($no_factchecks2update > 0 ) { ?>
								There are <?php echo $no_factchecks2update; ?> factchecks to update. <br/>
                                <form action="factcheck_content_exec.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update">
                                    <div style="width:450px;"><input type="submit" value="Factchecks update"></div>
                                </form>
								<?php } else { ?>
                                <b>There is no factcheck to update!</b>
                                <?php } ?>
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