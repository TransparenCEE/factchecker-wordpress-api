<?php    
include_once(dirname(__FILE__).'/common/header.inc.php');
?>
<script type="text/javascript">
function validateLoginForm(frm){
	if(frm.txt_username.value==''){
		alert('Please insert your e-mail address !');
		frm.txt_username.focus();
		return false;
	}
	if(frm.txt_password.value==''){
		alert('Please insert your password !');
		frm.txt_password.focus();
		return false;
	}

} //end function
</script>
<br><br>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td>
			<form name="form" method="post" action="login_exec.php" onSubmit="return validateLoginForm(document.form)"> 
			<table border="0" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<td><div class="error"><strong>Authentification required!</strong></div></td>
			</tr>
			<?php     if($_GET['msg']=='login_error'){?>
			<tr>
				<td><span class="error"><?php     echo $cfg_portal_errors_array['login']['login_error']; ?></span></td>
			</tr>
			<?php     } ?>
			<tr>
			  <td>
					<div class="sitetext1">
					<strong>Username: </strong>
					<input name="txt_username" type="text" class="input" value="<?php     echo $_GET['txt_username']; ?>">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="sitetext1"><strong>Password:</strong> <input name="txt_password" class="input" type="password"></div>
				</td>
			</tr>
			<tr>
			  <td><div align="right"><input type="submit" name="Submit" value="Submit" class="input"></div></td>
			</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php    
include_once(dirname(__FILE__).'/common/footer.inc.php');
?>