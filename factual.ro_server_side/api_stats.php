<?php
include_once(dirname(__FILE__)."/common/header.inc.php");
include_once('Pager/Pager_Wrapper.php');
$tables_elements = 5;
if(!$_GET['type']) {
	$_GET['type'] = 'days';
}

$sql_select_last_day = "SELECT insert_datetime FROM api_factchecks ORDER BY insert_datetime DESC";
$last_day = $db_read->queryOne($sql_select_last_day);

$sql_select_first_day = "SELECT insert_datetime FROM api_factchecks ORDER BY insert_datetime ASC";
$first_day = $db_read->queryOne($sql_select_first_day);

function allPartsDate($datetimeCompleteFormat) {
	list($dayMY, $timeHIS) = explode(' ', $datetimeCompleteFormat);
	list($y,$m,$d) = explode( '-', $dayMY);
	list($h,$i,$s) = explode(':', $timeHIS);
	$arr = [
		'y' => $y,
		'm' => $m,
		'd' => $d,
		'h' => $h,
		'i' => $i,
		's' => $s,
		'day' => $dayMY,
		'time' => $timeHIS
	];
	return $arr;
}

$arr_english_full_months_names  =[
	'01' => 'January',
	'02' => 'February',
	'03' => 'Mars',
	'04' => 'April',
	'05' => 'May',
	'06' => 'June',
	'07' => 'July',
	'08' => 'August',
	'09' => 'September',
	'10' => 'Octomber',
	'11' => 'November',
	'12' => 'December',
];

$arr_english_short_months_names  =[
	'01' => 'Jan',
	'02' => 'Feb',
	'03' => 'Mar',
	'04' => 'Apr',
	'05' => 'May',
	'06' => 'Jun',
	'07' => 'Jul',
	'08' => 'Aug',
	'09' => 'Sep',
	'10' => 'Oct',
	'11' => 'Nov',
	'12' => 'Dec',
];
		
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
							API stats 														
						</div>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top">
                         <!--Start content-->						 
						<div style="margin:30px 0 0 35px;text-align: left; font-weight: bold; font-size: 14px;">
							
							<a href="api_stats.php?type=days" style="font-weight: bold; font-size: 14px;<?php if($_GET['type'] == 'days') { ?> color:red; <?php } ?>">Days</a>
							&nbsp; | &nbsp;
							<a href="api_stats.php?type=weeks" style="font-weight: bold; font-size: 14px;<?php if($_GET['type'] == 'weeks') { ?> color:red; <?php } ?>">Weeks</a> 
							&nbsp; | &nbsp;
							<a href="api_stats.php?type=months" style="font-weight: bold; font-size: 14px;<?php if($_GET['type'] == 'months') { ?> color:red; <?php } ?>">Months</a>
							&nbsp; | &nbsp;
							<a href="api_stats.php?type=top" style="font-weight: bold; font-size: 14px;<?php if($_GET['type'] == 'top') { ?> color:red; <?php } ?>">Top factchecks</a>
							&nbsp; | &nbsp;
							<a href="api_stats.php?type=top_links" style="font-weight: bold; font-size: 14px;<?php if($_GET['type'] == 'top_links') { ?> color:red; <?php } ?>">Top links</a>
							
							<br/><br/>
							
							 
<?php

$tb_title = "Days";
switch ($_GET['type'] ) {
	case 'days':

				$sql_get_items = 'SELECT DATE_FORMAT(insert_datetime, "%Y-%m-%d") as date_day, id FROM api_factchecks ';                      
				$sql_get_items .= ' GROUP BY DATE_FORMAT(insert_datetime, "%Y-%m-%d")   '; 						
				$sql_get_items .= " ORDER BY insert_datetime DESC";
//                        echo $sql_get_items; 
				//$res_get_users = $db_write->query($sql_get_items);
				//$total = $res_get_users->numRows();
				$no_items_per_page = 30;
				$params = array(
					'perPage' => $no_items_per_page,
					'delta' => 3, // for 'Jumping'-style a lower number is better
					'append' => false,
					'clearIfVoid' => true,
					'urlVar' => 'page_no',
					//'path' => $cfg_array['site_root_path'],
					'fileName' => 'api_stats.php?page_no=%d&type=' . $_GET['type'],
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
				$page_data = Pager_Wrapper_MDB2($db_read, $sql_get_items, $params);
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
						<th align="left"  width="15%"> <?php echo $tb_title;?> </th>
						<th align="left"  width="15%"> Total  </th>
						<th align="left"  width="15%"> Unique  </th>
						<th align="left"  width="15%"> Top factchecks </th>						
					</TR>
					
					<?php
					$i = 0;
					$page_no = $_GET['page_no'];
					if (!$page_no) {
						$page_no = 1;
					}
					if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
						foreach ($page_data['data'] as $row) {
							$count_inside_fields = 0;
							$td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
							$id_element = $row['id'];
							$td_class = ($i % 2) == 1 ? 'even' : 'odd';
							$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
							
							?>					
					
						<tr id="tr_link_<?php echo $id_element;?>" onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top" align="center">
								<a name="#fact_<?php echo $row_factchecked['id_factcheck']; ?>" style="margin-top:-30px;" id="fact_<?php echo $row_factchecked['id_factcheck']; ?>"></a>
								<span class="sitetext1" >
									<?php echo $increment; ?>
								</span>
							</td>
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<?php 
									list($y,$m,$d) = explode( '-', $row['date_day']);
									echo $d . ' ' . strtolower($arr_english_short_months_names[$m]) . ' ' . $y;
									?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<?php 
									$sql_get_total_times = 'SELECT COUNT(id_factcheck) as no FROM api_factchecks WHERE DATE_FORMAT(insert_datetime, "%Y-%m-%d") = "' . $row['date_day']. '" ';
									$sql_get_total_times .= ''; 	
//									echo $sql_get_total_times;
									echo $db_read->queryOne($sql_get_total_times);
									?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								$sql_get_total_times = 'SELECT id_factcheck, COUNT(id_factcheck) as no FROM api_factchecks WHERE DATE_FORMAT(insert_datetime, "%Y-%m-%d") = "' . $row['date_day']. '" ';
								$sql_get_total_times .= ' GROUP BY id_factcheck ORDER BY no DESC'; 	
//									echo $sql_get_total_times;
								$arr_uniq_api_per_day = $db_read->queryAll($sql_get_total_times);
								$uniq_api_per_day =  count($arr_uniq_api_per_day);
								echo $uniq_api_per_day;
								?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								if($uniq_api_per_day > 0) {
									$string_top_api_per_day = '';
									if( $arr_uniq_api_per_day[0]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[0]['id_factcheck'] . ( $arr_uniq_api_per_day[1]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[1]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[1]['id_factcheck'] . ( $arr_uniq_api_per_day[2]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[2]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[2]['id_factcheck'];
									}
									if(!empty($string_top_api_per_day)) {
										$SQL_GET_TOPS_API = "SELECT factcheck_link FROM factcheck_content WHERE id_factcheck IN ($string_top_api_per_day)  ORDER BY FIELD(id_factcheck,$string_top_api_per_day)";
										$arr_get_tops = $db_read->queryCol($SQL_GET_TOPS_API);
										foreach ($arr_get_tops as $no_api => $factcheck_link ) { ?>
									
									<?php echo ($no_api+1); ?>. <a href="<?php echo $factcheck_link; ?>" target="_blank"><?php echo $factcheck_link; ?></a><br/>
												
										<?php }
									}
								}
								?>
								</span>																	
							</td>
							
							
						</tr>	
						
						<?php
								$i++;
							}
						}
						?>
						
					</table>

					<?php if (is_array($page_data) && $page_num > 1) { ?>
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


<?php

		break;
	
	case 'weeks':
		$tb_title = 'Weeks';
//		echo $last_day . '<br>';
//		echo $first_day . '<br>';
		if(empty($last_day)) {
			echo 'No requests yet!';
			exit();
		}
		
//		$last_day = "2016-08-25 00:00:00";
//		$first_day = "2016-08-25 00:00:00";
//		
		//afla prima zi din saptamana corespunzatoare ultimei zile din db
		$timestamp_IntervalLast_day = strtotime("$last_day");
		$dayno_last_day = date("N", $timestamp_IntervalLast_day);
//		echo '$dayno_last_day ' . $dayno_last_day . '<br>';
		$noDays2Decrese_last_day = $dayno_last_day - 1;		
		$arr_last_day = allPartsDate($last_day);
		$dateL = new DateTime($arr_last_day['day']);
		$dateL->sub(new DateInterval("P$noDays2Decrese_last_day"."D"));
		$firs_day_last_week = $dateL->format('Y-m-d');
//		echo $firs_day_last_week . "<br>";
		
		//aflam prima zi din saptamana corespunzatoare primei zile din db
		$timestamp_IntervalFirst_day = strtotime("$first_day");
		$dayno_first_day = date("N", $timestamp_IntervalFirst_day);
//		echo '$dayno_first_day ' . $dayno_first_day . '<br>';
		$noDays2Decrese_first_day = $dayno_first_day - 1;		
		$arr_frist_day = allPartsDate($first_day);
		$dateF = new DateTime($arr_frist_day['day']);
		$dateF->sub(new DateInterval("P$noDays2Decrese_first_day"."D"));
		$firs_day_first_week = $dateF->format('Y-m-d');
//		echo $firs_day_first_week . "<br>";		
		
		//arr cu saptamanile 
		$begin = new DateTime( $firs_day_first_week );
		$end = new DateTime( $firs_day_last_week );
		$end->add(new DateInterval('P7D'));
		$interval = new DateInterval('P1W');
		$daterange = new DatePeriod($begin, $interval ,$end);		
		$arr_weeks = [];
		foreach($daterange as $key => $date) {
//			echo '<br>'. $key. ' - ' .  $date->format("Y-m-d");
			$wEnd =  new DateTime( $date->format("Y-m-d") );
			$wEnd->add(new DateInterval('P6D'));
			$arr_weeks[$key] = [
				'start' => $key == 0 ? $arr_frist_day['day'] : $date->format("Y-m-d"),
				'end' => $wEnd->format("Y-m-d"),
			];
		}
		//modificam perioada pt ultima sapt: inceput sapt - ultima zi din db
//		$arr_english_full_months_names[$weeks_no-1]['start'] = '';
		rsort($arr_weeks);
//		lastweek last day se modfica cu ultima zi din db
		$arr_weeks[0]['start'] = $firs_day_last_week;
		$arr_weeks[0]['end'] = $arr_last_day['day'];
//		echo '<pre>'; print_r($arr_weeks); echo '</pre>'; exit();
		
		//listare saptamani
		$weeks_no =  count($arr_weeks);	
		$page_no = 1;
		$no_items_per_page = 30;	
		$total_pages = ceil($weeks_no / $no_items_per_page);
		if($_GET['page_no'] && is_numeric($_GET['page_no']) ) {
			$page_no = $_GET['page_no']; 
			$page_position = ($page_no - 1) * $no_items_per_page;
		}
		$arr_items2pages = array_chunk($arr_weeks, $no_items_per_page, true);
//		echo '<pre>'; print_r($arr_items2pages); echo '</pre>';				

		function displayLastLinks($current_page,$total_pages,$page_link ) {
			$next_link = $current_page+1;
			$lastLinks = "<a title=\"next page\" href=\"$page_link$next_link\"> &gt; </a>&nbsp;";
			$lastLinks .= "<a title=\"last page\" href=\"$page_link$total_pages\"> &gt;&gt; </a>";
			return $lastLinks;
		}
		function displayFirstLinks($current_page,$total_pages,$page_link ) {
			$prev_link = $current_page-1;
			$firstLink = '<a title="first page" href="' . $page_link . '1"> &lt;&lt; </a>&nbsp';
			$firstLink .= '<a title="previous page" href="' . $page_link . $prev_link . '"> &lt;  </a>&nbsp;';
			return $firstLink;
		}
		function forPaginator($start, $end, $current_page, $page_link, $type = 'all') {
			$pageSuffix = '<span class="email_headertabletext">|</span>&nbsp;&nbsp;&nbsp;';
			$pagination = '';
			for($i=$start;$i<=$end;$i++) {
				if($current_page == $i) {
					$pagination .= '<span class="email_headertabletext">Page&nbsp;&nbsp;' . $current_page . '</span>&nbsp;&nbsp;&nbsp;';
				} else {
					$pagination .=  "<a title=\"page $i\" href=\"$page_link$i\">$i</a>&nbsp;&nbsp;&nbsp;";
				}
				if($i<$end || $type == 'firsMiddlePages') {
					$pagination .= $pageSuffix;
				}
			}
			return $pagination;
		}
		function paginate_function($item_per_page, $current_page, $total_records, $total_pages, $delta=3) {
			$pagination = '';
			if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) {

				$pagination .= ' <div class="s_orderpage_link">
								<ul class="mainorderpage_profile_link">
									<li>
										<div align="center">';

				$right_links = $current_page + 3;
				$previous = $current_page - 1;
				$next = $current_page + 1;
				$first_link = true;
				$pageSuffix = '<span class="email_headertabletext">|</span>&nbsp;&nbsp;&nbsp;';
				$page_link = "api_stats.php?type=weeks&page_no=";

				if($current_page != 1) {
					$pagination .=  displayFirstLinks($current_page,$total_pages,$page_link );
				} 
				
				$totalsLinksNo = $delta*2+1;
				if($total_pages < $totalsLinksNo) {
					$pagination .= forPaginator(1, $total_pages, $current_page, $page_link, $type = 'all'); 
				} else { 
					if($current_page <= $delta) { //first $delta pages
						$pagination .= forPaginator(1, $totalsLinksNo, $current_page, $page_link, $type = 'all'); 
					} elseif($current_page > ($total_pages-$delta)) { //last pages
						$pagination .= forPaginator((($total_pages-$totalsLinksNo)+1), $total_pages, $current_page, $page_link, $type = 'all');
					} else { //middle pages
//						echo 'middle pages';
						$pagination .= forPaginator(($current_page-$delta), $current_page-1, $current_page, $page_link, $type = 'firsMiddlePages');
						$pagination .= '<span class="email_headertabletext">Page&nbsp;&nbsp;' . $current_page . '</span>&nbsp;&nbsp;&nbsp;<span class="email_headertabletext">|</span>&nbsp;&nbsp;&nbsp;';	
						$pagination .= forPaginator(($current_page+1), ($current_page+$delta), $current_page, $page_link, $type = 'all');
					}
				}
				if($current_page != $total_pages) {
					$pagination .=  displayLastLinks($current_page,$total_pages,$page_link);	
				}


				$pagination .= "							<span class=\"pages_info\">
												( $total_pages   pages, $total_records  items )
											</span>
										</div>
									</li>	
								</ul>
							</div>";

			}
			return $pagination;
		}

		$paginate = paginate_function($no_items_per_page, $page_no, $weeks_no, $total_pages);
		echo $paginate ;
		?>
				<table class="grid" width="100%" border="0" cellpadding="0" cellspacing="1">
					<tr>
						<td>
							<b>
								<?php echo $weeks_no; ?>
							</b> Results
						</tr>
					</td>
				</table>
				<table width="100%" cellpadding="3" cellspacing="3" border="0" class="grid">
					<TR>
						<th align="center" width="2%">Inc</th>
						<th align="left"  width="15%"> <?php echo $tb_title;?> </th>
						<th align="left"  width="15%"> Total  </th>
						<th align="left"  width="15%"> Unique  </th>
						<th align="left"  width="15%"> Top factchecks </th>						
					</TR>
					
					<?php
					if (!empty($arr_weeks) && !empty($arr_items2pages[$page_no-1]) ) {
						foreach ($arr_items2pages[$page_no-1] as $key => $row) {
							$count_inside_fields = 0;
							$td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
							$id_element = $key;
							$td_class = ($i % 2) == 1 ? 'even' : 'odd';
							$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
							
							?>					
					
						<tr id="tr_link_<?php echo $id_element;?>" onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top" align="center">
								<a name="#fact_<?php echo $row_factchecked['id_factcheck']; ?>" style="margin-top:-30px;" id="fact_<?php echo $row_factchecked['id_factcheck']; ?>"></a>
								<span class="sitetext1" >
									<?php echo $increment; ?>
								</span>
							</td>
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<?php  
									list($sy,$sm,$sd) = explode( '-', $row['start']);
									list($ey,$em,$ed) = explode( '-', $row['end']);
									echo $sd . ' ' . ($increment == 1 ? '' : strtolower($arr_english_short_months_names[$sm])) . ' ' . ( $sy!=$sy ? $sy : '');
									echo ' - ';
									echo $ed . ' ' . strtolower($arr_english_short_months_names[$em]) . ' ' . $ey;
									?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								$cond =  ' DATE_FORMAT(insert_datetime, "%Y-%m-%d") BETWEEN  "' . $row['start']. '" AND "' . $row['end']. '" ' ;
								$sql_get_total_times = 'SELECT COUNT(id_factcheck) as no FROM api_factchecks WHERE ' . $cond;
								$sql_get_total_times .= ''; 	
//									echo $sql_get_total_times;
								echo $db_read->queryOne($sql_get_total_times);
								?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								$sql_get_total_times = 'SELECT id_factcheck, COUNT(id_factcheck) as no FROM api_factchecks WHERE ' . $cond;
								$sql_get_total_times .= ' GROUP BY id_factcheck ORDER BY no DESC'; 	
//									echo $sql_get_total_times;
								$arr_uniq_api_per_day = $db_read->queryAll($sql_get_total_times);
								$uniq_api_per_day =  count($arr_uniq_api_per_day);
								echo $uniq_api_per_day;
								?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								if($uniq_api_per_day > 0) {
									$string_top_api_per_day = '';
									if( $arr_uniq_api_per_day[0]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[0]['id_factcheck'] . ( $arr_uniq_api_per_day[1]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[1]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[1]['id_factcheck'] . ( $arr_uniq_api_per_day[2]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[2]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[2]['id_factcheck'];
									}
									if(!empty($string_top_api_per_day)) {
										$SQL_GET_TOPS_API = "SELECT factcheck_link FROM factcheck_content WHERE id_factcheck IN ($string_top_api_per_day)  ORDER BY FIELD(id_factcheck,$string_top_api_per_day)";
										$arr_get_tops = $db_read->queryCol($SQL_GET_TOPS_API);
										foreach ($arr_get_tops as $no_api => $factcheck_link ) { ?>
									
									<?php echo ($no_api+1); ?>. <a href="<?php echo $factcheck_link; ?>" target="_blank"><?php echo $factcheck_link; ?></a><br/>
												
										<?php }
									}
								}
								?>
								</span>																	
							</td>
							
							
						</tr>	
						
						<?php
								$i++;
							}
						}
						?>
						
					</table>					

		<?php
			echo $paginate . "<br>";

		
		break;
	
	case 'months':
		$tb_title = 'Month';

				$sql_get_items = 'SELECT DATE_FORMAT(insert_datetime, "%Y-%m") as date_day, id FROM api_factchecks ';                      
				$sql_get_items .= ' GROUP BY DATE_FORMAT(insert_datetime, "%Y-%m")   '; 						
				$sql_get_items .= " ORDER BY insert_datetime DESC";
//                        echo $sql_get_items; 
				//$res_get_users = $db_write->query($sql_get_items);
				//$total = $res_get_users->numRows();
				$no_items_per_page = 30;
				$params = array(
					'perPage' => $no_items_per_page,
					'delta' => 3, // for 'Jumping'-style a lower number is better
					'append' => false,
					'clearIfVoid' => true,
					'urlVar' => 'page_no',
					//'path' => $cfg_array['site_root_path'],
					'fileName' => 'api_stats.php?page_no=%d&type=' . $_GET['type'],
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
				$page_data = Pager_Wrapper_MDB2($db_read, $sql_get_items, $params);
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
						<th align="left"  width="15%"> <?php echo $tb_title;?> </th>
						<th align="left"  width="15%"> Total  </th>
						<th align="left"  width="15%"> Unique  </th>
						<th align="left"  width="15%"> Top factchecks </th>						
					</TR>
					
					<?php
					$i = 0;
					$page_no = $_GET['page_no'];
					if (!$page_no) {
						$page_no = 1;
					}
					if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
						foreach ($page_data['data'] as $key => $row) {
							$count_inside_fields = 0;
							$td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
							$id_element = $row['id'];
							$td_class = ($i % 2) == 1 ? 'even' : 'odd';
							$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
							
							?>					
					
						<tr id="tr_link_<?php echo $id_element;?>" onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top" align="center">
								<a name="#fact_<?php echo $row_factchecked['id_factcheck']; ?>" style="margin-top:-30px;" id="fact_<?php echo $row_factchecked['id_factcheck']; ?>"></a>
								<span class="sitetext1" >
									<?php echo $increment; ?>
								</span>
							</td>
							
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<?php 
										list($y,$m) = explode('-', $row['date_day']);
										$sD = $arr_english_full_months_names[$m] . ' ' . $y;
										
//										if($key == 0 && (!$_GET['page_no'] || $_GET['page_no'] == 1) ) {
//											$arr_l = allPartsDate($last_day);
//											echo ($arr_l['d']+0) . ' ' . $sD . ' - ' . '1 ' . $sD;
//										} else if(($key+1) == count($page_data['data']) &&  (!$_GET['page_no'] || $_GET['page_no'] == $page_num) ) {
//											$arr_f = allPartsDate($first_day);
//											echo ($arr_f['d']+0) . ' ' . $sD . ' - ' . cal_days_in_month(CAL_GREGORIAN, $arr_f['m'], $arr_f['y']) . ' ' . $sD;
//										} else {
//											echo $sD;
//										}
										echo $sD;
									?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
									<?php 
									$sql_get_total_times = 'SELECT COUNT(id_factcheck) as no FROM api_factchecks WHERE DATE_FORMAT(insert_datetime, "%Y-%m") = "' . $row['date_day']. '" ';
									$sql_get_total_times .= ''; 	
//									echo $sql_get_total_times;
									echo $db_read->queryOne($sql_get_total_times);
									?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								$sql_get_total_times = 'SELECT id_factcheck, COUNT(id_factcheck) as no FROM api_factchecks WHERE DATE_FORMAT(insert_datetime, "%Y-%m") = "' . $row['date_day']. '" ';
								$sql_get_total_times .= ' GROUP BY id_factcheck ORDER BY no DESC'; 	
//								echo $sql_get_total_times;
								$arr_uniq_api_per_day = $db_read->queryAll($sql_get_total_times);
								$uniq_api_per_day =  count($arr_uniq_api_per_day);
								echo $uniq_api_per_day;
								?>
								</span>																	
							</td>
							<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" valign="top">
								<span class="sitetext1">
								<?php 
								if($uniq_api_per_day > 0) {
									$string_top_api_per_day = '';
									if( $arr_uniq_api_per_day[0]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[0]['id_factcheck'] . ( $arr_uniq_api_per_day[1]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[1]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[1]['id_factcheck'] . ( $arr_uniq_api_per_day[2]['id_factcheck'] ? ',' : '');
									}
									if( $arr_uniq_api_per_day[2]['id_factcheck']) {
										$string_top_api_per_day .= $arr_uniq_api_per_day[2]['id_factcheck'];
									}
									if(!empty($string_top_api_per_day)) {
										$SQL_GET_TOPS_API = "SELECT factcheck_link FROM factcheck_content WHERE id_factcheck IN ($string_top_api_per_day)  ORDER BY FIELD(id_factcheck,$string_top_api_per_day)";
//										echo $SQL_GET_TOPS_API;
										$arr_get_tops = $db_read->queryCol($SQL_GET_TOPS_API);
										foreach ($arr_get_tops as $no_api => $factcheck_link ) { ?>
									
									<?php echo ($no_api+1); ?>. <a href="<?php echo $factcheck_link; ?>" target="_blank"><?php echo $factcheck_link; ?></a><br/>
												
										<?php }
									}
								}
								?>
								</span>																	
							</td>
							
							
						</tr>	
						
						<?php
								$i++;
							}
						}
						?>
						
					</table>

					<?php if (is_array($page_data) && $page_num > 1) { ?>
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


<?php

		break;
	
	case 'top':
		$tb_title = 'Tops';
		if(!$_GET['top']) {
			$_GET['top'] = 'd';
		}
		?>
				
		<div style="margin:0px 0 0 25px;text-align: left; font-weight: bold; font-size: 14px;">			
			
			<a href="api_stats.php?type=top&top=d" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'd') { ?> color:red; <?php } ?>">Top last day</a> 
			&nbsp; | &nbsp;
			<a href="api_stats.php?type=top&top=w" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'w') { ?> color:red; <?php } ?>">Top last week</a>
			&nbsp; | &nbsp;
			<a href="api_stats.php?type=top&top=m" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'm') { ?> color:red; <?php } ?>">Top last month</a>

			<br/><br/>
			
			<?php
			$date2select = '';
			switch ($_GET['top']) {
				case 'd':
					$tm_per = strtotime("- 24 hours");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				case 'w':
					$tm_per = strtotime("- 7 days");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				case 'm':
					$tm_per = strtotime("- 30 days");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				default :
					break;
			}
//			echo $date2select . '<br>';
			
			$cond = " insert_datetime >= '" . $date2select . "' ";
			$sql_tops_total_api = "SELECT  id_factcheck, COUNT(id_factcheck) as no FROM api_factchecks WHERE $cond ";
			$sql_tops_total_api .= ' GROUP BY id_factcheck ORDER BY no DESC'; 
//			echo $sql_tops_total_api; 
			$no_items_per_page = 30;
			$params = array(
				'perPage' => $no_items_per_page,
				'delta' => 3, // for 'Jumping'-style a lower number is better
				'append' => false,
				'clearIfVoid' => true,
				'urlVar' => 'page_no',
				//'path' => $cfg_array['site_root_path'],
				'fileName' => 'api_stats.php?page_no=%d&type=' . $_GET['type'] . '&top=' . $_GET['top'],
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
			$page_data = Pager_Wrapper_MDB2($db_read, $sql_tops_total_api, $params);
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
				<tr>
					<th align="center" width="2%">Inc</th>
					<th align="left"  width="15%"> Titlu  </th>
					<th align="left"  width="15%"> Publish Date  </th>
					<th align="left"  width="15%"> Accesari unice  </th>
					<th align="left"  width="15%"> Accesari totale </th>						
				</tr>
				
			<?php
				$i = 0;
				$page_no = $_GET['page_no'];
				if (!$page_no) {
					$page_no = 1;
				}
				if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
					foreach ($page_data['data'] as $row) {
						$count_inside_fields = 0;
						$td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
						$id_element = $row['id_factcheck'];
						$td_class = ($i % 2) == 1 ? 'even' : 'odd';
						$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
						$sql_row_fact = 'SELECT post_title, factcheck_link, post_datetime, DATE_FORMAT(post_datetime, "%Y-%m-%d") as post_date FROM factcheck_content '; 
						$sql_row_fact .= " WHERE id_factcheck = '" . $row['id_factcheck'] . "' ";
						$row_fact = $db_read->queryRow($sql_row_fact);
						?>	
				<tr onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $increment; ?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<a href="<?php echo $row_fact['factcheck_link']; ?>" target="_blank"><?php echo $row_fact['post_title']; ?></a>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $row_fact['post_date']; ?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php
						$sql_get_unique = 'SELECT COUNT(id_factcheck) as no FROM api_factchecks';
						$sql_get_unique .= " WHERE id_factcheck = '" . $row['id_factcheck'] . "' ";
						$sql_get_unique .= " AND " .$cond;
						$sql_get_unique .= ' GROUP BY ip ORDER BY no DESC'; 	
						echo $db_read->queryOne($sql_get_unique);
						
						?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $row['no']; ?>
					</td>
				</tr>	
						
			<?php
					$i++;
				}
			}
			?>			
				
			</table>

			<?php if (is_array($page_data) && $page_num > 1) { ?>
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
			
		</div>
							
		<?php 
		break;
	
	case 'top_links':
		$tb_title = 'Top Links';
		if(!$_GET['top']) {
			$_GET['top'] = 'd';
		}
		?>
				
		<div style="margin:0px 0 0 25px;text-align: left; font-weight: bold; font-size: 14px;">			
			
			<a href="api_stats.php?type=top_links&top=d" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'd') { ?> color:red; <?php } ?>">Top last day</a> 
			&nbsp; | &nbsp;
			<a href="api_stats.php?type=top_links&top=w" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'w') { ?> color:red; <?php } ?>">Top last week</a>
			&nbsp; | &nbsp;
			<a href="api_stats.php?type=top_links&top=m" style="font-weight: bold; font-size: 14px;<?php if($_GET['top'] == 'm') { ?> color:red; <?php } ?>">Top last month</a>

			<br/><br/>
			
			<?php
			$date2select = '';
			switch ($_GET['top']) {
				case 'd':
					$tm_per = strtotime("- 24 hours");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				case 'w':
					$tm_per = strtotime("- 7 days");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				case 'm':
					$tm_per = strtotime("- 30 days");
					$date2select = date("Y-m-d H:i:s", $tm_per);
					break;
				default :
					break;
			}
//			echo $date2select . '<br>';
			
			$cond = " insert_datetime >= '" . $date2select . "' ";
			$sql_tops_total_api = "SELECT  id,id_factcheck,q, COUNT(q) as no FROM api_factchecks WHERE $cond ";
			$sql_tops_total_api .= ' GROUP BY q ORDER BY no DESC'; 
//			echo $sql_tops_total_api; 
			$no_items_per_page = 30;
			$params = array(
				'perPage' => $no_items_per_page,
				'delta' => 3, // for 'Jumping'-style a lower number is better
				'append' => false,
				'clearIfVoid' => true,
				'urlVar' => 'page_no',
				//'path' => $cfg_array['site_root_path'],
				'fileName' => 'api_stats.php?page_no=%d&type=' . $_GET['type'] . '&top=' . $_GET['top'],
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
			$page_data = Pager_Wrapper_MDB2($db_read, $sql_tops_total_api, $params);
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
				<tr>
					<th align="center" width="2%">Inc</th>
					<th align="left"  width="15%"> Titlu  </th>
					<th align="left"  width="15%"> Publish Date  </th>
					<th align="left"  width="15%"> Accesari unice  </th>
					<th align="left"  width="15%"> Accesari totale </th>						
				</tr>
				
			<?php
				$i = 0;
				$page_no = $_GET['page_no'];
				if (!$page_no) {
					$page_no = 1;
				}
				if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
					foreach ($page_data['data'] as $row) {
						$count_inside_fields = 0;
						$td_color = ($i % 2) == 1 ? '#F8F8F8' : '#F1F1F1';
						$id_element = $row['id'];
						$td_class = ($i % 2) == 1 ? 'even' : 'odd';
						$increment = $i + 1 + ($page_no - 1) * $no_items_per_page;
						
						$sql_row_fact = 'SELECT post_title, factcheck_link, post_datetime, DATE_FORMAT(post_datetime, "%Y-%m-%d") as post_date FROM factcheck_content '; 
						$sql_row_fact .= " WHERE id_factcheck = '" . $row['id_factcheck'] . "' ";
						$row_fact = $db_read->queryRow($sql_row_fact);
						
						$sql_row_link = 'SELECT link_identifier FROM factcheck_content2links '; 
						$sql_row_link .= " WHERE md5_link_identifier = '" . $row['q'] . "' ";
						$row_link = $db_read->queryOne($sql_row_link);
						
						?>	
				<tr onmouseover="set_tr_color('<?php print $id_element ?>', '', 'in', '<?php print $tables_elements ?>');" onmouseout="set_tr_color('<?php print $id_element ?>', '<?php print $td_color ?>', 'out', '<?php print $tables_elements ?>')" >
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $increment; ?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<strong>Link: <?php echo $row_link; ?></strong>
						<!-- <br><strong>md5: <?php echo $row['q']; ?></strong> -->
						<br>Factcheck: <a href="<?php echo $row_fact['factcheck_link']; ?>" target="_blank"><?php echo $row_fact['post_title']; ?></a>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $row_fact['post_date']; ?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php
						$sql_get_unique = 'SELECT COUNT(q) as no FROM api_factchecks';
						$sql_get_unique .= " WHERE q = '" . $row['q'] . "' ";
						$sql_get_unique .= " AND " .$cond;
						$sql_get_unique .= ' GROUP BY ip ORDER BY no DESC'; 	
						echo $db_read->queryOne($sql_get_unique);
						
						?>
					</td>
					<td class="<?php echo $td_class; ?>" id="<?php print ++$count_inside_fields ?>_<?php print $id_element ?>" > 
						<?php echo $row['no']; ?>
					</td>
				</tr>	
						
			<?php
					$i++;
				}
			}
			?>			
				
			</table>

			<?php if (is_array($page_data) && $page_num > 1) { ?>
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
			
		</div>
							
		<?php 
		break;

	default:
		break;
}
?>
						 
							
							
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
include_once(dirname(__FILE__)."/common/footer.inc.php");
?>                         