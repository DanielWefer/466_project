<?php 
session_start(); 

require '../db_connect.php';
$message = "";

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $userID = $_SESSION['user_id'];
    $productID = $_POST['product_id'];

    // check product stock
    $stmt = $pdo->prepare("SELECT Stock FROM Product WHERE ProductID = ?");
    $stmt->execute([$productID]);
    $product = $stmt->fetch();

    if ($product && $product['Stock'] > 0) {
        // find the customer's cart
        $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
        $stmt->execute([$userID]);
        $cart = $stmt->fetch();

        // create a cart if the customer doesn't have one yet
        if ($cart) {
            $cartID = $cart['CartID'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO Cart (UserID) VALUES (?)");
            $stmt->execute([$userID]);
            $cartID = $pdo->lastInsertId();
        }
        
        // check if the product is already in the cart
        $stmt = $pdo->prepare("SELECT Quantity FROM CartItem WHERE CartID = ? AND ProductID = ?");
        $stmt->execute([$cartID, $productID]);
        $cartItem = $stmt->fetch();

        if ($cartItem) {
            // increase quantity if already in the cart
            // HARDCODED TO 1, MAYBE ADD QUANTITY LATER
            if ($cartItem['Quantity'] < $product['Stock']) {
                $stmt = $pdo->prepare("UPDATE CartItem SET Quantity = Quantity + 1 WHERE CartID = ? AND ProductID = ?");
                $stmt->execute([$cartID, $productID]);
                $message = "Item added to cart.";
            } else {
                $message = "You already have all available stock in your cart.";
            }
        } else {
            // insert a new cart item if it's not already present
            $stmt = $pdo->prepare("INSERT INTO CartItem (CartID, ProductID, Quantity) VALUES (?, ?, 1)");
            $stmt->execute([$cartID, $productID]);
            $message = "Item added to cart.";
        }
    } else {
        $message = "That item is out of stock.";
    }
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
        <a href="logout.php"><button>Sign Out</button></a>
      <?php endif; ?>
    </header>

    <main>
      <?php
      if ($message != "") {
          echo "<p style='text-align:center;'><b>$message</b></p>";
      }
      ?>
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
             
             echo '<form method="POST" action="home.php">';
             echo '<input type="hidden" name="product_id" value="'.$row['ProductID'].'">';
             echo '<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>';
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

    <!-- Individual Meatball Popup elements -->
    <div id="modal-overlay" style="display:none;">
      <div id="modal-content">
        <button id="modal-close">✕</button>
        <img id="modal-img" src="" alt="">
        <h2 id="modal-name"></h2>
        <p id="modal-desc"></p>
        <p id="modal-price"></p>
        <form method="POST" action="home.php">
          <input type="hidden" name="product_id" id="modal-product-id" value="">
          <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
        </form>
        <p id="modal-stock"></p>
     </div>
    </div>

    <!-- JavaScript for popup functionality -->
    <script>
      document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-cart')) return;

            document.getElementById('modal-img').src = this.dataset.img;
            document.getElementById('modal-name').textContent = this.dataset.name;
            document.getElementById('modal-desc').textContent = this.dataset.desc;
            document.getElementById('modal-price').textContent = '$' + this.dataset.price;
            document.getElementById('modal-stock').textContent = 'Stock: ' + this.dataset.stock;
            document.getElementById('modal-product-id').value = this.dataset.id;
            document.getElementById('modal-overlay').style.display = 'flex';
        });
      });

      // Close on X button or clicking outside the modal
      document.getElementById('modal-close').addEventListener('click', closeModal);
      document.getElementById('modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
      });

      function closeModal() {
        document.getElementById('modal-overlay').style.display = 'none';
      }
    </script>
  </body>
</html>
