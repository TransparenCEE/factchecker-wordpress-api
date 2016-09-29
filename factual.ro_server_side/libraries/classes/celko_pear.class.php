<?php
/*
CCelkoNastedSet class

This class is meant to manage tree database structure using Jeo Celko's nested sets approach.

You can add node with 7 queries, delete a node with subnodes with 4 queries and move node with 11 queries.

The number of queries per action does not depend on the number of sub-nodes, so it is a scalable solution.

Thanks to Kirill Hryapin kx@chl.ru for finding and solving bugs into the MoveNode() method.

Members
$TableName => Table name that contains nasted sets
$FieldID => Field name for table ID
$FieldIDParent => Field name for table IDParent
$FieldLeft => Field name for table nasted set left field
$FieldRight => Field name for table nasted set right field
$FieldDiffer => Field name used to manage more than one type of nasted set in the same table
$FieldLevel => Field name for table nasted set level field  (0 = root node)
$FieldOrder => Field name for table nasted set order field
$FieldIgnore => Field name for tablr nested set ignore field

$TransactionTable => Name for table used to manage transactions

Methods
SelectSubNodes () => Returns all the sub node of IDNode fo Level number of level.
SelectPath () => Returns the path from the IDNode to the root node.
MoveNode () => Method used to move the node IDNode to IDParent.
AddRootNode () => Method used to add the root node for a nested set
AddNode () => Method used to add a node to IDParent. Return the new IDNode.
DeleteNode () => Method used to delete the node identified by IDNode and all his children.
ClearNodes () => Method used to clear the nasted set table

BeginTransaction () => Method that start the transaction. Returns true or false.
EndTransaction () => Method that stop the transaction.
IsInTransaction () => Method to check the transaction state. Returns true or false.

SQL STRUCTURE
The structure of tblCelkoTransTable must me mainteined, the structure of tblNastedSets could be modified,
you can add columns but you must maintain the NSFields needed for celko's nasted sets.

CREATE TABLE tblCelkoTransTable (
IDTransaction INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
TableName TINYTEXT NULL,
Differ TINYTEXT NULL,
InTransaction BIT NULL,
TStamp TIMESTAMP NULL,
PRIMARY KEY(IDTransaction)
);

CREATE TABLE tblNastedSets (
IDNode INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
IDParent INTEGER UNSIGNED NULL DEFAULT '0',
NSLeft INTEGER UNSIGNED NULL DEFAULT '0',
NSRight INTEGER UNSIGNED NULL DEFAULT '0',
NSLevel INTEGER UNSIGNED NULL DEFAULT '0',
NSOrder INTEGER UNSIGNED NULL DEFAULT '1',
NSDiffer TINYTEXT NULL,
NSIgnore INTEGER UNSIGNED NULL DEFAULT '0',
PRIMARY KEY(IDNode)
);

*/

class CCelkoNastedSet {
	var $TableName;
	var $TablePrefix;
	var $FieldID;
	var $FieldIDParent;
	var $FieldLeft;
	var $FieldRight;
	var $FieldDiffer;
	var $FieldLevel;
	var $FieldOrder;
	var $FieldIgnore;

	var $TransactionTable;
	var $_IsInTransaction;
	var $_TransactionTStamp;
	var $db_write;



	function CCelkoNastedSet ()
	{
		global $db_write,$cfg_db_array;
		$this->TableName = "tblCelkoTree";
		$this->TablePrefix = "{$cfg_db_array['db_main']}.admin";
		$this->FieldID = "IDNode";
		$this->FieldIDParent = "IDParent";
		$this->FieldLeft = "NSLeft";
		$this->FieldRight = "NSRight";
		$this->FieldDiffer = "NSDiffer";
		$this->FieldLevel = "NSLevel";
		$this->FieldOrder = "NSOrder";
		$this->FieldIgnore = "NSIgnore";

		$this->TransactionTable = "tblCelkoTransTable";
		$this->_IsInTransaction = false;
		$this->_TransactionTStamp = 0;

		$this->db = $db_write;
	}

	// Begin private functions //
	function _safe_set (&$var_true, $var_false = "")
	{
		if (!isset ($var_true))
		{ $var_true = $var_false; }
	}

	function _safe_query ($query)
	{
		global $db_write;
		//echo '<br>'.$query;
		if (empty($query)) { return false; }
		$result =& $this->db->query($query);
		return $result;
	}
	// End private functions //

	// Begin transaction functions //
	function InitializeTransaction ($Differ = "")
	{
		$sql_verify = "SELECT * FROM ".$this->TransactionTable." WHERE TableName = '".$this->TableName."' "." AND Differ = '".$Differ."'";
		$rs_verify = $this->_safe_query ($sql_verify);
		if (($rs_verify) && ($rs_verify->numRows() == 0))
		{
			$rs_verify->free();
			$sql_insert = "
				INSERT INTO ".$this->TransactionTable."
				SET TableName='".$this->TableName."', Differ='".$Differ."', InTransaction='0' ";
			$this->_safe_query ($sql_insert);
			return true;
		}
		else
		{ return false; }
	}

	function BeginTransaction ($Differ = "")
	{
		$TStamp = date ("YmdHis");

		//$sql_update = "UPDATE ".$this->TransactionTable." SET TStamp = ".$TStamp.", "." InTransaction = 1 "." WHERE InTransaction = 0 "." AND TableName = '".$this->TableName."' "." AND Differ = '".$Differ."'";
		$sql_update = "UPDATE ".$this->TransactionTable." SET TStamp = ".$TStamp.", "." InTransaction = 1 "." WHERE TableName = '".$this->TableName."' "." AND Differ = '".$Differ."'";
		$this->_safe_query ($sql_update);

		$sql_verify = "SELECT * FROM ".$this->TransactionTable." WHERE TableName = '".$this->TableName."' "." AND InTransaction = 1 "." AND TStamp = ".$TStamp." AND Differ = '".$Differ."'";
		$rs_verify = $this->_safe_query ($sql_verify);

		if (($rs_verify) && ($rs_verify->numRows() == 1))
		{
			$rs_verify->free();
			$this->_IsInTransaction = true;
			$this->_TransactionTStamp = $TStamp;
			return true;
		}
		else
		{
			$this->_IsInTransaction = false;
			$this->_TransactionTStamp = 0;
			return false;
		}
	}

	function EndTransaction ($Differ = "")
	{
		$sql_update = "UPDATE ".$this->TransactionTable." SET InTransaction = 0 "." WHERE TableName = '".$this->TableName."' "." AND TStamp = ".$this->_TransactionTStamp." AND Differ = '".$Differ."'";
		$this->_safe_query ($sql_update);
		$this->_IsInTransaction = false;
	}

	function IsInTransaction ()
	{ return $this->_IsInTransaction; }
	// End transaction  functions //

	// Begin nasted set functions //
	function ClearNodes ($Differ = "")
	{
		$sql_delete = "DELETE FROM ".$this->TableName." WHERE ".$this->FieldDiffer." = '".$Differ."'";
		$this->_safe_query ($sql_delete);
	}

	function DeleteNode ($IDNode = -1, $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'";
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];

			// Delete sub nodes
			$sql_delete = "DELETE FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldLeft." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_delete);

			// Update FieldLeft
			$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldLeft." = ".$this->FieldLeft." - ".($delete_offset + 1)." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_update);

			// Update FieldRight
			$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldRight." = ".$this->FieldRight." - ".($delete_offset + 1)." WHERE ".$this->FieldRight." > ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_update);

			$rs_select->free();

			return true;
		}
		else
		{ return false; }
	}

	function AddRootNode ($othercols="", $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }
		if (strlen($othercols)>0){$othercols .= ",";}

		$next_id = $this->db->nextId($this->TableName);
		$this->next_id = $next_id;
		$sql_insert = "
			INSERT INTO ".$this->TableName."
			SET $othercols  ".$this->FieldID."=".$next_id.", ".$this->FieldIDParent."=0, ".$this->FieldLeft."=1,
			".$this->FieldRight."=2, ".$this->FieldLevel."=0,
			".$this->FieldOrder."=1, ".$this->FieldDiffer."='".$Differ."'";
		$this->_safe_query ($sql_insert);
		return $next_id;
	}

	function AddNode ($IDParent = -1, $othercols="", $Order = -1, $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }
		if (strlen($othercols)>0){$othercols .= ",";}

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDParent." AND ".$this->FieldDiffer." = '".$Differ."'";
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);

			$left = $row_select[$this->FieldLeft] + 1;

			// Update Order (set order = order +1 where order>$Order)
			if ($Order == -1)
			{
				$sql_order = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldOrder." DESC "." LIMIT 0,1";
				$rs_order = $this->_safe_query ($sql_order);
				if (($rs_order) && ($row_order =& $rs_order->fetchRow(DB_FETCHMODE_ASSOC)))
				{
					$this->_safe_set ($row_order[$this->FieldOrder], 0);
					$Order = $row_order[$this->FieldOrder] + 1;
					$rs_order->free();
				}
				else
				{ $Order = 1; }
			}

			$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldOrder." = ".$this->FieldOrder." + 1"." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldOrder." >= ".$Order." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_update);

			$sql_order = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldOrder." <= ".$Order." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldOrder." DESC "." LIMIT 0,1";
			$rs_order = $this->_safe_query ($sql_order);
			if (($rs_order) && ($row_order =& $rs_order->fetchRow(DB_FETCHMODE_ASSOC)))
			{
				$this->_safe_set ($row_order[$this->FieldRight], -1);
				$left = $row_order[$this->FieldRight] + 1;
				$rs_order->free();
			}

			$right = $left + 1;

			// Update FieldLeft
			$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldLeft." = ".$this->FieldLeft." + 2"." WHERE ".$this->FieldLeft." >= ".$left." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_update);

			// Update FieldRight
			$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldRight." = ".$this->FieldRight." + 2"." WHERE ".$this->FieldRight." >= ".$left." AND ".$this->FieldDiffer." = '".$Differ."'";
			$this->_safe_query ($sql_update);

			$next_id = $this->db->nextId($this->TableName);
			$this->next_id = $next_id;
			// Insert
			$sql_insert = "
				INSERT INTO ".$this->TableName."
				SET $othercols ".$this->FieldID."=".$next_id.", ".$this->FieldIDParent."=".$IDParent.", ".$this->FieldLeft."=".$left.", ".$this->FieldRight."=".$right.",
				".$this->FieldLevel."=".($row_select[$this->FieldLevel] + 1).", ".$this->FieldOrder."=".$Order.",
				".$this->FieldDiffer."='".$Differ."'";
			$this->_safe_query ($sql_insert);

			$rs_select->free();

			return $next_id;
		}
		else
		{ return false; }
	}

	function MoveNode ($IDNode = -1, $IDParent = -1, $othercols='', $Order = -1, $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			$delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];


			$sql_select_parent = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDParent." AND ".$this->FieldDiffer." = '".$Differ."'";

			$rs_select_parent = $this->_safe_query ($sql_select_parent);
			if (($rs_select_parent) && ($row_select_parent =& $rs_select_parent->fetchRow(DB_FETCHMODE_ASSOC)))
			{
				$this->_safe_set ($row_select_parent[$this->FieldID], -1);
				$this->_safe_set ($row_select_parent[$this->FieldLeft], -1);
				$this->_safe_set ($row_select_parent[$this->FieldRight], -1);
				$this->_safe_set ($row_select_parent[$this->FieldLevel], -1);

				$left = $row_select_parent[$this->FieldLeft] + 1;

				//Set node tree as ignore
				$sql_ignore = "UPDATE ".$this->TableName." SET ".$this->FieldIgnore." = 1"." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'";
				$this->_safe_query ($sql_ignore);

				// Update Order (set order = order +1 where order>$Order)
				if ($Order == -1)
				{
					$sql_order = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldOrder." DESC "." LIMIT 0,1";
					$rs_order = $this->_safe_query ($sql_order);
					if (($rs_order) && ($row_order =& $rs_order->fetchRow(DB_FETCHMODE_ASSOC)))
					{
						$this->_safe_set ($row_order[$this->FieldOrder], 0);
						$Order = $row_order[$this->FieldOrder] + 1;
						$rs_order->free();
					}
					else
					{ $Order = 1; }
				}

				$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldOrder." = ".$this->FieldOrder." + 1"." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldOrder." >= ".$Order." AND ".$this->FieldDiffer." = '".$Differ."'";
				//echo '<br><br>'.$sql_update;
				$this->_safe_query ($sql_update);

				$sql_order = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldIDParent." = ".$IDParent." AND ".$this->FieldOrder." <= ".$Order." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldOrder." DESC "." LIMIT 0,1";
				$rs_order = $this->_safe_query ($sql_order);
				if (($rs_order) && ($row_order =& $rs_order->fetchRow(DB_FETCHMODE_ASSOC)))
				{
					$this->_safe_set ($row_order[$this->FieldRight], -1);
					$left = $row_order[$this->FieldRight] + 1;
					$rs_order->free();
				}


				$child_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft] + 1;

				// Update FieldLeft
				if ($left < $row_select[$this->FieldLeft]) // Move to left
				{
					$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldLeft." = ".$this->FieldLeft." + (".$child_offset.")"." WHERE ".$this->FieldLeft." >= ".$left." AND ".$this->FieldLeft." <= ".$row_select[$this->FieldLeft]." AND ".$this->FieldIgnore." = 0"." AND ".$this->FieldDiffer." = '".$Differ."'";
				}
				else // Move to right
				{
					$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldLeft." = ".$this->FieldLeft." - ".$child_offset." WHERE ".$this->FieldLeft." <= ".$left." AND ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldIgnore." = 0"." AND ".$this->FieldDiffer." = '".$Differ."'";
				}
				//echo '<br><br>'.$sql_update;
				$this->_safe_query ($sql_update);

				// Update FieldRight
				if ($left < $row_select[$this->FieldLeft]) // Move to left
				{
					$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldRight." = ".$this->FieldRight." + (".$child_offset.")"." WHERE ".$this->FieldRight." >= ".$left." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldIgnore." = 0"." AND ".$this->FieldDiffer." = '".$Differ."'";
				}
				else // Move to right
				{
					$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldRight." = ".$this->FieldRight." - ".$child_offset." WHERE ".$this->FieldRight." < ".$left." AND ".$this->FieldRight." >= ".$row_select[$this->FieldRight]." AND ".$this->FieldIgnore." = 0"." AND ".$this->FieldDiffer." = '".$Differ."'";
				}
				//echo '<br><br>'.$sql_update;
				$this->_safe_query ($sql_update);

				$level_difference = $row_select_parent[$this->FieldLevel] - $row_select[$this->FieldLevel] + 1;
				$new_offset = $row_select[$this->FieldLeft] - $left;
				if ($left > $row_select[$this->FieldLeft]) // i.e. move to right
				{ $new_offset += $child_offset; }

				//Update new tree left
				$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldLeft." = ".$this->FieldLeft." - (".$new_offset."), ".$this->FieldRight." = ".$this->FieldRight." - (".$new_offset."),"."$this->FieldLevel = $this->FieldLevel + $level_difference"." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldIgnore." = 1"." AND ".$this->FieldDiffer." = '".$Differ."'";
				//echo '<br><br>'.$sql_update;
				$this->_safe_query ($sql_update);

				//Remove ignore statis from node tree
				$sql_ignore = "UPDATE ".$this->TableName." SET ".$this->FieldIgnore." = 0"." WHERE ".$this->FieldLeft." >= ".($row_select[$this->FieldLeft] - $new_offset)." AND ".$this->FieldRight." <= ".($row_select[$this->FieldRight] - $new_offset)." AND ".$this->FieldIgnore." = 1"." AND ".$this->FieldDiffer." = '".$Differ."'";
				$this->_safe_query ($sql_ignore);

				//Update insert root field
				$sql_update = "UPDATE ".$this->TableName." SET ".$this->FieldIDParent." = ".$IDParent.", ".$this->FieldOrder." = ".$Order." WHERE ".$this->FieldID." = ".$IDNode;
				//echo '<br><br>'.$sql_update;
				$this->_safe_query ($sql_update);
				if(!empty($othercols)){
					$sql_update = "UPDATE ".$this->TableName." SET $othercols WHERE ".$this->FieldID." = ".$IDNode;
					$this->_safe_query ($sql_update);
				}
				$rs_select_parent->free();
				return true;
			}
			else
			{ return false; }

			$rs_select->free();
			return true;
		}
		else
		{ return false; }
	}

	function SelectPath ($IDNode = -1, $Differ = "")//Returns the path from the IDNode to the root node.
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'";
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." <= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." >= ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLeft;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	// Returns all the sub node of IDNode fo Level number of level.
	// RETURNEAZA SI NODUL $IDNode
	function SelectSubNodes ($IDNode = -1, $Level = -1, $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'";
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLevel.", ".$this->FieldOrder;
			}
			else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." <= ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLevel.", ".$this->FieldOrder;
			}
			$rs_select->free();
			//echo $sql_result;
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	function SelectSubNodesByZoneAndUser ($IDuser, $IDzone, $IDNode = -1, $Level = -1, $Differ = "")
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'";
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT a.* FROM ".$this->TableName." a INNER JOIN ".$this->TablePrefix."_users_2_pages b ON a.id = b.page_id  WHERE a.".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND a.".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND a.id_zone='" .$IDzone. "' AND a.id_page_inherited_rights='0' AND a.is_visible='Y' AND a.is_blocked!='Y' AND a.".$this->FieldDiffer." = '".$Differ."'"." AND b.id_user='" .$IDuser. "' ORDER BY a.".$this->FieldOrder;//a.".$this->FieldLevel.",
			}
			else // Only $Level child nodes
			{
				$sql_result = "SELECT a.* FROM ".$this->TableName." a INNER JOIN ".$this->TablePrefix."_users_2_pages b ON a.id = b.page_id WHERE a.".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND a.".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND a.id_zone='" .$IDzone. "' AND a.id_page_inherited_rights='0' AND a.is_visible='Y' AND a.is_blocked!='Y' a.".$this->FieldLevel." <= ".($Level + $row_select[$this->FieldLevel])." AND a.".$this->FieldDiffer." = '".$Differ."'"." AND b.id_user='" .$IDuser. "'  ORDER BY a.".$this->FieldOrder;//a.".$this->FieldLevel.",
			}
			//echo '<br>'.$sql_result;
			$rs_select->free();
			//echo $sql_result;
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	// Returns all the sub node of IDNode fo Level number of level.
	// RETURNEAZA SI NODUL $IDNode
	function SelectNodes()
	{
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM ".$this->TableName;
		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLevel.",".$this->FieldOrder;
			}
			else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." <= ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." <= ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLevel.",".$this->FieldOrder;
			}
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}
	// Returns all the sub node of IDNode fo Level number of level (DFS order).
	// NU RETURNEAZA SI NODUL $IDNode
	// Ordonarea se face dupa $this->FieldLeft
	function SelectSubNodesDFS ($IDNode = -1, $Level = -1, $Differ = ""){
		//echo "<br>Level = $Level";
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "
			SELECT * FROM ".$this->TableName."
			WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'
		";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLeft.", ".$this->FieldOrder;
			}else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY ".$this->FieldLeft.", ".$this->FieldOrder;
				//$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}
			//echo $sql_result;
			//echo $sql_result;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	function SelectSubNodesNotInherited ($IDNode = -1, $Level = -1, $Differ = ""){
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "
			SELECT * FROM ".$this->TableName."
			WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'
		";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." AND id_page_inherited_rights='0' ORDER BY {$this->FieldOrder}";
			}else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND id_page_inherited_rights='0' AND ".$this->FieldDiffer." = '".$Differ."' ORDER BY {$this->FieldOrder}";
				//$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}
			//echo $sql_result;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	function SelectSubNodesNotInheritedNotInvisible ($IDNode = -1, $Level = -1, $Differ = ""){
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "
			SELECT * FROM ".$this->TableName."
			WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."' AND is_blocked != 'Y'
		";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." AND is_visible='Y' AND id_page_inherited_rights='0' AND is_blocked != 'Y' ORDER BY ".$this->FieldOrder;
			}else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND is_visible='Y' AND id_page_inherited_rights='0' AND is_blocked != 'Y' AND ".$this->FieldDiffer." = '".$Differ."' ORDER BY ".$this->FieldOrder;
				//$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}
			//echo $sql_result;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}

	function SelectMainSubNodesDFS ($IDNode = -1, $Level = -1, $Differ = ""){
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "
			SELECT * FROM ".$this->TableName."
			WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'
		";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." AND id_page_inherited_rights='0' ORDER BY ".$this->FieldLeft.", ".$this->FieldOrder;
			}else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." AND id_page_inherited_rights='0' ORDER BY ".$this->FieldLeft.", ".$this->FieldOrder;
				//$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}
			//echo $sql_result;
			//echo $sql_result;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}
	// End nasted set functions //
	function SelectSubNodesOrderByName ($IDNode = -1, $Level = -1, $Differ = ""){
		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "
			SELECT * FROM ".$this->TableName."
			WHERE ".$this->FieldID." = ".$IDNode." AND ".$this->FieldDiffer." = '".$Differ."'
		";

		$rs_select = $this->_safe_query ($sql_select);
		if (($rs_select) && ($row_select =& $rs_select->fetchRow(DB_FETCHMODE_ASSOC)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." > ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
				//$sql_result = "SELECT * FROM ".$this->TableName." WHERE ".$this->FieldLeft." >= ".$row_select[$this->FieldLeft]." AND ".$this->FieldRight." < ".$row_select[$this->FieldRight]." AND ".$this->FieldLevel." < ".($Level + $row_select[$this->FieldLevel])." AND ".$this->FieldDiffer." = '".$Differ."'"." ORDER BY name";
			}
			//echo $sql_result;
			//echo $sql_result;
			$rs_select->free();
			return $this->_safe_query ($sql_result); // Remember to free result
		}
		else
		{ return false; }
	}
}

?>