<?php 
include("config.php");

// Role টেবিল থেকে role ডাটা আনা
$role_sql = "SELECT id, role_type FROM role";
$role_result = $conn->query($role_sql);

if (isset($_POST['submit'])) {
    $first_name = $_POST['firstname'];
    $last_name  = $_POST['lastname'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $role_id    = $_POST['role_id'];

    if (!empty($role_id)) {
        $sql = "INSERT INTO `user`(`firstname`, `lastname`, `email`, `password`, `role_id`) 
                VALUES ('$first_name','$last_name','$email','$password', '$role_id')";

        $result = $conn->query($sql);

        if ($result == TRUE) {
            echo "New record created successfully.";
            header("location: login.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Please select a role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration Page</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="dist/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="dist/index2.html"><b>Admin</b>LTE</a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="firstname" placeholder="First name" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="text" class="form-control" name="lastname" placeholder="Last name" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="email" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <!-- Role Dropdown -->
        <div class="input-group mb-3">
          <select name="role_id" class="form-control" required>
            <option value="">-- Select Role --</option>
            <?php
            if ($role_result->num_rows > 0) {
                while ($row = $role_result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['role_type']}</option>";
                }
            }
            ?>
          </select>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
          </div>
        </div>

        <div class="row">
          <div class="col-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <a href="login.php" class="text-center">I already have a membership</a>
    </div>
  </div>
</div>

<script src="dist/plugins/jquery/jquery.min.js"></script>
<script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/dist/js/adminlte.min.js"></script>
</body>
</html>
