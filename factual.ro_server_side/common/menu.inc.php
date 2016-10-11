<?php
require_once(dirname(__FILE__) . "/../libraries/classes/tree_structure.class.php");
//require_once(dirname(__FILE__)."/../admin_libraries/classes/div_tree_structure.class.php");
//require_once(dirname(__FILE__)."/../admin_libraries/functions/url_generation.function.php");

$action = $_REQUEST['action'];

require_once(dirname(__FILE__) . "/../libraries/classes/celko_pear.class.php");
$celkoMenu = new CCelkoNastedSet();

$celkoMenu->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
$celkoMenu->FieldID = "id"; //Field name for table ID
$celkoMenu->FieldIDParent = "parent_id"; //Field name for table IDParent
$celkoMenu->FieldLeft = "NSLeft"; //Field name for table nasted set left field
$celkoMenu->FieldRight = "NSRight"; //Field name for table nasted set right field
$celkoMenu->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
$celkoMenu->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
$celkoMenu->FieldOrder = "NSOrder"; //Field name for table nasted set order field
$celkoMenu->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

$celkoMenu->InitializeTransaction();
$celkoMenu->BeginTransaction();

//------------- Begin Get Root Category (name='root_category')
$sql_get_root_category = "SELECT " . $celkoMenu->FieldID . " FROM " . $celkoMenu->TableName . " WHERE " . $celkoMenu->FieldLevel . "='0'";
$res_get_root_category = & $db_write->query($sql_get_root_category);


if ($row_get_root_category = & $res_get_root_category->fetchRow()) {
    $root_category_id = $row_get_root_category[$celkoMenu->FieldID];
}
if (empty($root_category_id)) {//daca nu exista root node, il inserez
    $othercols = "name='root_category', url_identifier='', visible='N'";
    $root_category_id = $celkoMenu->AddRootNode($othercols);
}
//	echo "<br>root_category_id = $root_category_id";
//------------- End Get Root Category

$main_category_text = '<span class="titles1"><strong>Administrative module</strong></span>#|#index.php';

$categories_tree = array();
$categories_tree[] = array("id" => $root_category_id, "text" => $main_category_text, "category_parent_id" => -1);
$rs_nodes = $celkoMenu->SelectSubNodesByZoneAndUser($user_info['id_user'], 1, $root_category_id); //iau numai categoriile de nivel 2(adica subcateg. root-ului)

if ($rs_nodes) {
    while ($row_nodes = & $rs_nodes->fetchRow(DB_FETCHMODE_ASSOC)) {
        $query_string = trim($row_nodes['get']);
        if ($cfg_array['htaccess_flag'] == "no" && strstr(trim(stripslashes($row_nodes['url'])), ".phpx") !== false) {
            $categories_tree[$row_nodes['id']] = array("id" => $row_nodes['id'], "text" => trim(stripslashes($row_nodes['name'])) . "#|#admin_blank.php?page_url=" . trim(stripslashes($row_nodes['url'])) . (!empty($query_string) ? '&' . $query_string : ''), "category_parent_id" => $row_nodes['parent_id']);
        } else {
            $categories_tree[$row_nodes['id']] = array("id" => $row_nodes['id'], "text" => trim(stripslashes($row_nodes['name'])) . "#|#" . trim(stripslashes($row_nodes['url'])) . (!empty($query_string) ? '?' . $query_string : ''), "category_parent_id" => $row_nodes['parent_id']);
        }
        unset($query_string);
    }
}

//echo '<pre>'; print_r($categories_tree); echo '</pre>';
//exit();

$celkoMenu->EndTransaction();
$categories_tree = array_values($categories_tree);
// name of the script
$self = basename($_SERVER['PHP_SELF']);
foreach ($_GET as $key => $value) {
    if ($key != "page_url" && $key != 'retract') {
        $get .= $key . "=" . $value . "&";
    }
}
$get = substr($get, 0, strlen($get) - 1);
// if target_id param is present, set it to $target_id or set -1 to $id if not
if ($self == "admin_blank.php") {
    $sql_get_target = "SELECT id FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='" . $_GET['page_url'] . "' AND `get`='" . $get . "'";
} else {
    $sql_get_target = "SELECT id FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='" . $self . "'  AND `get`='" . $get . "'";
}
$res_get_target = $db_write->query($sql_get_target);
//pentru cazul in care nu am .htacces si am pus manual de tipul admin_blank.php?page_url=<fisier>.phpx
if ($res_get_target->numRows() == 0) {
    $sql_get_target = "SELECT id FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='admin_blank.php?page_url=" . $_GET['page_url'] . "'";
    $res_get_target = $db_write->query($sql_get_target);
}
$row_get_target = $res_get_target->FetchRow();
$target_id = (is_numeric($row_get_target['id'])) ? $row_get_target['id'] : -1;

$treeMenu = new tree_structure($categories_tree, "id", "text", "category_parent_id", "design/images");
// transform the linear tab to the tab ordered in tree order
$treeMenu->transform($treeMenu->get_idroot());
//$treeMenu->expand_all();
//$treeMenu->expand_to($target_id);
//session_destroy();
//echo '<br>$target_id = '.$target_id;
if ($_GET['retract'] == 'y') {
    if (is_array($_SESSION['admin_module_expanded'])) {
        $children = $treeMenu->get_list_childs_DFS($target_id);
        //echo '<pre>'; print_r($children); echo '</pre>';
        $_SESSION['admin_module_expanded'] = array_diff($_SESSION['admin_module_expanded'], $children);
        $key = array_search($target_id, $_SESSION['admin_module_expanded']);
        //var_dump($key);
        if (is_int($key)) {
            unset($_SESSION['admin_module_expanded'][$key]);
        }
    }
} else {
    if (is_array($_SESSION['admin_module_expanded'])) {
        if (!in_array($target_id, $_SESSION['admin_module_expanded'])) {
            $_SESSION['admin_module_expanded'][] = $target_id;
        }
    } else {
        $_SESSION['admin_module_expanded'][] = $target_id;
    }
}
//echo "<pre>";print_r($_SESSION['admin_module_expanded']);echo "</pre>";
$treeMenu->expand_to_multiple($_SESSION['admin_module_expanded']);
?>
Hello: <b><?php echo $user_info['username'] ?></b>,
<input type='hidden' id='url'>
<div class="scroll" id='divcontainer'>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="menu_table">
<?php
$pages_url_array = array();
for ($y = 0; $y < $treeMenu->height(); $y = $treeMenu->get_next_line_tree($y)) {
    ?>
            <tr>
            <?php
            // the $tree_static_part part is the static part of tree
            // the $current_symbol part is the last part of the tree, the part which looks like + or - in windows looking tree
            // the $node_text part is the text of the node
            // the $current_category_id part is the id of the node
            list($tree_static_part, $current_symbol, $node_text, $current_category_id) = $treeMenu->get_line_display($y);
            $text = explode("#|#", $node_text);
            $pages_url_array[$current_category_id] = $text[1];
            $classLevel = $treeMenu->level_of($current_category_id) + 1;
            ?>
                <td class="menu_node_level_<?php echo $classLevel; ?>">
                    <div style="background:url(<?php echo $cfg_array['site_root_path']; ?>design/images/arbo_vert.gif)">
                        <div style="margin-left:<?php echo 20 * ($classLevel - 2); ?>px; background-color:#F3F3F3">
                <?php
                //se adauga imagini in functie de nr de nivele
                //echo $tree_static_part;
                if ($classLevel != 1) {
                    if ($target_id == $current_category_id) {
                        echo '<a class="link on" href="' . $cfg_array['site_root_path'] . $text[1] . '"  onclick="javascript:GetScrollPosition(\'' . $text[1] . '\');">' . $text[0] . '</a>';
                    } else {
                        echo '<a class="link" href="' . $cfg_array['site_root_path'] . $text[1] . '"  onclick="javascript:GetScrollPosition(\'' . $text[1] . '\');">' . $text[0] . '</a>';
                    }
                }
                if ($treeMenu->tree_tab[$y]["symbol"] == "plus") { // if node is "+" => expand it
; //echo '<a href="' . $text[1] . '">' .$current_symbol. '</a>';
                } else {
                    if ($treeMenu->tree_tab[$y]["symbol"] == "moins") // if node is "-" => expand to father
                        if ($pages_url_array[$treeMenu->tree_tab[$y]["id_father"]] != NULL) {
                            if (!strpos($pages_url_array[$treeMenu->tree_tab[$y]["id"]], '?')) {
                                echo '<a href="' . $cfg_array['site_root_path'] . $pages_url_array[$treeMenu->tree_tab[$y]["id"]] . '?retract=y" onclick="javascript:GetScrollPosition(\'' . $pages_url_array[$treeMenu->tree_tab[$y]["id"]] . '?retract=y\');"><img style="margin-left: 5px;" align="absmiddle" src="' . $cfg_array['site_root_path'] . 'design/images/minus.gif" border="0"></a>';
                            } else {
                                echo '<a href="' . $cfg_array['site_root_path'] . $pages_url_array[$treeMenu->tree_tab[$y]["id"]] . '&retract=y" onclick="javascript:GetScrollPosition(\'' . $pages_url_array[$treeMenu->tree_tab[$y]["id"]] . '&retract=y\');"><img style="margin-left: 5px;" align="absmiddle" src="' . $cfg_array['site_root_path'] . 'design/images/minus.gif" border="0"></a>';
                            }
                        } else {
                            ; //echo '<a href="admin_blank.php">'.$current_symbol.'</a>';
                        } else // else the node have static tree
                        ;
                    //echo $current_symbol;
                }
                //begin make down/up links
                $current_nb_brothers = count($treeMenu->get_brothers($current_category_id));
                $current_nb_children = count($treeMenu->get_list_childs($current_category_id));
                $father = end($treeMenu->get_fathers($current_category_id));
                $children = $treeMenu->get_list_childs($father);

                //print_r($children);
                foreach ($children as $key => $child) {
                    if ($current_category_id == $child) {
                        if (!empty($children[$key + 1])) {
                            $down_brother = $children[$key + 1];
                        } else {
                            $down_brother = '';
                        }
                        if (!empty($children[$key - 1])) {
                            $up_brother = $children[$key - 1];
                        } else {
                            $up_brother = '';
                        }
                    }
                }
                unset($num_articles);
                ?>
                        </div>
                    </div>
                </td>
            </tr>
                            <?php
                        }
                        ?>
        <tr>
            <td  height="16" class="menu_node_level_2" >
                <a class="link"  href="<?php echo $cfg_array['site_root_path']; ?>logout.php">Logout</a>
            </td>
        </tr>
        <tr>
            <td align="center">
                <br>
                <input type="button" onClick="window.print();" value="Print Page" class="input">
            </td>
        </tr>
    </table>
</div>
