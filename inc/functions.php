<?php

function dbConnect() {
	global $config;

	$dbUserName = $config['dbUserName'];
	$dbPassword = $config['dbPassword'];
	$dbHostname = $config['dbHostname'];
	$dbName = $config['dbName'];

	$db = new PDO("mysql:host=$dbHostname;dbname=$dbName", $dbUserName, $dbPassword);

	return $db;
}

function checkInputValue($name) {
	$output = null;

	if($name) {
		$output = (isset($_POST) && isset($_POST[$name])) ? $_POST[$name] : null;
	}

	return $output;
}

function getAlert($msg, $type) {

	if($msg && $type) {
		$alertType = null;
		switch($type) {
			case 'danger':
				$alertType = " alert-danger";
				break;
			case 'success':
				$alertType = " alert-success";
				break;
		}

		$output = '<div class="row"><div class="col-12"><div class="alert'.$alertType.'">' . $msg .'</div></div></div>';
	}

	return $output;
}

function convertDate($timestamp, $format = "d.m.Y, H:i") {
	$output = null;

	if($timestamp && $timestamp > 0) {
		$output = date($format, $timestamp);
	} else {
		$output = "/";
	}

	return $output;
}

function redirect($page) {
	$output = null;

	if($page) {
		header("Location: $page");
	}

	return $output;
}

function getFieldValue($row, $name) {
	$output = null;

	$output = (isset($row) && (isset($name)) && isset($row[$name])) ? $row[$name] : null;

	return $output;
}

function actionModul() {
	$output = null;

	$ActionAllowed = ['view', 'edit', 'delete', 'status'];

	$output = (isset($_GET['action']) && in_array($_GET['action'], $ActionAllowed)) ? $_GET['action'] : null;

	return $output;
}

function getID($ID) {
	$output = null;

	$output = (isset($_GET[$ID]) && is_numeric($_GET[$ID])) ? $_GET[$ID] : null;

	return $output;
}

function getModul() {
	$output = null;

	$output = (isset($_GET['modul'])) ? $_GET['modul'] : null;

	return $output;
}

function checkLogIn() {
	$output = null;

	if(isset($_SESSION) && isset($_SESSION['LogIn'])) {
		$output = true;
	} else {
		$output = false;
	}

	return $output;
}

function logout() {
	if(isset($_SESSION)) {
		session_destroy();

		redirect("admin.php");
	}
}

function outputLogIn() {
	$output = null;

	$checkLogIn = checkLogIn();

	if($checkLogIn) {
		$UserID = $_SESSION['UserID'];

		$output .= '
		<li class="nav-item dropdown">
            <a href="#"role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Pozdravljen, '.$UserID.'
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="admin.php?modul=news">Urejevalnik novic</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="admin.php?modul=logout">Odjava</a></li>
          </ul>
        </li>
		';
	}

	return $output;
}

function outputLogInADMIN() {
	$output = null;

	$checkLogIn = checkLogIn();

	if($checkLogIn) {
		$UserID = $_SESSION['UserID'];

		$output .= '
		<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Pozdravljen, '.$UserID.'
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="admin.php?modul=news">Urejevalnik novic</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="admin.php?modul=logout">Odjava</a></li>
          </ul>
        </li>
		';
	}

	return $output;
}

function getNews($Limit = null) {
	global $db;

	$output = null;

	$NewsID = getID('NewsID');

	if($NewsID) {
		$sql = $db->prepare("SELECT NewsID, Title, Description, Logo, Content, TimeStampAdded, TimeStampUpdated FROM News WHERE NewsID = :NewsID");
		$sql->execute(['NewsID' => $NewsID]);
		$row = $sql->fetchAll(PDO::FETCH_ASSOC);
		$row = (isset($row) && isset($row[0])) ? $row[0] : null;

		$NewsID = $row['NewsID'];
		$Title = $row['Title'];
		$Description = $row['Description'];
        $Logo = (isset($row['Logo']) && !empty($row['Logo'])) ? '<img src="data:image/jpeg;base64,'.base64_encode($row['Logo']).'" class="float-lg-end float-sm-start img-thumbnail mb-2 ms-2" style="max-width: 300px;">' : null;

        $BtnBack = '<div class="clearfix"></div><a href="index.php" class="btn btn-dark float-end mt-2">Vse novice</a>';

		$output .= '
		<div class="col">
			<h2>' . $row['Title'] . '</h2>
			'.$Logo.'
			<blockquote class="blockquote"><p>'. $row['Description'] . '</p></blockquote>
			'. $row['Content'] . '
			'.$BtnBack.'
		</div>
		';
	} else {
		$Limit = ($Limit) ? " LIMIT $Limit" : null;
	
		$sql = $db->prepare("SELECT NewsID, Title, Description, Logo, Status, TimeStampAdded, TimeStampUpdated FROM News WHERE Status = :Status ORDER BY TimeStampAdded DESC $Limit ");
		$sql->execute(['Status' => 1]);
		$dbNews = $sql->fetchAll(PDO::FETCH_ASSOC);

		if(isset($dbNews) && count($dbNews) > 0) {
			$counterNews = 1;
			foreach($dbNews as $key => $row) {
				$NewsID = $row['NewsID'];
				$Title = outputShortText($row['Title'], 40);
				$Description = outputShortText($row['Description'], 100);
				$Logo = (isset($row['Logo']) && !empty($row['Logo'])) ? '<img src="data:image/jpeg;base64,'.base64_encode($row['Logo']).'" class="card-img-top d-none d-md-block d-lg-block"/ style="max-height: 177px;">' : '<img src="https://via.placeholder.com/350x150" class="card-img-top d-none d-md-block d-lg-block">';
				$TimeStampAdded = convertDate($row['TimeStampAdded']);
				$TimeStampUpdated = convertDate($row['TimeStampUpdated']);
				$Status = $row['Status'];
				$StatusText = ($row['Status'] == 1) ? 'Skrij' : 'Prikaži';

				$output .= '
				<div class="col-md-4 col-sm-12 mb-3">
					<div class="card">
						'. $Logo . '
						<div class="card-body">
							<h5 class="card-title">' . $Title . '</h5>
							<p class="card-text">' . $Description . '</p>
							<a href="?NewsID=' . $NewsID . '" class="btn btn-dark">Preberi več</a>
						</div>
					</div>
				</div>';

				$counterNews++;
			}
		}
	}

	return $output;
}


function outputShortText($String, $Length = null) {
	$output = null;
	
	if($String && $Length > 0) {
		$output = (strlen($String) > $Length) ? substr($String, 0, $Length) . "..." : $String;
	}
	
	return $output;
}