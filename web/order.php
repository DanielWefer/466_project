<?php
require '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['user_id'];
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
      <?php if (!empty($_SESSION['user_email'])): ?>
        <div class="user-info"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
        <a href="logout.php"><button>Sign Out</button></a>
        <?php endif; ?>
    </header>

    <main>
      <div class="cart-actions">
        <h1>Your Orders</h1>
      </div>
<?php
// Get User Orders
$stmt = $pdo->prepare("
    SELECT * FROM Orders
    WHERE UserID = ?
    ORDER BY OrderID DESC
");
$stmt->execute([$userID]);

$hasOrders = false;
$grandTotal = 0;

// Loop Through Orders
while($order = $stmt->fetch()){
    $hasOrders = true;

    echo "<div class='card'>";
    echo "<h2>Order #{$order['OrderID']}</h2>";
    echo "<p>Status: {$order['Status']}</p>";
    echo "<p>Shipping: {$order['ShippingAddr']}</p>";
    echo "<p>Billing: {$order['BillingInfo']}</p>";

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
    $grandTotal += $orderTotal;
}

// Display User Grand Total
if($hasOrders) {
	echo "<div class='card'>";
	echo "<h2>Total Spent on Meatball Mall: $$grandTotal</h2>";
	echo "</div>";
}

// No Orders Message
if(!$hasOrders){
    echo "<p>You have no orders yet.</p>";
}
?>

      <br>
      <div class="cart-actions">
	<a href="home.php" class="proceed-to-checkout" style="margin-top:5px; margin-bottom:25px;">Back to Store</a>
      </div>
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
