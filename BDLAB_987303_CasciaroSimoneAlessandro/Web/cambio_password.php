<?php
include("menu.php");
?>

<br><br><br>

<div> 
	<?php
	echo $_SESSION['messaggio_password_modificata']; 
	$_SESSION['messaggio_password_modificata'] = ''; 
	?> 
</div>

<form method="POST" action="memorizza_nuova_password.php">
	<label> Nuova Password </label>
	<input type="password" id="nuova_password" name="nuova_password" for="nuova_password" value="">
	<label> Ripeti Nuova Password </label>
	<input type="password" id="nuova_password_conferma" name="nuova_password_conferma" for="nuova_password_conferma" value="">
	<button type="submit" class="btn btn-primary"> Conferma </button>
</form>