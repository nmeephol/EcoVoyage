<?php
session_start(); // เริ่ม session

$conn = new mysqli('localhost', 'root', '', 'ecovoyagedb');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// หากผู้ใช้ล็อกอิน ให้ดึงข้อมูล
if (isset($_SESSION['User_ID'])) {
    $userID = $_SESSION['User_ID'];
    $sql = "SELECT * FROM user WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoVoyage Navigation Bar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--custome Login & Register css;-->
    <link rel="stylesheet" href="custome.css">
    <link href="hello.js">
</head>
<body class="bodyhomepage">
    <h1 class="welcomemessage">มาท่องเที่ยวแบบเป็นมิตร<br>ต่อสิ่งแวดล้อมพร้อมกัน!</h1>
    <div class="rectangleEXplace">
        <div class="lumgroup">
            <img src="image/lum.jpg" class="lum"><br><br>
            <p class="HeadEXplace">เที่ยวสวนลุมพินี</p>
            <p class="detailEXplace">สวนลุมพินีเป็นสวนสาธารณะแห่งแรกของประเทศไทย<br>มีขนาดใหญ่ ประกอบด้วยสระน้ำ เส้นทางวิ่ง/เดิน<br>ห้องสมุด ศูนย์กีฬาในที่ร่ม ซุ้มขายน้ำ และฟู้ดคอร์ท<br>รายล้อมไปด้วยต้นไม้ใหญ่ พรรณไม้ใหม่และโบราณ<br>เป็นที่ชื่นชอบของหลายคนที่รักธรรมชาติ และสุขภาพ</p>
        </div> 
        <p>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </p>
        <div class="bicyclegroup">
            <img src="image/bicycle.jpg" class="bicycle"><br><br>
            <p class="HeadEXplace">ปั่นจักรยานกลางสวน</p>
            <p class="detailEXplace">แค่นั่งเรือฝั่งแม่น้ำเจ้าพระยาจากท่าเรือคลองเตย<br>มายังฝั่งบางกะเจ้า ก็มีเส้นทางปั่นจักรยานชิลล์ๆ<br>ตัดผ่านสวนมะพร้าว สวนป่าชุมชน และตลาดน้ำ<br>บางผึ้ง ได้รับการยกย่องว่าเป็น The best Urban <br>Oasis of Asia จากนิตยสาร Time</p>
        </div> 
    </div>
    <div class="rectanglebotton">
        <button class="circle-button">
            <img src="icon/Home/travelplanning.png" onclick="document.location='plan.html'" alt="วางแผนการท่องเที่ยว"> <!-- รูปภาพในปุ่ม -->
            <p class="listhomepage"><br>วางแผนการท่องเที่ยว</p>
        </button>
        <button class="circle-button">
            <img src="icon/Home/journeyplanning.png" alt="วางแผนการเดินทาง" onclick="document.location='http://localhost:3000/'">
            <p class="listhomepage"><br>วางแผนการเดินทาง</p>
        </button>
        <button class="circle-button">
            <img src="icon/Home/hotel.png" alt="ที่พัก" onclick="document.location='/ecovoyageCN/searching/hotel.php'"> 
            <p class="listhomepage"><br>ที่พัก</p>
        </button>
        <button class="circle-button">
            <img src="icon/Home/placeholder.png" alt="สถานที่ท่องเที่ยว" onclick="document.location='/ecovoyageCN/searching/place.php'"> 
            <p class="listhomepage"><br>สถานที่ท่องเที่ยว</p>
        </button>
        <button class="circle-button">
            <img src="icon/Home/activities.png" alt="กิจกรรม" onclick="document.location='/ecovoyageCN/searching/activity.php'"> 
            <p class="listhomepage"><br>กิจกรรม</p>
        </button>
    </div>
    <div class="navbar">
        <div class="logo" onclick="document.location='Homepage.html'">EcoVoyage</div> <!-- โลโก้ -->
        <div>
            <?php if (isset($_SESSION['User_ID'])): ?>
                <a class="loginOrRegist" onclick="logout()">ออกจากระบบ</a>
                <img src="icon/user/user.png" class="UserIcon" onclick="document.location='profile.php'">
            <?php else: ?>
                <a class="loginOrRegist" href="/ecovoyageCN/Project/Login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>
</body>
<script src="Logout.js"></script>
</html> 