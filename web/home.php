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
      <div class="catalogue">
	   <?php
	   require '../db_connect.php';
	   $counter = 1;
	   $sql = "SELECT COUNT(*) FROM Product;";
	   $result = $pdo->query($sql);
	   $row = $result->fetch();
	   $total = $row[0];
	   $sql = "SELECT * FROM Product ORDER BY ProductID;";
	   $result = $pdo->query($sql);
	   while($counter <= $total){
		   echo '<div class="card">'."\r\n";
		   echo '<img src="../meatballs/meatball'.$counter.'.png" alt="'.$row['Name'].'">';
		   $row = $result->fetch();
		   echo '<h3>'.$row['Name'].'</h3>'."\r\n";
		   echo '<h4 class="description">'.$row['Description'].'</h4>'."\r\n";
		   echo '<p class="price">$'.$row['Price'].'</p>'."\r\n";
		   echo '<button class="add-to-cart">Add to Cart</button>'."\r\n";
		   echo '<p class="stock in-stock">Stock: '.$row['Stock'].'</p>'."\r\n";
		   echo '</div>'."\r\n";
		   $counter++;
	   }
	   ?>
	<!-- copy/paste to add more meatball cards... -->
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
