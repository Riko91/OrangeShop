<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/prijava.css" />
	<link rel="icon" href="slike/Icon.png" type="image/x-icon" />
	<title>Citrus</title>
  </head>
<?php

$checkLogIn = checkLogIn();
if($checkLogIn) {
	redirect("admin.php?modul=news");
}

$output = null;

$UserName = checkInputValue('UserName');
$Password = checkInputValue('Password');

if(isset($_POST) && count($_POST) > 0) {
	if($UserName && $Password) {
		if($UserName == $config['admin_username'] && $Password == $config['admin_password']) {
			$_SESSION['LogIn'] = true;
			$_SESSION['UserID'] = $config['user_id'];

			redirect("admin.php");
		} else {
			echo getAlert("Napačno uporabniško ime ali geslo.", "danger");
		}
	} else {
		echo getAlert("Uporabniško ime in geslo sta obvezna!", "danger");
	}
}

$output .= '
<div class="Prijave">
        <img src="slike/Icon.png" width="100px" height="100px" />
        <h1>Registracija</h1>
        <form method="post" name="form">
            <p>Korisničko ime</p>
            <input type="text" name="UserName" placeholder="Unesite korisničko ime" />
            <p>Lozinka</p>
            <input type="password" name="Password" placeholder="Unesite lozinku" />
            <input type="submit" name="submit" value="Prijavi se" />
            <hr class="linija" />
            <br>
            <p>Da bi kupili naranče, morate se prijaviti na našu web-stranicu!</p>
        </form>
    </div>';

echo $output;
