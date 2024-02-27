<?php
session_start();

$servername = "localhost";
$username = "root";
$password = " ";
$dbname = "online_bookstore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getBooks($category)
{
    global $conn;
    $sql = "SELECT * FROM $category";
    $result = $conn->query($sql);

    $books = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }

    return $books;
}

function addToCart($title)
{
    global $conn;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $existingBook = array_search($title, array_column($_SESSION['cart'], 'title'));

    if ($existingBook !== false) {
        echo "This book is already in the cart!";
    } else {
        $sql = "SELECT * FROM fiction WHERE title = '$title'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $_SESSION['cart'][] = $book;

            $cartInsertQuery = "INSERT INTO cart (BookName, Quantity, Action, Price) VALUES (
                '{$book['title']}', 
                1, 
                'Added to Cart', 
                {$book['price']}
            )";
            $conn->query($cartInsertQuery);

            echo $book['title'] . " has been added to the cart!";
        } else {
            echo "Book not found!";
        }
    }
}

$fictionBooks = getBooks('fiction');
$nonFictionBooks = getBooks('nonfiction');
$mysteryBooks = getBooks('mystery');
$popupMessage = isset($_SESSION['popup_message']) ? $_SESSION['popup_message'] : "";
unset($_SESSION['popup_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Wander - Shop</title>
    <link rel="stylesheet" href="shop.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
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

    <section class="category-fiction" id="category-fiction">
        <h2>Fiction</h2>
        <div class="book-container" id="bookContainerFiction">
            <?php foreach ($fictionBooks as $book) : ?>
                <div class="book">
                    <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <p>Author: <?php echo $book['author']; ?></p>
                    <p>Price: ₹<?php echo number_format($book['price'], 2); ?></p>
                    <button onclick="addToCart('<?php echo $book['title']; ?>')">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="category-nonfiction" id="category-nonfiction">
        <h2>Non-Fiction</h2>
        <div class="book-container" id="bookContainerNonFiction">
            <?php foreach ($nonFictionBooks as $book) : ?>
                <div class="book">
                    <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <p>Author: <?php echo $book['author']; ?></p>
                    <p>Price: ₹<?php echo number_format($book['price'], 2); ?></p>
                    <button onclick="addToCart('<?php echo $book['title']; ?>')">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="category-mystery" id="category-mystery">
        <h2>Mystery</h2>
        <div class="book-container" id="bookContainerMystery">
            <?php foreach ($mysteryBooks as $book) : ?>
                <div class="book">
                    <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <p>Author: <?php echo $book['author']; ?></p>
                    <p>Price: ₹<?php echo number_format($book['price'], 2); ?></p>
                    <button onclick="addToCart('<?php echo $book['title']; ?>')">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2023 Book Wander. All Rights Reserved.</p>
    </footer>

</body>

</html>