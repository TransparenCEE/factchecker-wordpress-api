<?php
if (!is_numeric($_GET['session'])) {
    header("Location: admin_sessions.php");
}
require_once(dirname(__FILE__) . "/common/constructor.inc.php");
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
            <TABLE id="Table1" cellSpacing="0" cellPadding="0" width="100%" border="0"  >
                <!-- start section title -->
                <TR>
                    <TD align="center"> <DIV class="pageTitle">
                            <span id="lblSection">
                                <?php
                                $explode = explode("?", basename($_SERVER['REQUEST_URI']));
                                $page = $explode[0];
                                $sql_get_page_name = "SELECT name FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='" . $page . "'";
                                $res_get_page_name = $db_write->query($sql_get_page_name);
                                $row_get_page_name = $res_get_page_name->fetchRow();
                                echo $page_name = stripslashes($row_get_page_name['name']);
                                ?>
                            </span>
                        </DIV></TD>
                </TR>
                <!-- end section title -->
                <TR>
                <TR>
                    <TD align="center">
                        <!-- Start continut -->
                        <table border='0' width="90%" cellspacing='0' cellpadding='0' class="grid">
                            <?php
                            //selecting current session
                            $sql_audit_logins = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit a INNER JOIN " . $cfg_array['table_prefix'] . "_users b ON a.audit_user_id=b.id_user WHERE a.audit_session_ident='" . $_GET['session'] . "'";
                            $res_audit_logins = $db_write->query($sql_audit_logins);
                            $row_audit_logins = $res_audit_logins->fetchRow();
                            //counting clicks numbers
                            $sql_count_clicks = "SELECT COUNT(*) as clicks FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $_GET['session'] . "'";
                            $res_count_clicks = $db_write->query($sql_count_clicks);
                            $row_count_clicks = $res_count_clicks->fetchRow();
                            //checking session end
                            $sql_check_session_end = "SELECT  audit_date,audit_timestamp FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $_GET['session'] . "' ORDER BY audit_date DESC LIMIT 1";
                            $res_check_session_end = $db_write->query($sql_check_session_end);
                            $row_check_session_end = $res_check_session_end->fetchRow();
                            ?>
                            <tr>
                                <td colspan="2">
                                    <p><strong>Username:</strong> <?php echo $row_audit_logins['username']; ?></p>
                                    <p><strong>Login Date:</strong> <?php echo $row_audit_logins['audit_date']; ?></p>
                                    <p><strong>Session End:</strong> <?php echo $row_check_session_end['audit_date']; ?></p>
                                    <p><strong>Number of clicks:</strong> <?php echo $row_count_clicks['clicks']; ?></p>
                                    <p><strong>Session lasted:</strong> <?php echo round(($row_check_session_end['audit_timestamp'] - $row_audit_logins['audit_session_ident']) / 60, 0); ?> minutes</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="section" colspan="2">Session File Statistics</td>
                            </tr>
                            <tr>
                                <td class="head">Filename</td>
                                <td class="head">Times visited</td>
                            </tr>
                            <?php
                            $sql_files = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $_GET['session'] . "' GROUP BY audit_page_name";
                            $res_files = $db_write->query($sql_files);
                            while ($row_files = $res_files->fetchRow()) {
                                $sql_count_file = "SELECT COUNT(*)as numar FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $_GET['session'] . "' AND audit_page_name='" . $row_files['audit_page_name'] . "'";
                                $res_count_file = $db_write->query($sql_count_file);
                                $row_count_file = $res_count_file->fetchRow();
                                ?>
                                <tr>
                                    <td><?php echo $row_files['audit_page_name']; ?></td>
                                    <td><?php echo $row_count_file['numar']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td class="section" colspan="2">Session Log</td>
                            </tr>
                            <tr>
                                <td class="head">Time</td>
                                <td class="head">Filename</td>
                            </tr>
                            <?php
                            $sql_files = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $_GET['session'] . "' ";
                            $res_files = $db_write->query($sql_files);
                            while ($row_files = $res_files->fetchRow()) {
                                ?>
                                <tr>
                                    <td><?php echo $row_files['audit_date']; ?></td>
                                    <td><?php echo $row_files['audit_page_name']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td colspan="2"><input type="button" value="Back" onClick="window.location = 'admin_sessions.php';"></td>
                            </tr>
                        </table>
                        <!-- End continut -->
                    </TD>
                </TR>
            </TABLE>

        </td>
    </tr>
</table>
<!-- End Main Content -->
<?php
include_once(dirname(__FILE__) . "/common/footer.inc.php");
?>