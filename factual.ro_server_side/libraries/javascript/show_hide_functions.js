function Hide(elementID){
	document.getElementById(elementID).style.visibility = 'hidden';
	document.getElementById(elementID).style.display = 'none';
}

function Show(elementID){
	document.getElementById(elementID).style.visibility='visible';
	document.getElementById(elementID).style.display = 'block';
}
function ShowTR(elementID){
 document.getElementById(elementID).style.visibility='visible';
 if(BrowserDetect.browser=='Firefox'){
  document.getElementById(elementID).style.display = 'table-row';
 }else{
  document.getElementById(elementID).style.display = 'block';
 }
} 

function ShowHide(elementID){
	if(document.getElementById(elementID).style.display == 'none'){
		Show(elementID)
	}else{
		Hide(elementID)
	}
}