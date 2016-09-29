<?php

	function adminFactcheckList($id_factcheck, $ID,  $db_read ) {
		$msg = '';
		
		$sql_get_links = "SELECT * FROM factcheck_content2links WHERE id_factcheck = '{$id_factcheck}' AND status = 'active' ORDER BY id_link ASC";
//		echo $sql_get_links;
		$array_links = $db_read->queryAll($sql_get_links);
		if(!empty($array_links)) { 	
			$count_links = 1;
			$linksJs = '';
			foreach($array_links as $key_l=>$link_row) {
//				echo '<pre>'; print_r($link_row); echo '</pre>';
				$msg .= '<table  id="link_div_' . $id_factcheck . '_' . $link_row['id_link'] . '" class="grid" style="margin:0px;">';
				$msg .= '	<tr><td rowspan="3" width="2%" style="border-bottom: 2px solid #dddddd;margin:0px;">' . $count_links . '</td>';
				$msg .='		<td align="left">';
				$msg .= '			<i>Link url: </i>';
				$msg .='			<a id="link_' . $id_factcheck . '_' . $link_row['id_link'] . '" href="' . $link_row['link_content'] . '" target="_blank" id="link_href_' . $link_row['id_link'] . '" class="check2enable">' . $link_row['link_content'] . '</a>';
//				$msg .='			<input name="link" id="update_link_' . $id_factcheck . '_' . $link_row['id_link'] . '" value="' . $link_row['link_content'] . '" style="display:none;min-width:370px" class="check2disable" />';
				$msg .='			<span id="msg_modify_link_' . $id_factcheck . '_' . $link_row['id_link'] . '" style="margin-top:5px;" class="msgcheck2disable"></span>';
				$msg .='        </td>       ';
				$msg .='		<td rowspan="3" width="15%" align="center" style="border-bottom: 2px solid #dddddd;margin:0px;">';
				$msg .='			<a href="javascript: disableAllInputs(\'' . $id_factcheck . '\'); enable_input(\'' . $id_factcheck . '\', \'' . $link_row['id_link'] . '\');hideCsvForm(\'' . $id_factcheck . '_' . $ID . '\');" title="Edit"><img src="design/images/btn_edit.gif" alt="Edit" /></a>';
//				$msg .='			<a href="javascript: enable_input(\'' . $id_factcheck . '\', \'' . $link_row['id_link'] . '\')" title="Edit"><img src="design/images/edit.gif" alt="Edit" /></a>';
				$msg .= '			&nbsp;  &nbsp;';
				$msg .= '			<a href="javascript: add_edit_factcheck(\'' . $id_factcheck . '\', \'disable_link\', \'' . $ID . '\', \'' . $link_row['id_link'] . '\');" title="Disable"><img src="design/images/btn_delete.gif" alt="Disable" /></a>';
				$msg .='		</td>';
				$msg .='	</tr>';
				$msg .='	<tr>';
				$msg .='		<td align="left">';
				$msg .='			<i>Snippet:</i>';
				$msg .='			<span id="snippet_' . $id_factcheck . '_' . $link_row['id_link'] . '" class="check2enable">' . (($link_row['snippet'] == '' ) ? '' : $link_row['snippet']) . '</span>';
				$msg .='			<textarea name="snippet" id="update_snippet_' . $id_factcheck . '_' . $link_row['id_link'] . '" style="display:none;min-width:370px;"  class="check2disable">' . $link_row['snippet'] . '</textarea> ';
				$msg .= '	</td><tr><td colspan="2" align="left" style="border-bottom: 2px solid #dddddd;margin:0px;">';
				$msg .='			<input onclick="add_edit_factcheck(\'' . $id_factcheck . '\', \'modify_link\', \'' . $ID . '\', \'' . $link_row['id_link'] . '\');" value="Modify" id="go_update_' . $id_factcheck . '_' . $link_row['id_link'] . '"  style="display:none;margin-left:45px;cursor:pointer;width:60px;text-align:center;" class="input check2disable"/>';
				$msg .= '			<input  onclick="reset_add_edit_factcheck(\'' . $id_factcheck . '_' . $link_row['id_link'] . '\');" value="Reset"  id="go_reset_' . $id_factcheck . '_' . $link_row['id_link'] . '" style="display:none;margin-left:45px;cursor:pointer;width:60px;text-align:center;" class="input check2disable"/>';
				$msg .='		</td>';
				$msg .='	</tr>';
				$msg .='</table>';
				$count_links++;
				$linksJs .= $link_row['id_link'] . ',';
			}
		} else { 
			$msg .='<span id="emptyLinksList_' . $id_factcheck . '_' . $link_row['id_link'] . '">No links</span>';

		} 

		$msg .= '';
		return $msg;
	}
	
