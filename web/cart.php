<?php
require '../db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Remove item
if(isset($_POST['remove'])){
    $pdo->prepare("DELETE FROM CartItem WHERE CartID=? AND ProductID=?")
        ->execute([$_POST['cart_id'], $_POST['product_id']]);
}

// Update Quantity
if(isset($_POST['update'])){
    $cartID = $_POST['cart_id'];
    $productID = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity']));

    $stmt = $pdo->prepare("UPDATE CartItem SET Quantity = ? WHERE CartID = ? AND ProductID = ?");
    $stmt->execute([$quantity, $cartID, $productID]);
}
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
          <li><a href="home.php"><b>Home</b></a></li>
          <li><a href="login.php"><b>Login</b></a></li>
          <li><a href="cart.php"><b>Cart</b></a></li>
          <li><a href="order.php"><b>Orders</b></a></li>
        </ul>
      </nav>
      <?php if(!empty($_SESSION['user_email'])): ?>
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

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $hasItems = true;

    $subtotal = $row['Price'] * $row['Quantity'];
    $total += $subtotal;

    echo "<div class='card'>";
    echo "<h3>{$row['Name']}</h3>";
    echo "<p>{$row['Quantity']} × {$row['Price']} = $subtotal</p>";

    // Update/ Remove
    echo "<form method='POST' style='border:none; margin-top:10px;'>
            <input type='hidden' name='cart_id' value='{$row['CartID']}'>
            <input type='hidden' name='product_id' value='{$row['ProductID']}'>

            <div style='display:flex; gap:10px; justify-content:center; align-items:center;'>
                <input type='number' name='quantity' value='{$row['Quantity']}' min='1' style='width:70px; padding:5px;'>

                   <button type='submit' name='update' style='width:auto;'>Update</button>
                   <button type='submit' name='remove' style='width:auto; background-color:#f44336;'>Remove</button>
            </div>
          </form>";

    echo "</div><br>";
}


// Empty Cart Message
if(!$hasItems){
    echo "<p>Your cart is empty.</p>";
}

// Total
if($hasItems){
    echo "<h2>Total: $$total</h2>";
    echo "<a href='checkout.php'><b>Proceed to Checkout</b></a><br><br>";
}
?>

      <br>
      <a href="home.php">Continue Shopping</a><br><br>


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
