<?php
// include "config.php";
// date_default_timezone_set("Asia/Dhaka"); 

// if(isset($_POST['btn-login'])) {
// 	$email =	trim($_POST['email']);
// 	$password =	trim($_POST['password']);

// 	$query = $db->query("SELECT * FROM users WHERE email='$email' AND password='$password' ");
// 	header("location:home.php");
// }
// ?>


<?php session_start(); 

 require_once("config.php");
 
 if(isset($_POST["btnLogin"])){
	$email=trim($_POST["email"]);
	$password=trim($_POST["password"]);
	
   $user_table=$conn->query("select id,email,password from users where email='$email' and password='$password'");
   
  list($id,$_first,$_last,$_email,$_password)=$user_table->fetch_row();
  
	if(isset($id)){
	    $_SESSION["s_email"]=$_email;	
		header("location:home.php");
	}else{
	   $error="<span style='color:red;'>Incorrect username or password</span>";	
	}
	
 }

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="dist/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
  <div class="container-fluid"> 
       <div class="row">
          <div class="col-4" style="float: left;">
          </div>
        <div class="col-4" style="float: left;">
          </div>
        <div class="col-4">
           <a href="index.php">
            <button type="submit" class="btn btn-primary ">Register</button>
            </a>
             <a href="login.php">
            <button type="submit" class="btn btn-info ">sign-in</button>
            </a>
          </div>
       
         
          <!-- /.col -->
        </div>
  </div>
<div class="login-box">
  <div class="login-logo">
    <a href="dist/index2.html"><b>Admin</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
 <div><?php echo isset($error)?$error:"";?></div>
      <form action="#" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name = "email" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name = "password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
         
            <button type="submit" name="btnLogin" class="btn btn-primary btn-block">Sign In</button>
       
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- /.social-auth-links -->

      
      <p class="mb-0">
        <a href="index.php" class="text-center">Register a new membership</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="dist/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/dist/js/adminlte.min.js"></script>
</body>
</html>
