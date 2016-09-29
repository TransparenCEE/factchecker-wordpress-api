<?php 
function selected($a, $b)	{
	if($a == $b)	{
		return 'selected="selected"';
	}
	else	{
		return false;
	}
}
function checked($a, $b)	{
	if($a == $b)	{
		return 'checked';
	}
	else	{
		return false;
	}
}
?>