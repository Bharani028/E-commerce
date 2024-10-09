<?php
session_start();
include '../includes/db.php';  // Include your database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product data from the form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Prepare to handle the uploaded image
    $uploadDirectory = '../product/';  // Ensure this directory exists and is writable
    $imagePath = '';

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['image']['name'];
        $fileTmpName = $_FILES['image']['tmp_name'];

        // Ensure the file is a valid image type
        $fileType = mime_content_type($fileTmpName);
        if (strpos($fileType, 'image/') === 0) {  // Check if it's an image
            $imagePath = $uploadDirectory . basename($fileName);
            if (!move_uploaded_file($fileTmpName, $imagePath)) {
                echo "Error moving uploaded file.";
            }
        } else {
            echo "Uploaded file is not a valid image.";
        }
    } else {
        echo "Error uploading file.";
    }

    // Prepare SQL statement to insert the product
    $sql = "INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $name, $description, $price, $category_id, $imagePath);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Set a success message in session and redirect to avoid form resubmission
        $_SESSION['success'] = true;
        header('Location: add_product.php');  // Redirect to the same page
        exit();  // Ensure no further code is executed
    } else {
        echo "Error uploading product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Upload Product</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Product Description:</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Product Price:</label>
            <input type="text" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select name="category_id" class="form-control" required>
                <option value="">Select a category</option>
                <option value="1">Paste</option>
                <option value="2">Soap</option>
                <option value="3">Vegetable</option>
                <option value="4">Energy</option>
                <option value="5">Shampoo</option>
                <option value="6">Snacks</option>
                <option value="7">Masala</option>
                <option value="8">Soft Drink</option>
                <option value="9">Baby Products</option>
                <option value="10">Others</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Upload Image:</label>
            <input type="file" name="image" class="form-control-file" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Product</button>
    </form>
</div>

<script>
// Check if PHP set the success flag and trigger the alert
<?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
    Swal.fire({
        icon: 'success',
        title: 'Product Uploaded',
        text: 'The product has been uploaded successfully!',
    }).then(() => {
        // Remove the success flag to prevent the alert from showing again
        <?php unset($_SESSION['success']); ?>
    });
<?php endif; ?>
</script>

</body>
</html>
