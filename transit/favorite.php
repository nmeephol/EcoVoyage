<?php
header('Content-Type: application/json');
session_start();

// เช็ค session และข้อมูลที่รับมา
if (!isset($_SESSION['User_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// ตัวอย่างการประมวลผลข้อมูล
$user_id = $_SESSION['User_ID'];
$distance = $data['distance'];
$emission = $data['emission'];
$fare = $data['fare'];
$routesteps = json_encode($data['steps']);

// ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'ecovoyagedb');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO favtransit (user_id, route_distance, route_emission, route_fare, route_steps) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $distance, $emission, $fare, $routesteps);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Favorite saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save']);
}

$stmt->close();
$conn->close();
?>
