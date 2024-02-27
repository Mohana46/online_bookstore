<?php
session_start();

function showCart()
{
    $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $cartBody = "";
    $totalPrice = 0;

    foreach ($cartData as $index => $book) {
        $row = "<tr>";
        $row .= "<td>" . ($index + 1) . "</td>";
        $row .= "<td>" . $book['title'] . "</td>";
        $row .= "<td>";
        $row .= "<button onclick='decreaseQuantity($index)'>-</button>";
        $row .= "<span>" . ($book['quantity'] ?? 1) . "</span>";
        $row .= "<button onclick='increaseQuantity($index)'>+</button>";
        $row .= "</td>";
        $row .= "<td><button onclick='removeFromCart($index)'>Remove</button></td>";
        $row .= "<td>₹" . number_format(($book['price'] * ($book['quantity'] ?? 1)), 2) . "</td>";
        $row .= "</tr>";

        $cartBody .= $row;
        $totalPrice += $book['price'] * ($book['quantity'] ?? 1);
    }

    $response = [
        'cartBody' => $cartBody,
        'totalPrice' => number_format($totalPrice, 2)
    ];

    echo json_encode($response);
}

function removeFromCart($index)
{
    $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    unset($cartData[$index]);
    $_SESSION['cart'] = array_values($cartData);
    showCart();
}

function increaseQuantity($index)
{
    $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    if (isset($cartData[$index])) {
        $cartData[$index]['quantity'] = ($cartData[$index]['quantity'] ?? 1) + 1;
    }
    $_SESSION['cart'] = $cartData;
    showCart();
}

function decreaseQuantity($index)
{
    $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    if (isset($cartData[$index]) && $cartData[$index]['quantity'] > 1) {
        $cartData[$index]['quantity'] -= 1;
    }
    $_SESSION['cart'] = $cartData;
    showCart();
}

function showPopup()
{
    $cartData = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $popupContent = "<h2>Order Summary</h2>";

    foreach ($cartData as $book) {
        $popupContent .= $book['title'] . " - " . ($book['quantity'] ?? 1) . "<br>";
    }

    $popupContent .= "<br><strong>Total Price:</strong> ₹" . $_POST['totalPrice'];
    echo $popupContent;
}

function clearCart()
{
    unset($_SESSION['cart']);
    showCart();
    echo "Thank You! 😊";
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'showCart':
                showCart();
                break;
            case 'removeFromCart':
                removeFromCart($_POST['index']);
                break;
            case 'increaseQuantity':
                increaseQuantity($_POST['index']);
                break;
            case 'decreaseQuantity':
                decreaseQuantity($_POST['index']);
                break;
            case 'showPopup':
                showPopup();
                break;
            case 'clearCart':
                clearCart();
                break;
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="cart.css">
</head>

<body>
    <header class="header">
        <div class="header-1">
            <a href="#" class="logo"><i class="fas fa-book"></i> Book Wander</a>
            <div class="navbar">
                <a href="index.html">Home</a>
                <a href="index.html#section2">Category</a>
                <a href="shop.php">Shop</a>
                <a href="cart.php">Cart</a>
            </div>
        </div>
    </header>

    <section class="cart">
        <h2>Shopping Cart</h2>
        <table id="cartTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Book Name</th>
                    <th>Quantity</th>
                    <th>Action</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="cartBody"></tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td>Total</td>
                    <td id="totalPriceCell"> ₹0.00</td>
                </tr>
            </tfoot>
        </table>
        <button id="buy" onclick="showPopup()">Buy</button>
    </section>
    <div class="overlay" id="overlay" onclick="closePopup()"></div>
    <div class="popup" id="popup">
        <p id="popupText"></p>
        <button id="ok" onclick="clearCart()">OK</button>
    </div>
    <footer class="footer">
        <p>&copy; 2023 Book Wander. All Rights Reserved.</p>
    </footer>
    <script src="cart.js"></script>
</body>

</html>