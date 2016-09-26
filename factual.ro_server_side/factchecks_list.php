<?php
include_once(dirname(__FILE__)."/common/header.inc.php");
include_once('Pager/Pager_Wrapper.php');
$tables_elements = 3;

if ($_POST['session'] == "on") {
    $_SESSION['factchecks_search'] = trim($_POST['factchecks_search']);
    $_SESSION['factcheck_status'] = trim($_POST['factcheck_status']);
}
if ($_GET['action'] == 'delete_search') {
	$_SESSION['factchecks_search'] = '';
    $_SESSION['factcheck_status'] = "";
}
?>

<script type="text/javascript" src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/set_tr_color_jsfunction.js" ></script>
<script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/jquery/jquery-1.10.2.js"></script>

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
							Factchecks List
						</div>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top">
                    <!--Start content-->
					
						<table class="grid" width="100%" border="0" cellpadding="0" cellspacing="1">
							<tr>
								<td>
									<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="POST">
										<b>Filtres:</b>
										<?php if ( $_SESSION['factchecks_search'] != '' || $_SESSION['factcheck_status'] != '' ) { ?>
										<font color="Red">
											<strong>
												<b>activa&nbsp;</b>
											</strong>
										</font>
										<a href="<?php print $_SERVER['PHP_SELF']; ?>?action=delete_search"><b>sterge</b></a>&nbsp;&nbsp;
										<?php } ?>
										
										<br/>
										Search (Title, Categorie, Context, Declaratie, Concluzie, Ce verificam, Verificare) :
										<input type="text" size="25" name="factchecks_search" value="<?php print $_SESSION['factchecks_search'] ?>">
										
										&nbsp;File Status:
										<select name="factcheck_status">
											<option value="">Unselected</option>
											<option value="Fals" <?php echo $_SESSION['factcheck_status'] == 'Fals' ? 'selected' : '' ?>>Fals</option>
											<option value="Neutru" <?php echo $_SESSION['factcheck_status'] == 'Neutru' ? 'selected' : '' ?>>Neutru</option>
											<option value="Parțial Adevărat" <?php echo $_SESSION['factcheck_status'] == 'Parțial Adevărat' ? 'selected' : '' ?>>Parțial Adevărat</option>
											<option value="Adevărat" <?php echo $_SESSION['factcheck_status'] == 'Adevărat' ? 'selected' : '' ?>>Adevărat</option>
											<option value="Parțial Fals" <?php echo $_SESSION['factcheck_status'] == 'Parțial Fals' ? 'selected' : '' ?>>Parțial Fals</option>
										</select>
										
										
										&nbsp;&nbsp;&nbsp;
										<input type="submit" value="Filtreaza" class="input">
										<input type="hidden" name="session" value="on">
									</form>
								</td>
							</tr>
						</table>
					
						 
						 <?php
                        $sql_get_users = "SELECT * FROM factcheck_content WHERE 1";                      
                        if ($_SESSION['factcheck_status'] != "") {
                            $sql_get_users .= " AND status = '" . $_SESSION['factcheck_status'] . "' ";
                        }                      
                        if ($_SESSION['factchecks_search'] != "") {
                            $_SESSION['factchecks_search'] = str_replace("\\", "\\\\", $_SESSION['factchecks_search']);
                            $sql_get_users .= " AND ( "
									. "post_title LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR categoria LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR context LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR declaratie LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR ce_verificam LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR concluzie LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. " OR verificare LIKE '%" . $db_write->escape($_SESSION['factchecks_search']) . "%'  "
									. ")";
                        }
                        $sql_get_users .=" ORDER BY post_datetime DESC";
                        //echo $sql_get_users;
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
                            'fileName' => 'factchecks_list.php?page_no=%d',
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
						<th align="center" width="5%">Inc</th>
						<th align="left" width="45%">Factcheck Info</th>
						<th align="left" width="50%">Factcheck Links</th>
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
							$id_element = $row_factchecked['id_factcheck'];
							$td_class = ($i % 2) == 1 ? 'even' : 'odd';
							//$td_class = ($i%2)==1?'td_even_row':'td_od_row';
							$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
							
							?>					
					
						<tr onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top" align="center">
								<a name="#fact_<?php echo $row_factchecked['id_factcheck']; ?>" style="margin-top:-30px;" id="fact_<?php echo $row_factchecked['id_factcheck']; ?>"></a>
								<span class="sitetext1" >
									<?php echo $increment; ?>
								</span>
							</td>
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<strong>Title:</strong> <a href="<?php echo $row_factchecked['factcheck_link'];?>" target="_blank"><?php echo $row_factchecked['post_title']; ?></a> </br>
									<strong>Categoria: </strong><?php echo $row_factchecked['categoria']; ?> </br>
									<strong>Context: </strong><?php echo strip_tags($row_factchecked['context']); ?> </br>
									<strong>Declaratie: </strong><?php echo strip_tags($row_factchecked['declaratie']); ?> </br>
									<strong>Ce verificam?: </strong><?php echo strip_tags($row_factchecked['ce_verificam']); ?> </br>
									<strong>Verificare: </strong><?php echo strip_tags($row_factchecked['verificare']); ?> </br>
									<strong>Concluzie: </strong><?php echo strip_tags($row_factchecked['concluzie']); ?> </br>
									<strong>Status: </strong><?php echo $row_factchecked['status']; ?> </br>
									<strong>Sursa: </strong><?php echo $row_factchecked['url_sursa']; ?> </br>
									<strong>Data publicarii: </strong><?php echo $row_factchecked['post_datetime']; ?> </br>
									
									
									<!-- </br><strong>ID(wp_posts):</strong> <?php echo $row_factchecked['ID']; ?> </br> -->
								</span>																	
							</td>
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" align="left" valign="top">
								<script type="text/javascript">
									<?php if(!empty($_GET['msg'])) { ?>
									function activateMsgFact() {
										<?php if(!empty($_GET['msg'])) { ?>
											$("#msg_add_link_"+<?php echo $_GET['id_f']; ?>).show();
										<?php } ?>
									}
									activateMsgFact();
									<?php } ?>
								</script>
								
								<div id="msg_add_link_<?php echo $row_factchecked['id_factcheck']; ?>" class="message_box"  style="display:none;background-color: #fff;padding:10px;margin:5px;">
									<div class="<?php if($_GET['result'] == 0) { ?> error_message <?php } else { ?> success_message <?php } ?>">
										<?php if(!empty($_GET['msg'])) { ?>
											<?php if($_GET['msg'] == 'linksImportedActivated') { ?>
										
												<?php echo $_GET['result'];?> links imported rom csv.
												
											<?php } ?>
											<?php if($_GET['msg'] == 'fileNoCsv') { ?>
										
												There were no file to import or file is not csv.
												
											<?php } ?>
												
										<?php } ?>
									</div>
								 </div>
								
								
								<a onclick="hideCsvForm('<?php echo $row_factchecked['id_factcheck'];?>_<?php echo $row_factchecked['ID'];?>');disableAllInputs('form_fact_<?php echo $row_factchecked['id_factcheck']; ?>');ShowHide('form_fact_<?php echo $row_factchecked['id_factcheck']; ?>'); " href="javascript:;" style="float:left"><b>Add Link</b></a>
								<span style="float:left"> &nbsp; &nbsp; |  &nbsp; &nbsp; </span>
								<a onclick="ShowHide('<?php echo $row_factchecked['id_factcheck'];?>_<?php echo $row_factchecked['ID'];?>');disableAllInputs('<?php echo $row_factchecked['id_factcheck']; ?>');" href="javascript:;" style="float:left"><b>Import from csv</b></a>
								<br style="clear:both;"/>
								
								
								<div style="width:400px">									
									<!-- <form action="factchecks_list_exec.php" id="form_fact_<?php echo $row_factchecked['id_factcheck']; ?>" method="POST"> -->
                                    
									<form action="javascript:;" id="form_fact_<?php echo $row_factchecked['id_factcheck']; ?>" method="POST" style="display:none;" class="check2disable">
										<table class="grid">
											<tr>
												<td>
													Link Url
												</td>
												<td>
													<input name="link" value="" id="input_link_<?php echo $row_factchecked['id_factcheck']; ?>" style="min-width: 370px;"/>
													<br/><span id="add_link_<?php echo $row_factchecked['id_factcheck']; ?>"></span>
												</td>
											</tr>
											<tr>
												<td>
													Snipped
												</td>
												<td>
													<textarea name="snipped" id="input_snipped_<?php echo $row_factchecked['id_factcheck']; ?>" style="min-width: 370px;" ></textarea>
												</td>
											</tr>
											<tr>
												<td>
													
												</td>
												<td align="left">
													<input type="submit" value="Add Link" class="input"  onclick="add_edit_factcheck('<?php echo $row_factchecked['id_factcheck']; ?>', 'add_link', <?php echo $row_factchecked['ID']; ?>, '');" style="cursor:pointer">
												</td>
											</tr>
										</table>
									</form>									
								</div>
								
								
								<div  style="width:400px">
									<form class="cvsForm" method="POST" enctype="multipart/form-data" id="<?php echo $row_factchecked['id_factcheck'];?>_<?php echo $row_factchecked['ID'];?>" action="factchecks_csv_import_ajax.php" style="display:none;" class="check2disable">										
										<table class="grid" id="csv_<?php echo $row_factchecked['id_factcheck'];?>_<?php echo $row_factchecked['ID'];?>">
											<tr>
												<td colspan="2">
													<strong>Import links from .csv file</strong>
													<br> File format: 2 columns: link, snippet
												</td>
											</tr>
											<tr>
												<td>
													<input type="file" name="import_csv" id="file2import_<?php echo $row_factchecked['id_factcheck'];?>_<?php echo $row_factchecked['ID'];?>" class="input"/>
												</td>
												<td>
													<input type="hidden" value="<?php echo $row_factchecked['id_factcheck'];?>" name="id_factcheck" />
													<input type="hidden" value="<?php echo $row_factchecked['ID'];?>" name="ID" />
													<input type="hidden" value="<?php echo $_GET['page_no'];?>" name="page" />
													<input type="submit" value="Import" name="import" class="input"/>
												</td>
											</tr>
										</table>
									</form>
								</div>
								
								<br/>
								
								<div>
									<strong>Assigned links:</strong>
									
									<div id="links_list_<?php echo $row_factchecked['id_factcheck'];?>" style=" margin-left:5px;">
										
									<?php 
									require_once(dirname(__FILE__) . '/libraries/functions/adminFactcheckList.function.php');
									echo adminFactcheckList($row_factchecked['id_factcheck'], $row_factchecked['ID'], $db_read);
									?>
										
									</div>	
								</div>
								
								
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
<script src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/ValidateUrl_jsfunction.js" ></script>
<script type="text/javascript" src="<?php echo $cfg_array['site_root_path']; ?>libraries/javascript/show_hide_functions.js" ></script>

<script type="text/javascript" >
function add_edit_factcheck(id_factcheck, action, id_post, id_link) {
	$('#msg_add_link_'+id_factcheck).hide();
	hideCsvForm(id_factcheck+'_'+id_post);
	action = action.toLowerCase();
	var postData;
	var linkVal = $('#input_link_'+id_factcheck).val();
	linkVal = linkVal.trim();
	switch(action) {
		case "add_link":
			action = "add_link";
			if(linkVal == "") {
				$('#add_link_'+id_factcheck).html('<font style="font-size: 15px;" color="red"><b>Empty link!</b></font>');
				return;
			}
			if( !ValidURL(linkVal)) {
				$('#add_link_'+id_factcheck).html('<font style="font-size: 15px;" color="red"><b>Please enter a valid url!</b></font>');
				return;
			}
			postData = {id_factcheck: id_factcheck, action: action, link: linkVal, snipped: $('#input_snipped_'+id_factcheck).val(), id_post: id_post};
			break;
		case "modify_link":
			action = "modify_link";
//			var linkValUpdate = $('#update_link_'+id_factcheck+'_'+id_link).val();
//			linkValUpdate = linkValUpdate.trim();
//			if(linkValUpdate == "") {
//				console.log('val--'+action);
//				$('#msg_modify_link_'+id_factcheck+'_'+id_link).html('<font style="font-size: 15px;" color="red"><b>Empty link!</b></font>');
//				return;
//			}
//			if( !ValidURL(linkValUpdate)) {
//				$('#msg_modify_link_'+id_factcheck+'_'+id_link).html('<br><font style="font-size: 15px;" color="red"><b>Please enter a valid url!</b></font>');
//				return;
//			}
//			postData = {id_factcheck: id_factcheck, action: action, link: linkValUpdate, snipped: $('#update_snipped_'+id_factcheck+'_'+id_link).val(), id_post: id_post, id_link: id_link};
			postData = {id_factcheck: id_factcheck, action: action, snipped: $('#update_snipped_'+id_factcheck+'_'+id_link).val(), id_post: id_post, id_link: id_link};
			break;
		case "disable_link":
			action = "disable_link";
			postData = {id_factcheck: id_factcheck, action: action, id_post: id_post, id_link: id_link};
			var confirmDisable = confirm('Are you sure you want to disable?');
			if(confirmDisable == false) {
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
		url: "factchecks_list_ajax.php",
		data: postData
	})
	.done(function( msg ) {
		if(msg.length > 0) {
			var str = msg.split('%$');
			var msg_type = str[0];
			var msg_value = str[1];
			var links_content = str[2];
			var msg_content = '';
			console.log(msg_type + ' - ' + msg_value);
			if(msg_type == 'linkExists' && msg_value == 0) { 
				msg_content = 'Link already exists!';
			}
			if(msg_type == 'addlink' && msg_value == 0) { 
				msg_content = 'Please insert a valid link! Eg.: http://example.com';
			}
			if(msg_type == 'linkNoExists' && msg_value == 0) { 
				msg_content = 'Link was not found';
			}
			if(msg_type == 'modifylink') {
				if(msg_value == 0) { 
					msg_content = 'There were no modification to do.';
				} else { 
					msg_content = 'The link has been modified!';
					$('#link_div_'+id_factcheck+'_'+id_link).remove();
					disableAllInputs();
				}
			 }
			if(msg_type == 'insertlink') {
				if(msg_value == 0) { 
					msg_content = 'There was an error. Please try again.';
				} else { 
					msg_content = 'The link has been added!';
					disableAllInputs();
				}
					$('#input_snipped_'+id_factcheck).val('');
					$('#input_link_'+id_factcheck).val('');
			}
			if(msg_type == 'insertlink' || msg_type == 'modifylink' || msg_type == 'linkActivated') {
				$('#emptyLinksList_'+id_factcheck+'_'+id_link).hide();				
				$('#msg_modify_link_'+id_factcheck+'_'+id_link).html();
				$('#msg_add_link_'+id_factcheck+'_'+id_link).html();
			}
			if(msg_type == 'linkDisabled') {
				$('#link_div_'+id_factcheck+'_'+id_link).hide();
				msg_content = 'The link has been disabled!';
				disableAllInputs();
			}
			if(msg_type == 'linkActivated') {
				if(msg_value == 0) { 
					msg_content = 'Link already exists!';
				} else {
					msg_content = 'The link has been enabled!';
					disableAllInputs();
				}
			}
			if( links_content != '') {
				$('#links_list_'+id_factcheck).html( links_content);
			}
			$('#msg_add_link_'+id_factcheck).show();
			$('#msg_add_link_'+id_factcheck).html('<font style="font-size: 12px;" color="' + ( msg_value == '0' ? 'red' : 'green') + '"><b>' + msg_content + '</b></font>');
			$('#add_link_'+id_factcheck).html('');
		}
	});
}
function enable_input(id_factcheck, id_link) {
//	$('#link_'+id_factcheck+'_'+id_link).hide();
	$('#snipped_'+id_factcheck+'_'+id_link).hide();
//	$('#update_link_'+id_factcheck+'_'+id_link).show();
	$('#update_snipped_'+id_factcheck+'_'+id_link).val('');
	$('#update_snipped_'+id_factcheck+'_'+id_link).show();
	$('#go_update_'+id_factcheck+'_'+id_link).show();
	$('#go_reset_'+id_factcheck+'_'+id_link).show();
}

function disableAllInputs(id) {	
	$(".check2disable").each(function (){
		console.log($(this).attr('id'));
		if($(this).attr('id') != id) {
			$(this).hide();
		}
    });
	$(".check2enable").each(function (){
//		console.log($(this).valueOf());
		$(this).show();
    });
	$(".msgcheck2disable").each(function (){
//		console.log($(this).valueOf());
		$(this).html('');
    });
}

function reset_add_edit_factcheck(id) {
//	$("#update_link_"+id).val('');
	$("#snipped_"+id).show();
	$("#link_"+id).show();
	$("#update_snipped_"+id).val('');
	$("#update_snipped_"+id).hide();
	$("#go_update_"+id).hide();
	$("#go_reset_"+id).hide();
}
function hideCsvForm(id) {
	$(id).hide();
	$("csv_"+id).hide();
	$(".cvsForm").hide();
}

//$(".cvsForm").submit(function() {
//	var id_form = $(this).attr('id');
//	var formData = new FormData(this);	
//	var file_data = $( "#file2import_"+id_form ).prop('files')[0];
//	console.log(file_data);
//	formData.append('file', file_data);
//	console.log(formData);
////	return false;
//    $.ajax({
//        type: "post",
//		url: "factchecks_csv_import_ajax.php",
//		data: formData,
//		async: false,
//		cache: false,
//		contentType: false,
//		enctype: "multipart/form-data",
//		processData: false
//	})
//	.done(function( msg ) {
//		if(msg.length > 0) {
//			var str = msg.split('%$');
//			var msg_type = str[0];
//			var msg_value = str[1];
//			var links_content = str[2];
//			var msg_content = '';
//			console.log(msg_type + ' - ' + msg_value);
//			if(msg_type == 'fileNoCsv' && msg_value == 0) { 
//				msg_content = 'File is not csv!';
//			}
//			$('#msg_add_csv_'+id_form).show();
//			$('#msg_add_csv_'+id_form).html('<font style="font-size: 12px;" color="' + ( msg_value == '0' ? 'red' : 'green') + '"><b>' + msg_content + '</b></font>');
//		}
//	});
//    return false; 
//});
	
</script>
<?php
include_once(dirname(__FILE__)."/common/footer.inc.php");
?>                         