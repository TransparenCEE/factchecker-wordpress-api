<?php
require_once(dirname(__FILE__) . "/common/constructor.inc.php");


require_once(dirname(__FILE__) . "/libraries/classes/tree_structure.class.php");
$action = $_REQUEST['action'];
require_once(dirname(__FILE__) . "/libraries/classes/celko_pear.class.php");

function search_if_exist_id_in_array($id, $array) {
    foreach ($array as $node) {
        if ($node['id'] == $id) {
            return true;
            break;
        }
    }
    return false;
}

$sql_blocked = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE url='admin_pages.php'";
$res_blocked = $db_write->query;
$celko2 = new CCelkoNastedSet();

$celko2->TableName = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages"; //Table name that contains nasted sets
$celko2->FieldID = "id"; //Field name for table ID
$celko2->FieldIDParent = "parent_id"; //Field name for table IDParent
$celko2->FieldLeft = "NSLeft"; //Field name for table nasted set left field
$celko2->FieldRight = "NSRight"; //Field name for table nasted set right field
$celko2->FieldDiffer = "NSDiffer"; //Field name used to manage more than one type of nasted set in the same table
$celko2->FieldLevel = "NSLevel"; //Field name for table nasted set level field  (0 = root node)
$celko2->FieldOrder = "NSOrder"; //Field name for table nasted set order field
$celko2->TransactionTable = "{$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_menu_celkotranstable"; // Name for table used to manage transactions

$celko2->InitializeTransaction();
$celko2->BeginTransaction();

//------------- Begin Get Root Category (name='root_category')
$sql_get_root_category = "SELECT " . $celko2->FieldID . " FROM " . $celko2->TableName . " WHERE " . $celko2->FieldLevel . "='0'";
$res_get_root_category = & $db_write->query($sql_get_root_category);

if ($row_get_root_category = & $res_get_root_category->fetchRow()) {
    $root_category_id = $row_get_root_category[$celko2->FieldID];
}
if (empty($root_category_id)) {//daca nu exista root node, il inserez
    $othercols = "name='root_category', is_visible='N'";
    $root_category_id = $celko2->AddRootNode($othercols);
}
//	echo "<br>root_category_id = $root_category_id";
//------------- End Get Root Category



$main_category_text = '<span class="titles1"><strong>Pages</strong></span>';

$array_categories_tree = $array_primary_nodes_data = $array_parents_ids = array();
$array_categories_tree[] = $array_primary_nodes_data[] = array("id" => $root_category_id, "text" => $main_category_text, "category_parent_id" => -1);
$rs_nodes = $celko2->SelectSubNodesNotInherited($root_category_id); //iau numai categoriile de nivel 2(adica subcateg. root-ului)
if ($rs_nodes) {
    while ($row_nodes = & $rs_nodes->fetchRow(DB_FETCHMODE_ASSOC)) {
        $array_primary_nodes_data[] = array("id" => $row_nodes['id'], "text" => trim(stripslashes($row_nodes['name'])) . "#|#" . trim(stripslashes($row_nodes['url'])), "category_parent_id" => $row_nodes['parent_id']);
    }
}
//print_r($array_primary_nodes_data);
// creez array_parents_ids pentru nodului deschis (contine inclusiv nodul deschis)
$expand_node = !empty($_GET['target_id']) ? $_GET['target_id'] : $root_category_id;
if (!search_if_exist_id_in_array($expand_node, $array_primary_nodes_data)) {
    $expand_node = $root_category_id;
}
$p_nodes = $celko2->SelectPath($expand_node);  // Returns the path from the IDNode to the root node
if ($p_nodes) {
    $nr_p = $p_nodes->numRows();
    while ($row_pnodes = & $p_nodes->fetchRow()) {
        $array_parents_ids[] = $row_pnodes['id']; //
    }
}

$celko2->EndTransaction();

if (!empty($array_primary_nodes_data) && is_array($array_primary_nodes_data)) {
    // costruirea array-ul optimizat (array_categories_tree)
    foreach ($array_primary_nodes_data as $key => $current_node) {
        //cazul 1
        if (
                ($current_node['id'] != $_GET['target_id']) &&
                (
                (($current_node['level'] == 1) && !(in_array($current_node['id'], $array_parents_ids))) ||
                (in_array($current_node['category_parent_id'], $array_parents_ids))
                )
        ) {
            if (!search_if_exist_id_in_array($current_node['id'], $array_categories_tree)) {
                $array_categories_tree[] = array("id" => $current_node['id'], "text" => trim(stripslashes($current_node['text'])), "category_parent_id" => $current_node['category_parent_id']);
            }

            foreach ($array_primary_nodes_data as $child) {
                if ($child['category_parent_id'] == $current_node['id']) {
                    if (!search_if_exist_id_in_array($child['id'], $array_categories_tree)) {
                        $array_categories_tree[] = array("id" => $child['id'], "text" => trim(stripslashes($child['text'])), "category_parent_id" => $child['category_parent_id']);
                    }
                    continue 2;
                }
            } //end foreach ($array_primary_nodes_data as $child)
        }
        //cazul 2
        if (in_array($current_node['id'], $array_parents_ids)) {
            if (!search_if_exist_id_in_array($current_node['id'], $array_categories_tree)) {
                $array_categories_tree[] = array("id" => $current_node['id'], "text" => trim(stripslashes($current_node['text'])), "category_parent_id" => $current_node['category_parent_id']);
            }
            foreach ($array_primary_nodes_data as $child_node) {   //
                if ($child_node['category_parent_id'] == $current_node['id']) {
                    if (!search_if_exist_id_in_array($child_node['id'], $array_categories_tree)) {
                        $array_categories_tree[] = array("id" => $child_node['id'], "text" => trim(stripslashes($child_node['text'])), "category_parent_id" => $child_node['category_parent_id']);
                    }
                }
            }
        } // end if (in_array($current_node['id'], $array_parents_ids))
    } // end foreach ($array_primary_nodes_data as $key=>$current_node)
}
//echo '<pre>'; print_r($array_primary_nodes_data); echo '</pre>';
//echo '<pre>'; print_r($array_categories_tree); echo '</pre>';
//echo '<pre>'; print_r($array_parents_ids); echo '</pre>';
//echo '<br>count = '.count($array_categories_tree);

unset($array_parents_ids, $array_primary_nodes_data);
// name of the script
$self = "admin_pages.php";

// if target_id param is present, set it to $target_id or set -1 to $id if not
$target_id = (isset($_GET['target_id'])) ? $_GET['target_id'] : -1;

$tree2 = new tree_structure($array_categories_tree, "id", "text", "category_parent_id", "design/images");

// transform the linear tab to the tab ordered in tree order
$tree2->transform($tree2->get_idroot());
//$tree2->expand_all();
$tree2->expand_to($target_id);

//var_dump($tree2);
//dynamic view. React to mouse click!
//echo $tree2->height();exit();
?>
<?php include_once(dirname(__FILE__) . '/common/header.inc.php'); ?>
<?php //echo($_GET['page_url']);   ?>
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
                                Admin pages
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="<?php echo $cfg_array['site_root_path']; ?>admin_pages_add_modify.php" class="white">Add page/a>
                            </span>
                        </DIV></TD>
                </TR>
                <!-- end section title -->
                <TR>
                <TR>
                    <TD align="center">
                        <table border='0' width="90%" cellspacing='0' cellpadding='0'>
<?php
for ($y = 0; $y < $tree2->height(); $y = $tree2->get_next_line_tree($y)) {
    ?>
                                <tr>
                                    <td height="16">
    <?php
    // the $tree2_static_part part is the static part of tree
    // the $current_symbol part is the last part of the tree, the part which looks like + or - in windows looking tree
    // the $node_text part is the text of the node
    // the $current_category_id part is the id of the node
    list($tree2_static_part, $current_symbol, $node_text, $current_category_id) = $tree2->get_line_display($y);
    echo $tree2_static_part;

    if ($tree2->tree_tab[$y]["symbol"] == "plus") { // if node is "+" => expand it
        echo "<a href=$self?target_id=$current_category_id>$current_symbol</a>";
    } else {
        if ($tree2->tree_tab[$y]["symbol"] == "moins") // if node is "-" => expand to father
            echo "<a href=$self?target_id=" . $tree2->tree_tab[$y]["id_father"], ">$current_symbol</a>";
        else // else the node have static tree
            echo $current_symbol;
    }
    $explode = explode("#|#", $node_text);
    $page = $explode[0];
    $link = $explode[1];
    echo '<div title="' . $link . '" style="display: inline;">' . $page . '</div>';
    //begin make down/up links
    $current_nb_brothers = count($tree2->get_brothers($current_category_id));
    $current_nb_children = count($tree2->get_list_childs($current_category_id));
    $father = end($tree2->get_fathers($current_category_id));
    $children = $tree2->get_list_childs($father);
    $brothers = $tree2->get_brothers($current_category_id);

    //echo "<pre>".print_r($brothers)."</pre>";
    //echo "<pre>".print_r($children)."</pre>";
    $current_order = 0;
    foreach ($children as $key => $child) {
        if ($current_category_id == $child) {
            $current_order = $key + 1;
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
    //echo '<br>$current_order = '.$current_order;
    //echo '&nbsp;&nbsp;current_category_id='.$current_category_id.' down_brother='.$down_brother.' up_brother='.$up_brother.' ';
    if (($current_category_id > 0) && ($current_category_id != $root_category_id)) {
        ?>
                                            &nbsp;(&nbsp;<a href="admin_pages_add_modify.php?category_id=<?php echo $current_category_id; ?>">Add subpage</a> &nbsp;&nbsp;<a href="admin_pages_add_modify.php?edit_id=<?php echo $current_category_id; ?>">Edit page</a>
                                            <?php
                                            if ($current_nb_children == 0) {
                                                ?>
                                                &nbsp;&nbsp;<a href="admin_pages_exec.php?target_id=<?php echo $_GET['target_id']; ?>&del_id=<?php echo $current_category_id; ?>" onClick='return window.confirm("Are you sure you want to delete this page?");'>Delete page</a>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (!empty($up_brother)) {
                                                ?>
                                                &nbsp;&nbsp;<a href="admin_pages_exec.php?target_id=<?php echo $_GET['target_id']; ?>&id_page1=<?php echo $current_category_id; ?>&id_page2=<?php echo $up_brother; ?>&action=move"><img src="<?php echo $cfg_array['site_root_path']; ?>design/images/up.gif" border="0"></a>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (!empty($down_brother)) {
                                                ?>
                                                &nbsp;&nbsp;<a href="admin_pages_exec.php?target_id=<?php echo $_GET['target_id']; ?>&id_page1=<?php echo $current_category_id; ?>&id_page2=<?php echo $down_brother; ?>&action=move"><img src="<?php echo $cfg_array['site_root_path']; ?>design/images/down.gif" border="0"></a> )
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
        <?php
        $sql_inherit = "SELECT * FROM {$cfg_db_array['db_main']}." . $cfg_array['table_prefix'] . "_pages WHERE id_page_inherited_rights='" . $current_category_id . "'";
        $res_inherit = $db_write->query($sql_inherit);
        ?>
                                    <tr>
                                        <td style="color: #FF0000;font-size: 10px;padding-left: <?php echo ($tree2->level_of($current_category_id) * 1.3); ?>cm;">
        <?php
        while ($row_inherit = $res_inherit->fetchRow()) {
            ?>( <?php echo $row_inherit['name']; ?>&nbsp;&nbsp;&nbsp;<a href="admin_pages_add_modify.php?edit_id=<?php echo $row_inherit['id']; ?>">Edit page</a>&nbsp;&nbsp;&nbsp;<a href="admin_pages_exec.php?target_id=<?php echo $_GET['target_id']; ?>&del_id=<?php echo $row_inherit['id']; ?>">Delete page</a> )<?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
        <?php
    }
    unset($num_articles);
}
?>
                        </table>
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