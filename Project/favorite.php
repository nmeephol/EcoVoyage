<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecovoyagedb";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getUserFavorites($userID, $conn) {
    // สร้าง array สำหรับข้อมูลผลลัพธ์
    $favorites = [
        'hotels' => [],
        'places' => [],
        'activities' => [],

    ];

    // ดึงข้อมูลที่พักโปรด
    $hotelSql = "SELECT favoritehotel.Hotel_ID AS favHotel_ID, hotel.H_EngName, hotel.H_ImgSource 
                FROM favoritehotel 
                JOIN hotel ON favoritehotel.Hotel_ID = hotel.Hotel_ID
                WHERE favoritehotel.User_ID = ?";
    $hotelStmt = $conn->prepare($hotelSql);
    $hotelStmt->bind_param("i", $userID);
    $hotelStmt->execute();
    $hotelResult = $hotelStmt->get_result();
    while ($row = $hotelResult->fetch_assoc()) {
        $favorites['hotels'][] = $row;
    }
    $hotelStmt->close();

    // ดึงข้อมูลสถานที่โปรด
    $placeSql = "SELECT favoriteplace.Place_ID AS favPlace_ID, place.P_EngName, place.P_ImgSource 
                FROM favoriteplace 
                JOIN place ON favoriteplace.Place_ID = place.Place_ID
                WHERE favoriteplace.User_ID = ?";
    $placeStmt = $conn->prepare($placeSql);
    $placeStmt->bind_param("i", $userID);
    $placeStmt->execute();
    $placeResult = $placeStmt->get_result();
    while ($row = $placeResult->fetch_assoc()) {
        $favorites['places'][] = $row;
    }
    $placeStmt->close();

    // ดึงข้อมูลกิจกรรมที่โปรด
    $actSql = "SELECT favoriteact.Act_ID AS favAct_ID, activities.Act_Name, activities.Act_ImgSource
                FROM favoriteact 
                JOIN activities ON favoriteact.Act_ID = activities.Act_ID
                WHERE favoriteact.User_ID = ?";
    $actStmt = $conn->prepare($actSql);
    $actStmt->bind_param("i", $userID);
    $actStmt->execute();
    $actResult = $actStmt->get_result();
    while ($row = $actResult->fetch_assoc()) {
        $favorites['activities'][] = $row;
    }
    $actStmt->close();

    return $favorites;
}

function getUserTransitFavorites($userID, $conn) {
    $favorites = [];

    // ดึงข้อมูลจาก favtransit
    $sql = "SELECT * FROM favtransit WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $favorites['transits'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $favorites;
}

?>
