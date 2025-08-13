<?php
  require_once("config.php");

?>
<?php 
 include("include/home/header.php");
 include("include/home/nav.php");
 ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php 
 include("include/home/sidebar.php");
 ?>
 <div>
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
      
        <?php
       include("placholder.php");
        ?>
       
      </div>
      <!-- /.card -->

    </section>
  </div>
  <!-- /.content-wrapper -->

  <?php 
 include("include/home/footer.php");
 ?>