<?php
ob_start();
session_start();

include_once('inc/config.php');
include_once('inc/functions.php');

$db = dbConnect();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/ukras.css" />
	<link rel="icon" href="slike/Icon.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<title>Citrus</title>
	<script type="text/javascript" src="https://edge.cookieconsent.io/prod/js/000000000000-0000-0000-0000-000000000000.js?hidden=false&backdrop=false&position=center&marketing=false&analytics=true&google_consent_mode=true"></script>
  </head>
  <body>
	<script type="text/javascript">
	var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
	(function(){
	var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
	s1.async=true;
	s1.src='https://embed.tawk.to/62a5e7c0b0d10b6f3e76ec91/1g5c12id6';
	s1.charset='UTF-8';
	s1.setAttribute('crossorigin','*');
	s0.parentNode.insertBefore(s1,s0);
	})();
	</script>

    <?php include('tpl/header.php');?>
	<div class="Sadrzaj">
		<main class="p-3">
		<div class="container">
			<div class="row">
				<?php echo getNews();?>
			</div>
		</div>
		</main>
		</div>
		<?php include('tpl/footer.php');?>

	<script src="js/functions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>
