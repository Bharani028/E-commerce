<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$colgate='product/colgate.jpeg';
$hamam='product/hamam.jpeg';
$sql = "INSERT INTO products (name, description, price, category_id, image) VALUES 
('colgate', 'Description for product 11', 45.00, 3, '$colgate'),
('hamam', 'Description for product 12', 85.50, 5, '$hamam')";

if ($conn->query($sql) === TRUE) {
    echo "New products added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
