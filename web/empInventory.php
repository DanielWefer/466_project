<?php 
session_start(); 
require '../db_connect.php';

if(!isset($_SESSION['emp_id'])){
    header("Location: empLogin.php");
    exit();
}
if (isset($_POST['update-stock'])) {
	$productID = $_POST['product_id'];
	$quantity = $_POST['quantity'];
	$prep = $pdo->prepare("UPDATE Product SET Stock = ? WHERE ProductID = ?");
	$prep->execute([$quantity, $productID]);
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
      <?php if (!empty($_SESSION['user_email'])): ?>
        <div class="user-info"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
      <?php endif; ?>
    </header>
    <main>
      <div class="catalogue">
	   <?php
           $sql = "SELECT * FROM Product ORDER BY ProductID;";
           $result = $pdo->query($sql);
           foreach ($result->fetchAll() as $index => $row) {
             $imgNum = $index + 1;
             echo '<div class="card"
               data-id="'.htmlspecialchars($row['ProductID']).'"
               data-name="'.htmlspecialchars($row['Name']).'"
               data-desc="'.htmlspecialchars($row['Description']).'"
               data-price="'.htmlspecialchars($row['Price']).'"
               data-stock="'.htmlspecialchars($row['Stock']).'"
               data-img="../meatballs/meatball'.$imgNum.'.png"
               style="cursor:pointer;">'."\r\n";
             echo '<img src="../meatballs/meatball'.$imgNum.'.png" alt="'.htmlspecialchars($row['Name']).'">';
             echo '<h3>'.htmlspecialchars($row['Name']).'</h3>'."\r\n";
             echo '<h4 class="description">'.htmlspecialchars($row['Description']).'</h4>'."\r\n";
             echo '<p class="price">$'.$row['Price'].'</p>'."\r\n";

             echo '<form method="POST" action="empInventory.php">';
	     echo '<input type="hidden" name="product_id" value="'.$row['ProductID'].'">';
	     echo '<input type="number" id="quantity" value="'.$row['Stock'].'" name="quantity" min="0">';
             echo '<button type="submit" name="update-stock" class="update-stock">Update Stock</button>';
             echo '</form>';

             echo '<p class="stock in-stock">Stock: '.$row['Stock'].'</p>'."\r\n";
             echo '</div>'."\r\n";
	   }
	   ?>
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
