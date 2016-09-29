<?php
require_once(dirname(__FILE__)."/audit.function.php");
function admin_autentification($cookie_user_login){
	$defaultPagesArray = array('admin_blank.php', 'index.php', 'login.php');
	global $db_write, $cfg_array,$cfg_db_array;
	$db_write = $db_write;
	if(!empty($cookie_user_login)){
		$sql_auth = "
			SELECT id_user, username, password
			FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users
			WHERE cookie_user_login='".$db_write->escape($cookie_user_login)."' AND is_active='Y'
		";

		//echo "<br>$sql_auth";
		$res_auth =& $db_write->query($sql_auth);
		//errors_handler($res_auth);
		if ($row_auth =& $res_auth->fetchRow()){

			$id_user = $row_auth['id_user'];
			$username = $row_auth['username'];
			if(!empty($id_user)){


				$sql_get_user_pages = "
					SELECT DISTINCT a.name, a.url
					FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_pages a
					INNER JOIN {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users_2_pages as b
					ON a.id=b.page_id
					WHERE b.id_user='$id_user' AND a.is_blocked='N'
					GROUP BY a.id
				";
				//echo $sql_get_user_pages;exit();
				$res_get_user_pages = & $db_write->query($sql_get_user_pages);
				//errors_handler($res_get_user_pages);
				$user_pages_array = array();
				foreach($defaultPagesArray as $value)	{
					$user_pages_array[] = $value;
				}

				while($row_get_user_pages = & $res_get_user_pages->fetchRow()){
					$user_pages_array[] = trim(stripslashes($row_get_user_pages['url']));
				}

				if($_SERVER['REQUEST_URI']==($cfg_array['site_root_path'])){
					$page = "index.php";
				}else{
					$explode = explode("?", basename($_SERVER['REQUEST_URI']));
					$page = $explode[0];
				}
				//echo '<pre style="border: 1px dashed;background-color: #EFEFEF;">';print_r($user_pages_array);echo '</pre>';
				
				//echo "work/".$page;exit;
				
				if(!in_array($page, $user_pages_array) && !in_array('work/'.$page, $user_pages_array)){
					//caut daca pagina in care sunt are inherited rights
					$sql_check_inherit = "SELECT id_page_inherited_rights FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_pages 
						WHERE url='" .$page. "' OR url='work/" .$page. "'";
					//echo $sql_check_inherit;exit();
					$res_check_inherit = $db_write->query($sql_check_inherit);
					if($res_check_inherit->numRows() > 0)	{
						$row_check_inherit = $res_check_inherit->fetchRow();
						$inheritedId = $row_check_inherit['id_page_inherited_rights'];
						$sql_check_rights = "
							SELECT * FROM {$cfg_db_array['db_main']}.".$cfg_array['table_prefix']."_users_2_pages
							WHERE id_user='" .$id_user. "' AND page_id='" .$inheritedId. "'
						";
						$res_check_rights = $db_write->query( $sql_check_rights );
						if($res_check_rights->numRows() == 0)	{
							header("Location: ".$cfg_array['site_url']."login.php");
							exit();
						}
					}
					else	{
						header("Location: ".$cfg_array['site_url']."login.php");
						exit();
					}
				}
				//scriu in audit
				adminAudit($id_user, $page);
				$return_array = array('id_user'=>$id_user);
				$return_array['username'] = $username;
				return $return_array;
			}
		}
	}
	//setcookie($cfg_array['cookie_user_login'], '', time()-3600, "/"); //delete cookie
	setcookie("admin_cookie_login_next_url_engine", $_SERVER['REQUEST_URI'], 0, "/");
	if(isset($_SESSION[$cfg_array['cookie_user_login']])){
		unset($_SESSION[$cfg_array['cookie_user_login']]);
	}

	$url = $cfg_array['site_url'].'login.php';
	header("Location: $url");
	exit();
	return false;
}
?>