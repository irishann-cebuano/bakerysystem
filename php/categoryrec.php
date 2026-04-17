<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
$conn = new PDO("mysql:host=localhost;dbname=db_bakeryrecord_system", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $category_id = $_POST['category_id'] ?? '';

    if (empty($category_id)) {
        echo json_encode(["status" => "error", "message" => "No ID provided"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tb_category WHERE category_id = ?");
    $stmt->execute([$category_id]);

    echo json_encode([
        "status" => $stmt->rowCount() > 0 ? "success" : "error",
        "message" => $stmt->rowCount() > 0 ? "Category deleted successfully" : "Category not found"
    ]);
    exit;
}



// FETCH
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $stmt = $conn->query("SELECT category_id, category_name FROM tb_category ORDER BY category_id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no categories exist, insert default ones
    if (empty($categories)) {
        $defaultCategories = ['Supply', 'Ingredients', 'Labor', 'Utilities', 'Production/Packaging'];
        $stmt = $conn->prepare("INSERT INTO tb_category (category_name) VALUES (?)");
        
        foreach ($defaultCategories as $category) {
            $stmt->execute([$category]);
        }
        
        // Fetch again after inserting defaults
        $stmt = $conn->query("SELECT category_id, category_name FROM tb_category ORDER BY category_id ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        "status" => "success",
        "records" => $categories
    ]);
    exit;
}

// INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_GET['action']) || $_GET['action'] !== 'delete')) {

    $category_name = $_POST['category_name'] ?? '';

    if ( !$category_name) {
        echo json_encode([
            "status" => "error",
            "message" => "Category Name is required"
        ]);
        exit;
    }

    try {
        // INSERT new category
        $stmt = $conn->prepare("INSERT INTO tb_category (category_name) VALUES (?)");
        $stmt->execute([$category_name]);

        echo json_encode([
            "status" => "success",
            "message" => "Category added successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to save category: " . $e->getMessage()
        ]);
    }

    exit;
}
?>