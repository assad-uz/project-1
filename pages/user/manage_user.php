<?php
if (isset($_POST["btnDelete"])) {
  $u_id = $_POST["txtId"];

  $conn->query("delete from users where id='$u_id'");
  echo "Deleted";
}
?><!-- Content Wrapper. Contains page content -->




<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Manage users</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Blank Page</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Manage Users</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="card">
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-striped table-bordered">
              <thead class="bg-primary text-white">
                <tr>
                  <th>#ID</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $users = $conn->query("select * from users");
                while (list($id, $fname, $lname, $email, $_pass) = $users->fetch_row()) {
                  echo "<tr> 
					<td>$id</td>
					<td>$fname</td>
					<td>$lname</td>
					<td>$email</td>
					<td>$_pass</td>
					<td> 
					
          <form action='home.php?page=2' method='post'>
            <input type='hidden' name='txtId' value='$id' />
            <input type='submit' name='btnDelete' class='material-icons red600' value='delete'>
          </form>
          <form action='home.php?page=3' method='post' style='display:inline'>
           <input type='hidden' name='id' value='$id' />
           <input type='submit' name='btnEdit'  class='material-icons red600' value='edit'>
					</td>
				</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
              <li class="page-item"><a class="page-link" href="#">«</a></li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">»</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- /.card-footer-->
    </div>
    <!-- /.card -->

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->