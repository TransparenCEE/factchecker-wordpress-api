function displayInheritFrom(how, id)	{
	if(how == 1)	{
		document.getElementById(id).style.display = "";
	}
	else	{
		document.getElementById(id).style.display = "none";
	}
}

function enableCheckbox(parentId)	{
	var args = enableCheckbox.arguments;
	obParent =  document.getElementById("check_" + parentId);
	for (var i = 1; i < args.length; i++)	{
		ob = document.getElementById("check_" + args[i]);
		if(obParent.checked == true)	{
			ob.disabled = false;
		}
		else	{
			ob.disabled = true;
			ob.checked = false;			
		}
	}	
}