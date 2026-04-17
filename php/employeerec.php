<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
header('Content-Type: application/json');

$conn = new PDO("mysql:host=localhost;dbname=db_bakeryrecord_system", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//  DELETE EMPLOYEE 
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $employee_id = $_POST['employee_id'] ?? '';

    if (empty($employee_id)) {
        echo json_encode(["status" => "error", "message" => "No ID provided"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tb_employee WHERE employee_id = ?");
    $stmt->execute([$employee_id]);

    echo json_encode([
        "status" => $stmt->rowCount() > 0 ? "success" : "error",
        "message" => $stmt->rowCount() > 0 ? "Employee deleted successfully" : "Employee not found"
    ]);
    exit;
}

// FETCH EMPLOYEES
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $stmt = $conn->query("SELECT employee_id, firstname, lastname, role, rate_per_hour FROM tb_employee ORDER BY employee_id ASC");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "records" => $employees
    ]);
    exit;
}

//  INSERT EMPLOYEE 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_GET['action']) || $_GET['action'] !== 'delete' && $_GET['action'] !== 'update')) {
    $lastname = $_POST['lastname'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $role = $_POST['role'] ?? '';
    $rate_per_hour = floatval($_POST['rate_per_hour'] ?? 0);

    if (!$lastname || !$firstname) {
        echo json_encode(["status" => "error", "message" => "Lastname, and Firstname are required"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO tb_employee (lastname, firstname, role, rate_per_hour) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lastname, $firstname, $role, $rate_per_hour]);

        echo json_encode(["status" => "success", "message" => "Employee added successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to add employee: " . $e->getMessage()]);
    }
    exit;
}

// UPDATE EMPLOYEE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $employee_id = $_POST['employee_id'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $role = $_POST['role'] ?? '';
    $rate_per_hour = floatval($_POST['rate_per_hour'] ?? 0);

    if (!$employee_id || !$lastname || !$firstname) {
        echo json_encode(["status" => "error", "message" => "Employee ID, Lastname, and Firstname are required"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE tb_employee SET lastname=?, firstname=?, role=?, rate_per_hour=? WHERE employee_id=?");
        $stmt->execute([$lastname, $firstname, $role, $rate_per_hour, $employee_id]);

        echo json_encode(["status" => "success", "message" => "Employee updated successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to update employee: " . $e->getMessage()]);
    }
    exit;
}
?>