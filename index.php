<?php
require_once("includes/header.php");
require_once("includes/navbar.php");
?>

  <!-- Full screen background with overlay content -->
<div class="bg-cover">
  <div class="overlay text-center">
    <h1 class="display-7 fw-bold mb-3">Welcome to HOTEL HORIZON</h1>
    <!-- <h1 class="display-4 fw-bold mb-3">Welcome to <span class="text-warning">HOTEL HORIZON</span></h1> -->
    <p class="lead fw-semibold mb-4">Book your stay with ease and comfort</p>
    <a href="book_room.php" class="btn btn-yellow btn-lg fw-semibold">Book Now</a>
  </div>
</div>


  <!-- Features Section -->
  <section class="py-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-4">
          <h4>Room Booking</h4>
          <p>Find and book available rooms quickly with just a few clicks.</p>
        </div>
        <div class="col-md-4">
          <h4>Easy Management</h4>
          <p>Manage your bookings and rooms effortlessly.</p>
        </div>
        <div class="col-md-4">
          <h4>User Dashboard</h4>
          <p>Track your bookings, payments, and check-in/check-out schedule.</p>
        </div>
      </div>
    </div>
  </section>
  
  <?php
  require_once("includes/footer.php");

  ?>

