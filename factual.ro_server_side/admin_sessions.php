<?php
require_once(dirname(__FILE__) . "/common/constructor.inc.php");
require_once(dirname(__FILE__) . "/libraries/functions/date_generate.function.php");
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
                            <form action="" method="get">
                                <tr>
                                    <td class="head" colspan="6"> Username:
                                        <select name="user_id">
                                            <option value="-1">- All users -</option>
                                            <?php
                                            $currentUserId = (isset($_GET['user_id']) && $_GET['user_id'] > 0) ? $_GET['user_id'] : -1;
                                            //selectz userii
                                            $sql_get_users = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users";
                                            $res_get_users = $db_write->query($sql_get_users);
                                            while ($row_get_users = $res_get_users->fetchRow()) {
                                                ?>
                                                <option <?php echo($row_get_users['id_user'] == $currentUserId) ? 'selected' : ''; ?> value="<?php echo $row_get_users['id_user']; ?>">
                                                    <?php echo $row_get_users['username']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select> From:
                                        <?php echo generateDate("From", $dateFrom); ?>
                                        To:
                                        <?php echo generateDate("To", $dateTo); ?>
                                        <input type="submit" value="go" > </td>
                                    <?php
                                    if (!isset($_GET['FromMonth'])) {
                                        $yesterdayTS = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
                                        $yesterday = date('d/m/Y', $yesterdayTS);
                                        $dateFrom = $yesterday;
                                        $dateTo = date('d/m/Y');
                                        $dateFromDb = date('Y-m-d 00:00:00', $yesterdayTS);
                                        $dateToDb = date('Y-m-d 23:59:59');
                                    } else {
                                        $dateFrom = $_GET['FromDay'] . "/" . $_GET['FromMonth'] . "/" . $_GET['FromYear'];
                                        $dateTo = $_GET['ToDay'] . "/" . $_GET['ToMonth'] . "/" . $_GET['ToYear'];
                                        $dateFromDb = $_GET['FromYear'] . "-" . $_GET['FromMonth'] . "-" . $_GET['FromDay'] . " 00:00:00";
                                        $dateToDb = $_GET['ToYear'] . "-" . $_GET['ToMonth'] . "-" . $_GET['ToDay'] . " 23:59:59";
                                    }
                                    ?>
                                </tr>
                            </form>
                            <tr>
                                <td width="16%" class="head">Username</td>
                                <td width="16%" class="head">Login</td>
                                <td width="16%" class="head">Session End</td>
                                <td width="16%" class="head">Session Lasted</td>
                                <td width="16%" class="head">Number of clicks</td>
                                <td width="18%" class="head">Details</td>
                            </tr>
<?php
if ($currentUserId != -1) {
    $sql_audit_logins = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit a INNER JOIN {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users b ON a.audit_user_id=b.id_user WHERE b.id_user='" . $currentUserId . "' AND a.audit_date >= '" . $dateFromDb . "' AND a.audit_date <= '" . $dateToDb . "' GROUP BY audit_session_ident ORDER BY a.audit_session_ident ASC";
} else {
    $sql_audit_logins = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit a INNER JOIN {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_users b ON a.audit_user_id=b.id_user WHERE a.audit_date >= '" . $dateFromDb . "' AND a.audit_date <= '" . $dateToDb . "' GROUP BY a.audit_session_ident  ORDER BY a.audit_session_ident ASC";
}
$res_audit_logins = $db_write->query($sql_audit_logins);
while ($row_audit_logins = $res_audit_logins->fetchRow()) {
    //counting clicks numbers
    $sql_count_clicks = "SELECT COUNT(*) as clicks FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $row_audit_logins['audit_session_ident'] . "'";
    $res_count_clicks = $db_write->query($sql_count_clicks);
    $row_count_clicks = $res_count_clicks->fetchRow();
    //checking session ends
    $sql_check_session_end = "SELECT  audit_date,audit_timestamp FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_audit WHERE audit_session_ident='" . $row_audit_logins['audit_session_ident'] . "' ORDER BY audit_date DESC LIMIT 1";
    $res_check_session_end = $db_write->query($sql_check_session_end);
    $row_check_session_end = $res_check_session_end->fetchRow();
    ?>
                                <tr>
                                    <td width="16%">
                                <?php echo $row_audit_logins['username']; ?>
                                    </td>
                                    <td width="16%">
                                        <?php echo $row_audit_logins['audit_date']; ?>
                                    </td>
                                    <td width="16%">
                                        <?php echo $row_check_session_end['audit_date']; ?>
                                    </td>
                                    <td width="16%"><?php echo round(($row_check_session_end['audit_timestamp'] - $row_audit_logins['audit_session_ident']) / 60, 0); ?>
                                        minutes</td>
                                    <td width="16%">
    <?php echo $row_count_clicks['clicks'] - 1; ?>
                                    </td>
                                    <td width="18%"><a href="admin_session_details.php?session=<?php echo $row_audit_logins['audit_session_ident']; ?>">details</a></td>
                                </tr>
    <?php
}
?>
                        </table>
                        <!-- End continut -->
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