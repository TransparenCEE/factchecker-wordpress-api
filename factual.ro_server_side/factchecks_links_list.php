<?php
include_once(dirname(__FILE__) . "/common/header.inc.php");
include_once('Pager/Pager_Wrapper.php');
$tables_elements = 6;

if ($_POST['session'] == "on") {
    $_SESSION['file_on_server_search'] = trim($_POST['file_on_server_search']);
    $_SESSION['factcheck_status'] = trim($_POST['factcheck_status']);
}
if ($_GET['action'] == 'delete_search') {
    $_SESSION['file_on_server_search'] = '';
    $_SESSION['factcheck_status'] = "";
}
?>

<script type="text/javascript" src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/set_tr_color_jsfunction.js" ></script>

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
                            Factchecks Links List
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding-left:35px;">Show all the links where a factcheck will be shown by the plugin.<br>
Allows the adding/deleting of the links.
                    </td>
                </tr>                 
                <tr>
                    <td align="center" valign="top">
                        <!--Start content-->

                        <table class="grid" width="100%" border="0" cellpadding="0" cellspacing="1">
                            <tr>
                                <td>
                                    <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="POST">
                                        <b>Filters:</b>
                                        <?php if ($_SESSION['file_on_server_search'] != '' || $_SESSION['factcheck_status'] != '' || $_SESSION['file_privacy'] != '') { ?>
                                            <font color="Red">
                                            <strong>
                                                <b>active&nbsp;</b>
                                            </strong>
                                            </font>
                                            <a href="<?php print $_SERVER['PHP_SELF']; ?>?action=delete_search"><b>delete</b></a>&nbsp;&nbsp;
                                        <?php } ?>

                                        <br/>
                                        Search (Factcheck Title, Link url, Snippet) :
                                        <input type="text" size="25" name="file_on_server_search" value="<?php print $_SESSION['file_on_server_search'] ?>">
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="submit" value="Filter" class="input">
                                        <input type="hidden" name="session" value="on">
                                    </form>
                                </td>
                            </tr>
                        </table>


                        <?php
                        $sql_get_users = "SELECT a.md5_link_identifier, a.link_identifier,a.id_link, a.link_identifier, a.link_content, a.snippet, a.insert_datetime, b.factcheck_link, b.post_title FROM factcheck_content2links as a LEFT JOIN factcheck_content as b ON a.id_factcheck = b.id_factcheck ";
                        $sql_get_users .= " where a.status='active' ";

                        if ($_SESSION['file_on_server_search'] != "") {
                            $_SESSION['file_on_server_search'] = str_replace("\\", "\\\\", $_SESSION['file_on_server_search']);
                            $sql_get_users .= " AND ( "
                                    . "b.post_title LIKE '%" . $db_write->escape($_SESSION['file_on_server_search']) . "%'  "
                                    . "OR a.link_content  LIKE '%" . $db_write->escape($_SESSION['file_on_server_search']) . "%'  "
                                    . "OR a.snippet  LIKE '%" . $db_write->escape($_SESSION['file_on_server_search']) . "%'  "
                                    . ")";
                        }
                        $sql_get_users .=" ORDER BY insert_datetime DESC";
//                        echo $sql_get_users; 
                        //$res_get_users = $db_write->query($sql_get_users);
                        //$total = $res_get_users->numRows();
                        $no_items_per_page = 25;
                        $params = array(
                            'perPage' => $no_items_per_page,
                            'delta' => 3, // for 'Jumping'-style a lower number is better
                            'append' => false,
                            'clearIfVoid' => true,
                            'urlVar' => 'page_no',
                            //'path' => $cfg_array['site_root_path'],
                            'fileName' => 'factchecks_links_list.php?page_no=%d',
                            'mode' => 'Sliding', //try switching modes
                            'curPageSpanPre' => '<span class="email_headertabletext">Page&nbsp;&nbsp;', // css pt 'Pagina x'
                            'curPageSpanPost' => '</span>',
                            'separator' => '<span class="email_headertabletext">|</span>',
                            'prevImg' => ' <  ',
                            'nextImg' => ' > ',
                            'firstPageText' => ' << ',
                            'firstPagePre' => '',
                            'firstPagePost' => '',
                            'lastPageText' => ' >> ',
                            'lastPagePre' => '',
                            'lastPagePost' => '',
                        );
                        $page_data = Pager_Wrapper_MDB2($db_read, $sql_get_users, $params);
                        $page_num = $page_data['page_numbers']['total'];
                        $num_elements = $page_data['totalItems'];
                        if (is_array($page_data) && $page_num > 1) {
                            ?>
                            <div class="s_orderpage_link">
                                <ul class="mainorderpage_profile_link">
                                    <li>
                                        <div align="center">
                                            <?php print str_replace("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;", $page_data['links']); ?>
                                            <span class="pages_info">
                                                <?php print " (" . $page_num . " pages, " . $num_elements . " items)"; ?>
                                            </span>
                                        </div>
                                    </li>	
                                </ul>
                            </div>
                        <?php } ?>						


                        <table class="grid" width="100%" border="0" cellpadding="0" cellspacing="1">
                            <tr>
                                <td>
                                    <b>
                                        <?php echo $num_elements; ?>
                                    </b> Results
                            </tr>
                    </td>
            </table>

            <table width="100%" cellpadding="3" cellspacing="3" border="0" class="grid">
                <TR>
                    <th align="center" width="2%">Inc</th>
                    <th align="left"  width="15%"> Factcheck </th>
                    <th align="left"> Link  </th>
                    <th align="left" width="15%"> Snippet </th>
                    <?php /* <th align="left" width="10%"> q </th> */ ?>
                    <th align="left" width="10%"> Insert date </th>
                    <th align="left" width="10%"> Actions </th>
                </TR>

                <?php
                $i = 0;
                $page_no = $_GET['page_no'];
                if (!$page_no) {
                    $page_no = 1;
                }
                if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
                    foreach ($page_data['data'] as $row_factchecked) {
                        $count_inside_fields = 0;
                        $td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
                        $id_element = $row_factchecked['id_link'];
                        $td_class = ($i % 2) == 1 ? 'even' : 'odd';
                        //$td_class = ($i%2)==1?'td_even_row':'td_od_row';
                        $increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
                        ?>					

                        <tr id="tr_link_<?php echo $id_element; ?>" onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
                            <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top" align="center">
                                <a name="#fact_<?php echo $row_factchecked['id_factcheck']; ?>" style="margin-top:-30px;" id="fact_<?php echo $row_factchecked['id_factcheck']; ?>"></a>
                                <span class="sitetext1" >
                                    <?php echo $increment; ?>
                                </span>
                            </td>

                            <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                                <span class="sitetext1">
                                    <a href="<?php echo $row_factchecked['factcheck_link']; ?>" target="_blank"><?php echo $row_factchecked['post_title']; ?></a>
                                </span>																	
                            </td>

                            <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                                <?php echo $row_factchecked['link_content']; ?>
                                <br> link identifier: <?php echo $row_factchecked['link_identifier']; ?>

                            </td>

                            <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                                <span id="snippet_<?php echo $id_element; ?>" class="txSnippet"><?php echo $row_factchecked['snippet']; ?></span>
                                <form action="javascript:;" id="frm_snippet_<?php echo $id_element; ?>" style="display:none;" class="check2disable">
                                    <textarea id="tx_snippet_<?php echo $id_element; ?>" class=""><?php echo $row_factchecked['snippet']; ?></textarea>
                                    <br/>
                                    <span id="msg_snippet_<?php echo $id_element; ?>" class="check2disable"></span>

                                    <br/>
                                    <input type="submit" value="Modify snippet" class="input"  onclick="javascript:linksAction('modify_snippet', <?php echo $id_element; ?>);" style="cursor:pointer">
                                </form>
                            </td>
                            <?php /*
                              <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                              <?php echo $row_factchecked['md5_link_identifier'];?>
                              </td>
                             */ ?>

                            <td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                                <?php echo date('Y-m-d', strtotime($row_factchecked['insert_datetime'])); ?>
                            </td>							

                            <td class="<?php echo $td_class; ?> td_actions_<?php echo $id_element; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
                                <a href="javascript: disableAllInputs('<?php echo $id_element; ?>');" title="Edit"><img src="design/images/btn_edit.gif" alt="Edit" /></a>
                                &nbsp; &nbsp;
                                <a href="javascript: linksAction('disable_link', <?php echo $id_element; ?>);" title="Disable"><img src="design/images/btn_delete.gif" alt="Disable" /></a>
                            </td>

                        </tr>	

                        <?php
                        $i++;
                    }
                }
                ?>

            </table>

            <?php if (is_array($page_data) && $page_num > 1) {
                ?>
                <div class="s_orderpage_link">
                    <ul class="mainorderpage_profile_link">
                        <li>
                            <div align="center">
                                <?php print str_replace("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;", $page_data['links']); ?>
                                <span class="pages_info">
                                    <?php print " (" . $page_num . " pages, " . $num_elements . " items)"; ?>
                                </span>
                            </div>
                        </li>	
                    </ul>
                </div>
            <?php } ?>							




            <!--End content-->
        </td>
    </tr>
</table>
</td>
</tr>
</table>
<!-- End Main Content -->
<link rel="stylesheet" href="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/jquery/jquery-ui.css">
<script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/jquery/jquery-1.10.2.js"></script>
<script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/show_hide_functions.js" ></script>

<script type="text/javascript" >
                                function disableAllInputs(id) {
//	$("#tx_snippet_"+id).val('');
                                    $(".txSnippet").each(function () {
                                        $(this).show();
                                    });
                                    if (id != 0) {
                                        $("#snippet_" + id).hide();
                                        $(".check2disable").each(function () {
                                            $(this).hide();
                                        });
                                        $("#frm_snippet_" + id).show();
                                    }
                                }

                                function linksAction(action, id_link) {
                                    var postData;
                                    switch (action) {
                                        case "modify_snippet":
                                            var snippet = $("#tx_snippet_" + id_link).val();
                                            snippet = snippet.trim();
                                            if (snippet == '') {
                                                $('#msg_snippet_' + id_link).html('<font style="font-size: 15px;" color="red"><b>Empty snippet!</b></font>');
                                                $('#msg_snippet_' + id_link).show();
                                                return;
                                            }
                                            postData = {action: action, id_link: id_link, snippet: snippet};
//                                            console.log(postData);
                                            break;
                                        case "disable_link":
                                            postData = {action: action, id_link: id_link};
                                            var confirmDisable = confirm('Are you sure you want to disable?');
                                            if (confirmDisable == false) {
                                                return;
                                            }
                                            break;
                                        default:
                                            break;
                                            return;
                                    }
                                    $.ajax({
                                        method: "POST",
                                        type: "POST",
                                        url: "factchecks_links_list_ajax.php",
                                        data: postData
                                    })
                                            .done(function (msg) {
                                                if (msg.length > 0) {
                                                    var str = msg.split('%$');
                                                    var msg_type = str[0];
                                                    var msg_value = str[1];
                                                    var snippet_ret = str[2];
//                                                    console.log(msg_type + ' - ' + msg_value);
                                                    if (msg_type == 'linkNoExists') {

                                                    }
                                                    if (msg_type == 'linkDisabled') {
                                                        $(".td_actions_" + id_link).html('<font style="font-size: 15px;" color="red"><b>The link has been disabled!</b></font>');
                                                        disableAllInputs(0);
                                                    }
                                                    if (msg_type == 'snippetModified') {
                                                        disableAllInputs();
//                                                        console.log(snippet_ret);
                                                        $('#snippet_' + id_link).html(snippet_ret);
//                                                        $("#tx_snippet_" + id_link).val('');
                                                        $('#msg_snippet_' + id_link).html('<font style="font-size: 15px;" color="red"><b>The snippet is modified!</b></font>');
                                                        $('#snippet_' + id_link).show();
                                                        $('#msg_snippet_' + id_link).show();
                                                        $(".txSnippet").show();
                                                        $("#tx_snippet_" + id_link).show();
                                                    }
                                                }
                                            });
                                }
</script>
<?php
include_once(dirname(__FILE__) . "/common/footer.inc.php");
?>                         