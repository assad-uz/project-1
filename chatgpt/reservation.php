<?php
session_start();
require_once('config.php'); // expects $conn = new mysqli(...)

$messages = [];

function get_role_id($conn, $role_name) {
  $sql = "SELECT id FROM role WHERE LOWER(role_type)=LOWER(?) LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $role_name);
  $stmt->execute();
  $stmt->bind_result($id);
  $found = $stmt->fetch();
  $stmt->close();
  return $found ? (int)$id : null;
}

$adminRoleId    = get_role_id($conn, 'admin');
$customerRoleId = get_role_id($conn, 'customer');

// Fetch dropdown data upfront
$admins_rs = $conn->query("SELECT id, name, email FROM users WHERE role_id = " . (int)$adminRoleId . " ORDER BY name");
$customers_rs = $conn->query("SELECT id, name, email FROM users WHERE role_id = " . (int)$customerRoleId . " ORDER BY name");
$room_types_rs = $conn->query("SELECT id, room_name FROM room_type ORDER BY room_name");
$rooms_rs = $conn->query("SELECT id, room_type_id, room_number, room_price, room_status FROM room ORDER BY room_number");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reservation'])) {
  $mode            = $_POST['customer_mode'] ?? 'existing';
  $admin_id        = isset($_POST['approved_by']) ? (int)$_POST['approved_by'] : 0;
  $room_id         = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
  $checkin         = $_POST['checkin_date'] ?? null;
  $checkout        = $_POST['checkout_date'] ?? null;
  $payment_method  = $_POST['payment_method'] ?? null;

  try {
    if (!$admin_id) throw new Exception('Please select an approving admin.');
    if (!$room_id) throw new Exception('Please select a room.');
    if (!$checkin || !$checkout) throw new Exception('Please select check-in and check-out dates.');

    $conn->begin_transaction();

    // 1) Resolve / Create customer
    if ($mode === 'new') {
      $name     = trim($_POST['name'] ?? '');
      $email    = trim($_POST['email'] ?? '');
      $phone    = trim($_POST['phone'] ?? '');
      $password = trim($_POST['password'] ?? '');
      if ($name === '' || $email === '' || $password === '') {
        throw new Exception('Name, Email and Password are required for a new customer.');
      }
      $stmt = $conn->prepare("INSERT INTO users (role_id, name, email, phone, password) VALUES (?,?,?,?,?)");
      $stmt->bind_param('issss', $customerRoleId, $name, $email, $phone, $password);
      $stmt->execute();
      $user_id = $stmt->insert_id;
      $stmt->close();
    } else {
      $user_id = (int)($_POST['user_id'] ?? 0);
      if (!$user_id) throw new Exception('Please select an existing customer.');
    }

    // 2) Calculate nights and room price
    $d1 = new DateTime($checkin);
    $d2 = new DateTime($checkout);
    $nights = (int)$d1->diff($d2)->format('%a');
    if ($nights <= 0) throw new Exception('Checkout must be after Check-in.');

    $stmt = $conn->prepare('SELECT room_price FROM room WHERE id=?');
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $stmt->bind_result($room_price);
    if (!$stmt->fetch()) {
      $stmt->close();
      throw new Exception('Selected room not found.');
    }
    $stmt->close();

    $amount = $nights * (float)$room_price;

    // 3) Create booking
    $payment_status = 'pending';
    $stmt = $conn->prepare("INSERT INTO booking (users_id, room_id, booking_date, checkin_date, checkout_date, payment_status, amount) VALUES (?,?,NOW(),?,?,?,?)");
    $stmt->bind_param('iisssd', $user_id, $room_id, $checkin, $checkout, $payment_status, $amount);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // 4) Create invoice
    $invoice_status = 'unpaid';
    $stmt = $conn->prepare("INSERT INTO invoice (users_id, booking_id, invoice_date, payment_status) VALUES (?,?,CURDATE(),?)");
    $stmt->bind_param('iis', $user_id, $booking_id, $invoice_status);
    $stmt->execute();
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // 5) Create payment
    $stmt = $conn->prepare('INSERT INTO payment (booking_id, users_id, invoice_id, payment_method) VALUES (?,?,?,?)');
    $stmt->bind_param('iiis', $booking_id, $user_id, $invoice_id, $payment_method);
    $stmt->execute();
    $payment_id = $stmt->insert_id;
    $stmt->close();

    // 6) Create transaction (approved by admin)
    $stmt = $conn->prepare('INSERT INTO transaction (users_id, booking_id, payment_id, approved_by) VALUES (?,?,?,?)');
    $stmt->bind_param('iiii', $user_id, $booking_id, $payment_id, $admin_id);
    $stmt->execute();
    $stmt->close();

    // 7) Optional: mark room as booked
    $stmt = $conn->prepare("UPDATE room SET room_status='booked' WHERE id=?");
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $messages[] = '✅ Reservation created successfully. Booking ID: ' . $booking_id . ' | Nights: ' . $nights . ' | Amount: ' . number_format($amount, 2);
  } catch (Exception $e) {
    $conn->rollback();
    $messages[] = '❌ Error: ' . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Reservation (Wizard)</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .step-pane { display:none; }
    .step-footer { display:flex; justify-content:space-between; margin-top:1rem; }
    .badge-step { font-size: 0.9rem; }
  </style>
</head>
<body class="bg-light">
<div class="container my-4">
  <div class="d-flex align-items-center mb-3">
    <h3 class="mb-0">Reservation Wizard</h3>
    <span class="ml-3 badge badge-info badge-step">Step <span id="stepIndicator">1</span> / 4</span>
  </div>

  <?php if (!empty($messages)): ?>
    <?php foreach ($messages as $m): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($m); ?></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form method="post" id="reservationForm">
    <input type="hidden" name="create_reservation" value="1">

    <!-- Step 1: Customer -->
    <div class="card step-pane" data-step="1">
      <div class="card-header">Step 1 — Customer</div>
      <div class="card-body">
        <div class="form-group">
          <label class="mr-3"><input type="radio" name="customer_mode" value="existing" checked> Existing Customer</label>
          <label><input type="radio" name="customer_mode" value="new"> New Customer</label>
        </div>

        <div id="existingCustomerBox">
          <div class="form-group">
            <label>Select Customer</label>
            <select class="form-control" name="user_id">
              <option value="">-- choose customer --</option>
              <?php if ($customers_rs && $customers_rs->num_rows): while ($c = $customers_rs->fetch_assoc()): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['name'] . ' — ' . $c['email']); ?></option>
              <?php endwhile; endif; ?>
            </select>
          </div>
        </div>

        <div id="newCustomerBox" class="border rounded p-3 bg-white" style="display:none;">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Name</label>
              <input type="text" class="form-control" name="name" placeholder="Full Name">
            </div>
            <div class="form-group col-md-6">
              <label>Phone</label>
              <input type="text" class="form-control" name="phone" placeholder="Phone">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" name="email" placeholder="email@example.com">
            </div>
            <div class="form-group col-md-6">
              <label>Password</label>
              <input type="password" class="form-control" name="password" placeholder="Set a password">
            </div>
          </div>
          <small class="text-muted">The new customer will be saved with role = Customer.</small>
        </div>
      </div>
      <div class="card-footer step-footer">
        <div></div>
        <button type="button" class="btn btn-primary btnNext">Next</button>
      </div>
    </div>

    <!-- Step 2: Room Selection -->
    <div class="card step-pane" data-step="2">
      <div class="card-header">Step 2 — Room Selection</div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Room Type</label>
            <select id="room_type_id" class="form-control">
              <option value="">-- all room types --</option>
              <?php if ($room_types_rs && $room_types_rs->num_rows): while ($rt = $room_types_rs->fetch_assoc()): ?>
                <option value="<?php echo (int)$rt['id']; ?>"><?php echo htmlspecialchars($rt['room_name']); ?></option>
              <?php endwhile; endif; ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>Room</label>
            <select id="room_id" name="room_id" class="form-control" required>
              <option value="">-- choose a room --</option>
              <?php if ($rooms_rs && $rooms_rs->num_rows): while ($r = $rooms_rs->fetch_assoc()): ?>
                <option value="<?php echo (int)$r['id']; ?>" data-type="<?php echo (int)$r['room_type_id']; ?>" data-price="<?php echo htmlspecialchars($r['room_price']); ?>">
                  <?php echo htmlspecialchars('Room #' . $r['room_number'] . ' — Price ' . $r['room_price'] . ' — Status ' . ($r['room_status'] ?? '')); ?>
                </option>
              <?php endwhile; endif; ?>
            </select>
            <small class="text-muted">Filtered by room type; price used to calculate total.</small>
          </div>
        </div>
      </div>
      <div class="card-footer step-footer">
        <button type="button" class="btn btn-secondary btnPrev">Back</button>
        <button type="button" class="btn btn-primary btnNext">Next</button>
      </div>
    </div>

    <!-- Step 3: Dates & Summary -->
    <div class="card step-pane" data-step="3">
      <div class="card-header">Step 3 — Stay Details</div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Check-in</label>
            <input type="date" id="checkin_date" name="checkin_date" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Check-out</label>
            <input type="date" id="checkout_date" name="checkout_date" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Nights</label>
            <input type="number" id="nights" class="form-control" readonly>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Total Amount (auto)</label>
            <input type="text" id="amount" name="amount_display" class="form-control" readonly>
            <small class="text-muted">Calculated as nights × room price (server will re-check).</small>
          </div>
        </div>
      </div>
      <div class="card-footer step-footer">
        <button type="button" class="btn btn-secondary btnPrev">Back</button>
        <button type="button" class="btn btn-primary btnNext">Next</button>
      </div>
    </div>

    <!-- Step 4: Billing & Confirm -->
    <div class="card step-pane" data-step="4">
      <div class="card-header">Step 4 — Billing & Confirmation</div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Payment Method</label>
            <select class="form-control" name="payment_method" required>
              <option value="">-- choose --</option>
              <option>Cash</option>
              <option>Card</option>
              <option>Mobile Banking</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>Approved By (Admin)</label>
            <select class="form-control" name="approved_by" required>
              <option value="">-- select admin --</option>
              <?php if ($admins_rs && $admins_rs->num_rows): while ($a = $admins_rs->fetch_assoc()): ?>
                <option value="<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['name'] . ' — ' . $a['email']); ?></option>
              <?php endwhile; endif; ?>
            </select>
          </div>
        </div>
        <div class="alert alert-warning">On submit, the system will create <strong>Booking → Invoice → Payment → Transaction</strong> and mark the room as <em>booked</em>.</div>
      </div>
      <div class="card-footer step-footer">
        <button type="button" class="btn btn-secondary btnPrev">Back</button>
        <button type="submit" class="btn btn-success">Create Reservation</button>
      </div>
    </div>

  </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  function showStep(n){
    $('.step-pane').hide();
    $('.step-pane[data-step="'+n+'"]').show();
    $('#stepIndicator').text(n);
  }
  var step = 1; showStep(step);
  $('.btnNext').on('click', function(){ if(step < 4){ step++; showStep(step); }});
  $('.btnPrev').on('click', function(){ if(step > 1){ step--; showStep(step); }});

  // Toggle customer mode
  $('input[name="customer_mode"]').on('change', function(){
    if ($(this).val() === 'existing') { $('#existingCustomerBox').show(); $('#newCustomerBox').hide(); }
    else { $('#existingCustomerBox').hide(); $('#newCustomerBox').show(); }
  }).trigger('change');

  // Filter rooms by selected room type
  $('#room_type_id').on('change', function(){
    var type = $(this).val();
    $('#room_id option').each(function(){
      if($(this).val() === '') return; // keep placeholder
      if(!type || String($(this).data('type')) === String(type)) $(this).show(); else $(this).hide();
    });
    $('#room_id').val('');
    updateAmount();
  });

  // Recalculate amount on inputs
  $('#room_id, #checkin_date, #checkout_date').on('change', updateAmount);

  function updateAmount(){
    var checkin = $('#checkin_date').val();
    var checkout = $('#checkout_date').val();
    var price = parseFloat($('#room_id option:selected').data('price') || 0);
    var nights = 0;
    if(checkin && checkout){
      var d1 = new Date(checkin);
      var d2 = new Date(checkout);
      nights = Math.round((d2 - d1) / (1000*60*60*24));
      if(nights < 0) nights = 0;
    }
    $('#nights').val(nights);
    var total = nights * price;
    if(!isFinite(total)) total = 0;
    $('#amount').val(total.toFixed(2));
  }
});
</script>
</body>
</html>
