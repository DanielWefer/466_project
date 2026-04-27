<?php
require '../db_connect.php';

// TEMPORARY: assume user is 1
$userID = 1;

// Remove Item
if(isset($_POST['remove'])){
    $pdo->prepare("DELETE FROM CartItem WHERE CartID=? AND ProductID=?")
        ->execute([$_POST['cart_id'], $_POST['product_id']]);
}
?>
<?php session_start(); ?>

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
          <!-- FIXED: must use .php -->
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
      <h1>Your Cart</h1>

<?php
// Get Cart Data
$stmt = $pdo->prepare("
    SELECT c.CartID, p.ProductID, p.Name, p.Price, ci.Quantity
    FROM Cart c
    JOIN CartItem ci ON c.CartID = ci.CartID
    JOIN Product p ON ci.ProductID = p.ProductID
    WHERE c.UserID = ?
");
$stmt->execute([$userID]);

$total = 0;
$hasItems = false;

while($row = $stmt->fetch()){
    $hasItems = true;

    $subtotal = $row['Price'] * $row['Quantity'];
    $total += $subtotal;

    echo "<div class='card'>";
    echo "<h3>{$row['Name']}</h3>";
    echo "<p>{$row['Quantity']} × {$row['Price']} = $subtotal</p>";

    echo "<form method='POST'>
            <input type='hidden' name='cart_id' value='{$row['CartID']}'>
            <input type='hidden' name='product_id' value='{$row['ProductID']}'>
            <button type='submit' name='remove'>Remove</button>
          </form>";

    echo "</div><br>";
}


// Empty Cart Message
if(!$hasItems){
    echo "<p>Your cart is empty.</p>";
}

// Total
echo "<h2>Total: $$total</h2>";
?>

      <br>
      <a href="home.php">Continue Shopping</a><br><br>
      <a href="checkout.php"><b>Proceed to Checkout</b></a>

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
