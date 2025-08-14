
<?php

  if(isset($_POST["btnUpdate"])){
	  
	  $id=$_POST["id"];	  
	  $fname=$_POST["fname"];
	  $lname=$_POST["lname"];
	  $email=$_POST["email"];
	  $password=$_POST["password"];
	  
	  $conn->query("update users set firstname='$fname',lastname='$lname',email='$email' where id='$id'");
	$r = "Success Updated";
	  // echo "Success Updated";
	  
  }
  
  
  if(isset($_POST["btnEdit"])){
	  $id=$_POST["id"];
	  
	  $use_tbl=$conn->query("select firstname,lastname,email,password from users where id='$id'");
	  
	  list($fname,$lname,$email,$password)=$use_tbl->fetch_row();
	}
  

?>
  
  <!-- Content Wrapper. Contains page content -->


  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add users</h1>
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
          <h3 class="card-title">Title</h3>

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
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Quick Example</h3>
              </div>
              <!-- /.card-header -->
        
  </div>
  <div class="ftitle text-center"> 
			<h4><?php echo isset($r)?$r:"Users update Form" ?></h4>
		</div>
              <!-- form start -->
             <form action="#" method="post">
                  <div class="card-body">

              <div class="form-group">
                <input type="hidden" class="form-control"  name="id" value="<?php echo $id ?>">
              </div>
              <div class="form-group">
                <label for="y">First Name</label>
                <input type="text" class="form-control" id="y" name="fname" value="<?php echo $fname ?>">
              </div>
              <div class="form-group">
                <label for="y">Last Name</label>
                <input type="text" class="form-control" id="y" name="lname" value="<?php echo $lname ?>">
              </div>
               <div class="form-group">
                <label for="p">Email</label>
                <input type="text" class="form-control" id="p" name="email" value="<?php echo $email ?>">
              </div>
               <div class="form-group">
                <label for="r">password</label>
                <input type="text" class="form-control" id="r" name="password" value="<?php echo $password ?>">
              </div>
              
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" name="btnUpdate">Submit</button>
            </div>
          </form>
        </div>
      </div>

      <!-- /.card-footer-->
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
