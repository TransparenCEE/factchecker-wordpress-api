<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CLASS NAME:  TREE_STRUCTURE                                                                                               //
// LANGUAGE:    PHP                                                                                                //
// AUTHOR:      Penseur Martien                                                                                    //
// EMAIL:       penseur.martien<no spam>@@ifrance.com                                                              //
// VERSION:     1.3                                                                                               //
// DATE:        06/08/2003                                                                                         //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// History:                                                                                                        //
//----------                                                                                                       //
//  Date        Version   Actions                                                                                  //
// --------------------------------------------------------------------------------------------------------------- //
//  06/08/2003  1.0       Final version                                                                            //
//  12/08/2003  1.1       Class now doesn't need the level of item                                                 //
//  28/08/2003  1.11      Can choose between text mode and graphic mode in the constructor                         //
//  11/12/2003  1.2       add get_path function to return the path to an item                                      //
//  12/12/2003  1.21      get_path function now return full text instead of id                                     //
//  26/12/2003  1.3       get_line_display($y) return the 4 part of what to display in line $y:                    //
//                          static tree, symbol before text, text and id                                           //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Need to work: No other file / documents                                                                         //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// What the class need:                                                                                            //
// * a 2D assoc array, with at least the column: id, text, level of the text and the father of the text            //
// * the names of the column id, text, level, father                                                               //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// What the class do:                                                                                              //
// * Transform the 2D linear array into a 2D array ordered in the order of the tree, and other infos in each line: //
//   * id of the node                                                                                              //
//   * text of the node                                                                                            //
//   * the symbol of the node: a plus, a minus, or other                                                           //
//   * the number of childs of the node                                                                            //
//   * the list in an array of the childs                                                                          //
//   * the rest of the pictures of the tree, to place before the symbole                                           //
// * React to expand / collapse action to nodes                                                                    //
// * export a static view of the tree (without for example the link which can launch the collapse/expand action)   //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class tree_structure {
	var $images_dir;
	var $linear_tab;
	var $tree_tab;
	var $symboles;
	var $id_column,$text_column,$father_column,$level_column;

	// constructor
	function tree_structure($tab,$a_id_column,$a_text_column,$a_father_column,$images_dir,$mode="graphic") {

		$this->images_dir=$images_dir;
		if ($mode=="graphic")
		$this->symboles=array(	"vert"=>"<img src='$this->images_dir/arbo_vert.gif'     border=0 vspace=0 hspace=0 align=absbottom>",
		"plus"=>"<img src='$this->images_dir/arbo_plus.gif'     border=0 vspace=0 hspace=0 align=absbottom>",
		"moins"=>"<img src='$this->images_dir/arbo_moins.gif'   border=0 vspace=0 hspace=0 align=absbottom>",
		"vide"=>"<img src='$this->images_dir/arbo_vide.gif'     border=0 vspace=0 hspace=0 align=absbottom>",
		"angle"=>"<img src='$this->images_dir/arbo_angle.gif'   border=0 vspace=0 hspace=0 align=absbottom>",
		"milieu"=>"<img src='$this->images_dir/arbo_milieu.gif' border=0 vspace=0 hspace=0 align=absbottom>");
		else
		$this->symboles=array(	"vert"=>"&nbsp;|&nbsp;",
		"plus"=>"&nbsp;+&nbsp;",
		"moins"=>"&nbsp;-&nbsp;",
		"vide"=>"&nbsp;&nbsp;&nbsp;",
		"angle"=>"&nbsp;`-",
		"milieu"=>"&nbsp;|-");
		$this->id_column=$a_id_column;
		$this->text_column=$a_text_column;
		$this->father_column=$a_father_column;
		$this->level_column="level";

		for ($i=0;$i<count($tab);$i++)
		$this->linear_tab[$i]=array($this->id_column		=>$tab[$i][$this->id_column],
		$this->text_column	=>$tab[$i][$this->text_column],
		$this->father_column=>$tab[$i][$this->father_column]);

		for ($i=0;$i<count($tab);$i++)
		$this->linear_tab[$i][$this->level_column]=$this->level_of($this->linear_tab[$i][$this->id_column]);
	}
	// Get the line number where to find the node $id in linear_tab
	function get_line_of($id) {
		for ($i=0;$i<count($this->linear_tab);$i++)
		if ($this->linear_tab[$i][$this->id_column]==$id)
		return $i;
	}

	// get the line number where to find the node $id in tree_tab
	function get_line_in_tree($id) {
		for ($i=0;$i<count($this->tree_tab);$i++)
		if ($this->tree_tab[$i]["id"]==$id)
		return $i;
	}

	// list in a array of the $id of the child
	function get_list_childs($id) {
		$res = array();
		for ($i=0;$i<count($this->linear_tab);$i++)	{
			if ($this->linear_tab[$i][$this->father_column]==$id)
			$res[]=$this->linear_tab[$i][$this->id_column];
		}
		return $res;
	}
	//functie de DFS facuta de Ioana
	/*
	function get_list_childs_DFS($id) {
	//echo '<pre>';print_r($this->linear_tab); echo '</pre>';die();
	$parents_array = array();
	$parents_array[] = $id;
	$res=array();
	for ($i=0;$i<count($this->linear_tab);$i++){
	if (in_array($this->linear_tab[$i][$this->father_column], $parents_array)){
	$res[]=$this->linear_tab[$i][$this->id_column];
	if (!in_array($this->linear_tab[$i][$this->id_column], $parents_array)){
	$parents_array[] = $this->linear_tab[$i][$this->id_column];
	}
	}
	}
	//echo '<pre>';print_r($res); echo '</pre>';
	return $res;
	}
	*/

	// list in a array with the ids of children,grandchildren, and so on... (recursive)
	function get_list_childs_DFS($id, $valuesArray = NULL) {
		global $res;
		//$res = array();
		for ($i=0;$i<count($this->linear_tab);$i++)	{
			if ($this->linear_tab[$i][$this->father_column]==$id)	{
				$res[]=$this->linear_tab[$i][$this->id_column];
				$this->get_list_childs_DFS($this->linear_tab[$i][$this->id_column]);
			}
		}
		return $res;
	}

	// number of childs of the $id
	function nb_childs($id) {
		return count($this->get_fathers($id));
	}

	// get the level of the item $id
	function level_of($id) {
		$res=0;
		while ($this->linear_tab[$this->get_line_of($id)][$this->father_column]!=-1) {
			$res++;
			$id=$this->linear_tab[$this->get_line_of($id)][$this->father_column];
		}
		return $res;
	}

	// list in array the father, grand-father, etc... of the $id
	function get_fathers($id) {
		$niveau=$this->linear_tab[$this->get_line_of($id)][$this->level_column];
		for ($temp=array(),$i=$niveau;$i!=0;$i--) {
			$id=$this->linear_tab[$this->get_line_of($id)][$this->father_column];
			$temp[$i-1]=$id;
		}
		for ($res=array(),$i=0;$i<count($temp);$i++)
		$res[$i]=$temp[$i];
		return $res;
	}

	// collapse the node $id
	function collapse($id) {
		$this->tree_tab[$this->get_line_in_tree($id)]["symbol"]="plus";
	}

	// collapse the entire tree
	function collapse_all() {
		for ($i=1;$i<count($this->tree_tab);$i++)
		if ($this->tree_tab[$i]["symbol"]=="moins")
		$this->tree_tab[$i]["symbol"]="plus";
	}

	// expand the node $id
	function expand($id) {
		$this->tree_tab[$this->get_line_in_tree($id)]["symbol"]="moins";
	}

	// try to expand from the root to the node $id
	function expand_to($id) {
		$this->collapse_all(); // on retrecie d'abord tout
		$ascendants=$this->get_fathers($id);
		$ascendants[]=$id;
		for ($i=1;$i<count($ascendants);$i++) {
			$nb_childs=$this->tree_tab[$this->get_line_in_tree($ascendants[$i])]["nb_childs"];
			if ($nb_childs>0)
			$this->expand($ascendants[$i]);
			else
			$i=count($ascendants);
		}
	}

	//expand multiple lines
	function expand_to_multiple($ids) {
		$this->collapse_all(); // on retrecie d'abord tout
		if(is_array($ids))	{
			foreach($ids as $id)	{
				$ascendants=$this->get_fathers($id);
				$ascendants[]=$id;
				for ($i=1;$i<count($ascendants);$i++) {
					$nb_childs=$this->tree_tab[$this->get_line_in_tree($ascendants[$i])]["nb_childs"];
					if ($nb_childs>0)
					$this->expand($ascendants[$i]);
					else
					$i=count($ascendants);
				}
			}
		}
	}

	// expand the entire tree
	function expand_all() {
		for ($i=0;$i<count($this->tree_tab);$i++)
		if ($this->tree_tab[$i]["symbol"]=="plus")
		$this->tree_tab[$i]["symbol"]="moins";
	}

	// get the id of the root, i.e. where id_pere=-1
	function get_idroot() {
		for ($i=0;$i<count($this->linear_tab);$i++)
		if ($this->linear_tab[$i][$this->father_column]==-1)
		return $this->linear_tab[$i][$this->id_column];
	}

	// width of the expanded tree
	function width() {
		for ($x=-1,$i=0;$i<count($this->tree_tab);$i=$this->get_next_line_tree($i))
		$x=max($x,$this->tree_tab[$i]["level"]);
		return $x;
	}

	// total height of the expanded tree
	function height() {
		return count($this->tree_tab);
	}

	// echo a static tree (you have do make you function to make collapse/expand action)
	function view_tree() {
		echo "<table border='0' cellspacing='0' cellpadding='0'>\n";
		for ($y=0;$y<$this->height();$y=$this->get_next_line_tree($y)) {
			echo "  <tr>\n";
			echo "    <td height=16><font size=1>\n      ";
			list($a,$b,$c,$d)=$this->get_line_display($y); // the $b part is the last part of the tree, the part which looks like + or - in windows looking tree
			echo $a,$b,$c; // for dynamic tree, just react to clic on $b part by putting a href link to expand or collapse tree
			echo "\n    </font></td>\n";
			echo "  </tr>\n";
		}
		echo "</table>\n";
	}
	// return the 4 parts of a line to display: static part of tree, last part of tree (this one have to react to mouse click), text, and id of the node
	function get_line_display($y) {
		$arbre=$this->tree_tab[$y]["tree"];
		// mai jos in for a fost modificat $i sa plece de la 1 .Initial pleca de la 0
		for ($i=1,$res=array(),$res[0]="";$i<count($arbre);$i++)	{
			$res[0].=$this->symboles[$arbre[$i]];
		}
		$res[1]=$this->symboles[$this->tree_tab[$y]["symbol"]];
		$res[2]=$this->tree_tab[$y]["text"];
		$res[3]=$this->tree_tab[$y]['id'];
		return $res;
	}
	// get the next line in the array tree_tab, depending of the collapse/expand node
	function get_next_line_tree($l) {
		//debug($l);
		if ($this->tree_tab[$l]["symbol"]!="plus")
		return $l+1;
		else // c'est q'il y a un plus -> sauter autant de ligne qu'il y a de fils cach�s
		for ($i=$l+1;$i<$this->height();$i++)
		if ($this->tree_tab[$i]["level"]<=$this->tree_tab[$l]["level"]) // d�s que le niveau est inf�rieur ou �gal
		return $i;
		return $this->height();
	}

	// transform the linear array in a 1D array in the order of the tree, with the info
	function transform($id_depart) {
		$ligne=$this->get_line_of($id_depart);
		$tab_fils=$this->get_list_childs($id_depart);
		$nb_fils=count($tab_fils);
		$niveau=$this->linear_tab[$ligne][$this->level_column];
		$id_pere=$this->linear_tab[$ligne][$this->father_column];
		$texte=$this->linear_tab[$ligne][$this->text_column];

		if($id_pere==-1){
			$symbole = '';
		}else{
			if ($nb_fils>0)
			$symbole="moins";
			else {
				$temp=$this->get_list_childs($id_pere); // liste des freres
				$symbole=($id_depart==$temp[count($temp)-1])?"angle":"milieu";
			}
		}
		// reste of tree
		$ascendants=$this->get_fathers($id_depart);
		$arbre=array();
		for ($i=0;$i<count($ascendants);$i++) {
			$freres=$this->get_brothers($ascendants[$i]);
			$arbre[$i]=($ascendants[$i]==$freres[count($freres)-1])?"vide":"vert";
		}

		$this->tree_tab[]=array("id"=>$id_depart,
		"symbol"=>$symbole,
		"text"=>$texte,
		"nb_childs"=>count($tab_fils),
		"level"=>$niveau,
		"id_father"=>$id_pere,
		"childs"=>$tab_fils,
		"tree"=>$arbre);
		for ($i=0;$i<count($tab_fils);$i++)
		$this->transform($tab_fils[$i]);
	}

	// get the list of the brothers of $id (include $id)
	function get_brothers($id) {
		$id_pere=$this->linear_tab[$this->get_line_of($id)][$this->father_column];
		return $this->get_list_childs($id_pere);
	}
	// get a path text to an item like "root -> item1 -> item11 -> item111"
	function get_path($id_item,$separator) {
		$ids=$this->get_fathers($id_item);
		for ($i=0;$i<count($ids);$i++)
		$temp[]=$this->tree_tab[$this->get_line_in_tree($ids[$i])]["text"];
		$temp[]=$this->tree_tab[$this->get_line_in_tree($id_item)]["text"];
		return implode($separator,$temp);
	}
}

?>