<?php
if(!isset($_SESSION)){
	session_start();
}
error_reporting(E_ALL);

$checkLogIn = checkLogIn();
if(!$checkLogIn) {
	redirect("admin.php?modul=login");
}

$Action = actionModul();
$NewsID = getID('NewsID');
include('tpl/adminheader.php');
$output = null;
$output .= '<div class="col">';
$output .= '<h3 class="mb-2">Urejevalnik novic</h3>';

if(!$Action || $Action == 'view') { // select from News
	$sql = $db->prepare("SELECT NewsID, Title, Description, Logo, Status, TimeStampAdded, TimeStampUpdated FROM News");
	$sql->execute();
	$dbNews = $sql->fetchAll(PDO::FETCH_ASSOC);

	$output .= '<a href="?modul=news&action=edit" class="btn btn-success">Dodaj novico</a>';
	if(isset($dbNews) && count($dbNews) > 0) {
		$output .= '<table class="mt-2 table">';
		$output .= '<thead>';
		$output .= '<tr>';
		$output .= '<th>#</th>';
		$output .= '<th>Naslov</th>';
		$output .= '<th>Opis</th>';
		$output .= '<th>Dodano</th>';
		$output .= '<th>Posodobljeno</th>';
		$output .= '<th>Opcije</th>';
		$output .= '</tr>';
		$output .= '</thead>';

		$output .= '<tbody>';

		$counterNews = 1;
		foreach($dbNews as $key => $row) {
			$NewsID = $row['NewsID'];
			$Title = outputShortText($row['Title'], 15);
			$Description = outputShortText($row['Description'], 25);
			$TimeStampAdded = convertDate($row['TimeStampAdded']);
			$TimeStampUpdated = convertDate($row['TimeStampUpdated']);
			$Status = $row['Status'];
			$StatusText = ($row['Status'] == 1) ? 'Skrij' : 'Prikaži';

			$outputBtn = '
			<div class="btns float-end">
				<a class="btn btn-danger delete" href="?modul=news&action=delete&NewsID='.$NewsID.'" data-msg="Ali želiš izbrisati ta elemnt?">Briši</a>
				<a class="btn btn-warning" href="?modul=news&action=edit&NewsID='. $NewsID .'">Uredi</a>
				<a class="btn btn-warning" href="?modul=news&action=status&NewsID='. $NewsID .'">'.$StatusText.'</a>
			</div>
			';

            $ClassStatus = ($row['Status'] == 0) ? ' class="table-danger"' : null;
			$output .= '<tr'.$ClassStatus.'>';
			$output .= '<td class="align-middle">' . $counterNews . '</td>';
			$output .= '<td class="align-middle">' . $Title . '</td>';
			$output .= '<td class="align-middle">' . $Description . '</td>';
			$output .= '<td class="align-middle">' . $TimeStampAdded . '</td>';
			$output .= '<td class="align-middle">' . $TimeStampUpdated . '</td>';
			$output .= '<td class="align-middle">' . $outputBtn . '</td>';
			$output .= '</tr>';

			$counterNews++;
		}
		$output .= '</tbody>';
		$output .= '</table>';
	}
} else if($Action == 'edit') { // Edit or Add News
	if(isset($_POST) && count($_POST) > 0) {
		$Title = checkInputValue('Title');
		$Description = checkInputValue('Description');
		$Content = checkInputValue('Content');
		$Logo = (isset($_FILES['Logo']) && isset($_FILES['Logo']['tmp_name']) && !empty($_FILES['Logo']['tmp_name'])) ? file_get_contents($_FILES['Logo']['tmp_name']) : "";
		$TimeStampAdded = time();
        $TimeStampUpdated = time();

		if($Title && $Description && $Content) {
			if($NewsID) {
				$sql = $db->prepare("UPDATE News SET Title = :Title, Description = :Description, Logo = :Logo, Content = :Content, TimeStampUpdated = :TimeStampUpdated WHERE NewsID = :NewsID");
				$sql->execute([
					'NewsID' => $NewsID,
					'Title' => $Title,
					'Description' => $Description,
					'Logo' => $Logo,
					'Content' => $Content,
					'TimeStampUpdated' => $TimeStampUpdated
				]);

				echo getAlert("Novica je bila posodobljena!", "success");
			} else {
				$sql = $db->prepare("INSERT into News (Title, Description, Content, Logo, TimeStampAdded) VALUES(?, ?, ?, ?, ?)");
				$sql->execute([$Title, $Description, $Content, $Logo, $TimeStampAdded]);

				echo getAlert("Novica je bila dodana!", "success");
			}
		} else {
			echo getAlert("Nisi izpolnil obveznih polj!", "danger");
		}
	}

	$row = null;
	if($NewsID) {
		$sql = $db->prepare("SELECT NewsID, Title, Description, Content, TimeStampAdded, TimeStampUpdated FROM News WHERE NewsID = :NewsID");
		$sql->execute(['NewsID' => $NewsID]);
		$dbNews = $sql->fetchAll(PDO::FETCH_ASSOC);
		$dbNews = (isset($dbNews) && isset($dbNews[0])) ? $dbNews[0] : null;

		$row = $dbNews;
	}

	$output = '<form method="post" enctype="multipart/form-data">
		<div class="row">
			<div class="col-12">
				<label for="Title">Naslov novice</label>
				<input class="form-control" type="text" name="Title" id="Title" value="'.getFieldValue($row, 'Title').'" required>
			</div>
			<div class="col-12 mt-3">
				<label for="Logo">Logotip novice</label>
				<input class="form-control" type="file" name="Logo" id="Logo">
			</div>
			<div class="col-12 mt-3">
				<label for="Description">Opis novice</label>
				<input class="form-control" type="text" name="Description" id="Description" minlength="100" value="'.getFieldValue($row, 'Description').'"  required>
			</div>
			<div class="col-12 mt-3">
				<label for="Content">Vsebina novice</label>
				<textarea class="form-control" name="Content" id="Content" required rows="10" cols="50">'.getFieldValue($row, 'Content').'</textarea>
			</div>
			<div class="col">
				<input type="submit" value="Shrani" class="mt-3 btn btn-success float-end">
			</div>
		</div>
	</form>';
} else if($Action == 'status') { // Change status for NewsID
	if($NewsID) {
		$sql = $db->prepare("SELECT Status FROM News WHERE NewsID = :NewsID");
		$sql->execute(['NewsID' => $NewsID]);
		$dbNews = $sql->fetchAll(PDO::FETCH_ASSOC);
		$dbNews = (isset($dbNews) && isset($dbNews[0])) ? $dbNews[0] : null;

		if(isset($dbNews) && isset($dbNews['Status'])) {
			$Status = ($dbNews['Status'] == 1) ? 0 : 1;
			$sql = $db->prepare("UPDATE News SET Status = :Status WHERE NewsID = :NewsID");
			$sql->execute(['Status' => $Status, 'NewsID' => $NewsID]);
		}

		redirect('admin.php?modul=news');
	} else {
		redirect('admin.php?modul=news');
	}
} else if($Action == 'delete') { // Delete from NewsID
	if($NewsID) {
		$sql = $db->prepare("DELETE FROM News WHERE NewsID = :NewsID");
		$sql->execute(['NewsID' => $NewsID]);

		redirect('admin.php?modul=news');
	} else {
		redirect('admin.php?modul=news');
	}
}
$output .= '</div>';

echo $output;
include('tpl/adminfooter.php');