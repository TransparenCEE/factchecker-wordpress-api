function set_tr_color(id_tr, old_color, onmouseover_type, tables_elements){
	var	target;
	for(var i = 1; i<=tables_elements ; i++){
		target = document.getElementById(i+'_'+id_tr);
		if (onmouseover_type == 'in'){
			target.style.background ='#FBD983';
		}else{
			target.style.background = old_color;
		}
	}
}