<?php
include_once(dirname(__FILE__) . "/common/header.inc.php");
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
                            <?php
                            $page = $_GET['page_url'];
                            $sql_get_page_name = "SELECT name FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='" . $page . "'";
                            $res_get_page_name = $db_write->query($sql_get_page_name);
                            if ($res_get_page_name->numRows() == 0) {
                                $sql_get_page_name = "SELECT name FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='admin_blank.php?page_url=" . $page . "'";
                                $res_get_page_name = $db_write->query($sql_get_page_name);
                            }
                            $row_get_page_name = $res_get_page_name->fetchRow();
                            echo $page_name = stripslashes($row_get_page_name['name']);
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
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