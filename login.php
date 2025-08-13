<?php
session_start();
require_once("config.php");

if (isset($_POST["btnLogin"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // এখন role_id নিয়েও ডাটা আনা
    $user_table = $conn->query("SELECT id, email, password, role_id FROM user WHERE email='$email' AND password='$password'");

    if ($user_table->num_rows > 0) {
        list($id, $db_email, $db_password, $role_id) = $user_table->fetch_row();

        // সেশন সেট
        $_SESSION["s_email"] = $db_email;
        $_SESSION["s_role"] = $role_id;

        // রোল অনুযায়ী রিডিরেক্ট
        if ($role_id == 1) {
            header("Location: home.php");
        } elseif ($role_id == 2) {
            header("Location: index.php");
        } else {
            header("Location: home.php");
        }
        exit;
    } else {
        $error = "<span style='color:red;'>Incorrect username or password</span>";
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
        <a href="registration.php" class="text-center">Register a new membership</a>
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
