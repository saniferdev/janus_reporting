<?php
session_start();
include('includes/config.php');
if(isset($_POST['login']))
{
$status='1';

$email=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT * FROM users WHERE email=:email and password=:password and status=(:status)";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> bindParam(':status', $status, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
    foreach($results as $val){
        $_SESSION['depot']	= $val->designation;
        $_SESSION['site']	= $val->site;
    }
    $_SESSION['alogin']= $_POST['username'];
    $_SESSION['where'] = "";
	if($_SESSION['site'] == "S1"){
		$_SESSION['DE_No'] = array(28,31);
		if($_SESSION['depot'] != 'SEC'){
			$_SESSION['where'] = "dbo.article.DL = '".$_SESSION['depot']."' AND";
		}
		$_SESSION['where'] .= " 
						
				
					(
						dbo.facture_entete.DO_Souche IN (0,1) 
					) 
				  ";
	}
	elseif($_SESSION['site'] == "S2"){
		$_SESSION['where'] = " dbo.facture_ligne.DE_No = 29";
		$_SESSION['DE_No'] = array(29);
	}
	elseif($_SESSION['site'] == "LIVRAISON" || $_SESSION['site'] == "LOGISTIQUE"){
		$_SESSION['where'] = " 
									(
										dbo.facture_entete.DO_Souche IN (0,1) 
									) 
									
								  ";
		$_SESSION['DE_No'] = array(28,31);
	}
	elseif($_SESSION['site'] == "S3"){
		$_SESSION['where'] = " dbo.facture_ligne.DE_No = 27 ";
		$_SESSION['DE_No'] = array(27);
	}
	elseif($_SESSION['site'] == "S4"){
		$_SESSION['where'] = " dbo.facture_ligne.DE_No = 34 ";
		$_SESSION['DE_No'] = array(34);
	}
	echo "<script type='text/javascript'> document.location = 'facture.php'; </script>";
} else{
  
  echo "<script>alert('Invalid Details Or Account Not Confirmed');</script>";

}

}

?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
    <link rel="apple-touch-icon" sizes="57x57" href="images/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="shortcut icon" href="images/favicon-96x96.png" />
	
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">

</head>

<body>
	<div class="login-page bk-img">
		<div class="form-content">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<h1 class="text-center text-bold mt-4x">Login</h1>
						<div class="well row pt-2x pb-3x bk-light">
							<div class="col-md-8 col-md-offset-2">
								<form method="post">

									<label for="" class="text-uppercase text-sm">Votre Email</label>
									<input type="text" placeholder="Username" name="username" class="form-control mb" required>

									<label for="" class="text-uppercase text-sm">Password</label>
									<input type="password" placeholder="Password" name="password" class="form-control mb" required>
									<button class="btn btn-primary btn-block" name="login" type="submit">LOGIN</button>
								</form>
								<br>
								<!--<p>Vous n'avez pas de compte ? <a href="register.php" >S'inscrire</a></p>-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>

</html>