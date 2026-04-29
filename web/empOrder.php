<?php
session_start();
require '../db_connect.php';

if(!isset($_SESSION['emp_id'])){
    header("Location: empLogin.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $orderID = $_POST['order_id'];
  $newStatus = $_POST['status'];
    
  $stmt = $pdo->prepare("UPDATE Orders SET Status = ? WHERE OrderID = ?");
  $stmt->execute([$newStatus, $orderID]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_notes'])) {
    $orderID = $_POST['order_id'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("UPDATE Orders SET Notes = ? WHERE OrderID = ?");
    $stmt->execute([$notes, $orderID]);
}

// Get the selected status filter
$statusFilter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Meatball Store</title>
    <link rel="stylesheet" href="../style.css">
  </head>
  <body>
    <header>
      <div>
        <h1>Meatball Mall</h1>
        <p>Satisfying all your meatball needs since yesterday.</p>
      </div>
      <nav>
        <ul>
          <!-- FIXED LINKS -->
          <li><a href="home.php"><b>Home</b></a></li>
          <li><a href="login.php"><b>Login</b></a></li>
          <li><a href="cart.php"><b>Cart</b></a></li>
          <li><a href="order.php"><b>Orders</b></a></li>
        </ul>
      </nav>
      <?php if (!empty($_SESSION['emp_name'])): ?>
        <div class="user-info"><?= htmlspecialchars($_SESSION['emp_name']) ?></div>
        <a href="logout.php"><button>Sign Out</button></a>
      <?php endif; ?>
    </header>

    <main>
      <h1 class="cart-actions">Order Fulfillment</h1>

        <!-- Status Filter -->
        <form method="POST" style="border: none; margin-bottom: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 8px; width: 100%; display: flex; justify-content: center; align-items: center;">
          <label for="status_filter" style="font-weight: bold; font-size: 1.1em; margin-right:10px;">Filter by Status:</label>
          <select name="status_filter" id="status_filter" onchange="this.form.submit()" style="margin-left: 10px; min-width:160px;">
            <option value="">All Orders</option>
            <option value="Processing" <?php echo $statusFilter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="Cancelled" <?php echo $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            <option value="Shipping" <?php echo $statusFilter === 'Shipping' ? 'selected' : ''; ?>>Shipping</option>
            <option value="Delivered" <?php echo $statusFilter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
          </select>
        </form>
<?php
// Get User Orders
$query = "
  SELECT o.*, c.Email, c.Name 
  FROM Orders o
  JOIN Customer c ON o.UserID = c.UserID
";

if ($statusFilter) {
  $query .= " WHERE o.Status = ?";
}

$query .= " ORDER BY o.OrderID DESC";

$stmt = $pdo->prepare($query);

if ($statusFilter) {
  $stmt->execute([$statusFilter]);
} else {
  $stmt->execute();
}

$hasOrders = false;

// Loop Through Orders
while($order = $stmt->fetch()){
    $hasOrders = true;

    echo "<div class='card'>";
    echo "<h2>Order #{$order['OrderID']}</h2>";
  echo "<p><strong>Customer Name:</strong> {$order['Name']}</p>";
  echo "<p><strong>Customer Email:</strong> {$order['Email']}</p>";
    
   // Current status text
  echo "<p><strong>Current Status:</strong> {$order['Status']}</p>";

  // Status dropdown for updating
  echo "<form method='POST' style='margin: 10px 0; display:flex; justify-content:center; align-items:center; gap:8px; width:100%;'>";
  echo "<label for='status_{$order['OrderID']}' style='margin-right:6px;'>Modify Status:</label>";
  echo "<select name='status' id='status_{$order['OrderID']}' style='min-width:140px;'>";
  $statuses = ['Processing', 'Cancelled', 'Shipping', 'Delivered'];
  foreach ($statuses as $status) {
    $selected = $order['Status'] === $status ? 'selected' : '';
    echo "<option value='{$status}' {$selected}>{$status}</option>";
  }
  echo "</select>";
  echo "<input type='hidden' name='order_id' value='{$order['OrderID']}'>";
  echo "<button type='submit' name='update_status' style='width:auto; padding:6px 10px; font-size:0.9em;'>Update</button>";
  echo "</form>";

    echo "<p>Shipping: {$order['ShippingAddr']}</p>";
    echo "<p>Billing: {$order['BillingInfo']}</p>";
    echo "<p>Notes: {$order['Notes']}</p>";

    echo "<form method='POST' style='margin: 10px 0; display:flex; justify-content: center; align-items:center; gap:8px; width:100%;'>";
    echo "<label for='notes_{$order['OrderID']}' style='margin-right:6px;'>Modify Notes:</label>";
    echo "<input type='text' name='notes' id='notes_{$order['OrderID']}' maxlength='200' value=\"" . htmlspecialchars($order['Notes'] ?? '') . "\" style='min-width:300px;'>";
    echo "<input type='hidden' name='order_id' value='{$order['OrderID']}'>";
    echo "<button type='submit' name='update_notes' style='width:auto; padding:6px 10px; font-size:0.9em;'>Update Notes</button>";
    echo "</form>";


    echo "<h3>Items:</h3>";

    // Get The Items In This Order
    $items = $pdo->prepare("
        SELECT p.Name, p.Price, oi.Quantity
        FROM OrderItem oi
        JOIN Product p ON oi.ProductID = p.ProductID
        WHERE oi.OrderID = ?
    ");
    $items->execute([$order['OrderID']]);

    $orderTotal = 0;

    while($item = $items->fetch()){
        $subtotal = $item['Price'] * $item['Quantity'];
        $orderTotal += $subtotal;

        echo "<p>{$item['Name']} - {$item['Quantity']} × {$item['Price']} = $subtotal</p>";
    }

    echo "<h4>Order Total: $$orderTotal</h4>";
    echo "</div><br>";
}


// No Orders Message
if(!$hasOrders){
    echo "<h3 class='cart-actions'>You have no orders yet.</h3>";
}
?>
      </br>
    </main>

    <footer>
      <ul>
        <li><a href="empLogin.php"><b>Employee Login</b></a></li>
        <li><a href="empInventory.php"><b>Inventory Management</b></a></li>
        <li><a href="empOrder.php"><b>Order Fulfillment</b></a></li>
      </ul>
    </footer>
  </body>
</html>
