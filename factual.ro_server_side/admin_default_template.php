<?php
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
        <td width="200" class="td_menu" valign="top">
            <!-- Start Category List -->
            <TABLE id="Table1" cellSpacing="0" cellPadding="0" width="100%" border="0"  >
                <!-- start section title -->
                <TR>
                    <TD align="center"> <DIV class="pageTitle">
                            <span id="lblSection">
                                <?php
                                $explode = explode("?", basename($_SERVER['REQUEST_URI']));
                                $page = $explode[0];
                                $get = $explode[1];
                                $sql_get_page_name = "SELECT name FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='" . $page . "' and get='" . $get . "'";
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