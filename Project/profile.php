<?php
session_start();

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

// ตรวจสอบว่า Session User_ID ถูกตั้งค่าหรือไม่
if (!isset($_SESSION['User_ID'])) {
    header('Location: start.html');
    exit();
}

// ดึง User_ID
$userID = $_SESSION['User_ID'];

include('favorite.php');
include('unfavorite.php');

// ดึงข้อมูลโปรดของผู้ใช้
$favorites = getUserFavorites($userID, $conn);
$transitFavorites = getUserTransitFavorites($userID, $conn);

// จัดการ POST Request สำหรับอัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วนหรือไม่
    if (isset($_POST['U_Name'], $_POST['U_Email'], $_POST['U_Gender'], $_POST['U_AgeRange'])) {
        $name = $_POST['U_Name'];
        $email = $_POST['U_Email'];
        $gender = $_POST['U_Gender'];
        $ageRange = $_POST['U_AgeRange'];
        
        // ถ้ามีการกรอกรหัสผ่านใหม่
        if (!empty($_POST['U_Password'])) {
            $password = password_hash($_POST['U_Password'], PASSWORD_DEFAULT); 
        } else {
            // ถ้าไม่ได้กรอกรหัสผ่านใหม่ ใช้รหัสผ่านเดิมจากฐานข้อมูล
            $password = $user['U_Password'] ? $user['U_Password'] : ''; 
        }

        // อัปเดตข้อมูลในฐานข้อมูล
        $sql = "UPDATE user SET U_Name = ?, U_Email = ?, U_Gender = ?, U_AgeRange = ?, U_Password = ? WHERE User_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $gender, $ageRange, $password, $userID);

        if ($stmt->execute()) {
            // การอัปเดตสำเร็จ
            header('Location: profile.php?status=success');
            exit();
        } else {
            // เกิดข้อผิดพลาด
            $error = "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM user WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // เก็บข้อมูลผู้ใช้ใน $user
} else {
    $error = "ไม่พบข้อมูลผู้ใช้.";
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoVoyage Navigation Bar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!--custome Login & Register css;-->
    <link rel="stylesheet" href="custome.css">
    <link rel="stylesheet" href="profilestyle.css">
</head>
<body>
    <div class="navbar">
        <div class="logo" onclick="document.location='Homepage.php'">EcoVoyage</div> <!-- โลโก้ -->
        <div>
            <?php if (isset($_SESSION['User_ID'])): ?>
                <a class="loginOrRegist" onclick="logout()">ออกจากระบบ</a>
                <img src="icon/user/user.png" class="UserIcon" onclick="document.location='profile.php'">
            <?php else: ?>
                <a class="loginOrRegist" href="/ecovoyageCN/Project/Login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Info-->
    <div class="content">
    <div class="user-content">
        <div class="UserImg">
            <img src="image/user/defultImage.jpg" alt="User Image">
        </div>
        <div class="user-info">
            <div class="header-info">
                <h4>ข้อมูลผู้ใช้</h4>
                <button id="change-info" onclick="showEditForm()" class="change-info">แก้ไข <img src="icon/user/edit.png" class="edit-icon" alt="Edit Icon"></button>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">ชื่อผู้ใช้: </h6>
                    <p class="card-text"><?= htmlspecialchars($user['U_Name']); ?></p>
                    <h6 class="card-title">อีเมล: </h6>
                    <p class="card-text"><?= htmlspecialchars($user['U_Email']); ?></p>
                    <h6 class="card-title">เพศ: </h6>
                    <p class="card-text">
                    <?php
                        if ($user['U_Gender'] === 'female') {
                            echo 'ผู้หญิง';
                        } elseif ($user['U_Gender'] === 'male') {
                            echo 'ผู้ชาย';
                        } else {
                            echo 'ไม่ระบุ';
                        }
                    ?>    
                    </p>
                    <h6 class="card-title">อายุ: </h6>
                    <p class="card-text">
                    <?php
                        if ($user['U_AgeRange'] === 'lower12y') {
                            echo 'ต่ำกว่า 12 ปี';
                        } elseif ($user['U_AgeRange'] === '12to19y') {
                            echo '12 - 19 ปี';
                        }elseif ($user['U_AgeRange'] === '20to39y') {
                            echo '20 - 39 ปี';
                        }elseif ($user['U_AgeRange'] === '40to59y') {
                            echo '40 - 59 ปี';
                        }elseif ($user['U_AgeRange'] === 'morethan60') {
                            echo '60 ปีขึ้นไป';
                        }
                    ?>     
                    </p>
                    <h6 class="card-title">รหัสผ่าน: </h6>
                    <p class="card-text">
                    <?php
                        if (!is_null($user['U_Password']) && $user['U_Password'] !== '') {
                            echo 'ลงทะเบียนเรียบร้อย';
                        } else {
                            echo 'กรุณาลงทะเบียน';
                        }
                    ?>  
                    </p>
                </div>
            </div>
        </div>
        <div id="popup-overlay" style="display: none;">
    <div id="popup-form">
        <form action="profile.php" method="POST">
            <label for="name">ชื่อผู้ใช้:</label>
            <input type="text" name="U_Name" id="name" value="<?= htmlspecialchars($user['U_Name']); ?>" required>

            <label for="email">อีเมล:</label>
            <input type="email" name="U_Email" id="email" value="<?= htmlspecialchars($user['U_Email']); ?>" required>

            <label for="gender">เพศ:</label>
            <select name="U_Gender" id="gender">
                <option value="male" <?= $user['U_Gender'] === 'male' ? 'selected' : ''; ?>>ผู้ชาย</option>
                <option value="female" <?= $user['U_Gender'] === 'female' ? 'selected' : ''; ?>>ผู้หญิง</option>
                <option value="not-specified" <?= $user['U_Gender'] === 'not-specified' ? 'selected' : ''; ?>>ไม่ระบุ</option>
            </select>

            <label for="age-range">ช่วงอายุ:</label>
            <select name="U_AgeRange" id="age-range">
                <option value="lower12y" <?= $user['U_AgeRange'] === 'lower12y' ? 'selected' : ''; ?>>ต่ำกว่า 12 ปี</option>
                <option value="12to19y" <?= $user['U_AgeRange'] === '12to19y' ? 'selected' : ''; ?>>12 - 19 ปี</option>
                <option value="20to39y" <?= $user['U_AgeRange'] === '20to39y' ? 'selected' : ''; ?>>20 - 39 ปี</option>
                <option value="40to59y" <?= $user['U_AgeRange'] === '40to59y' ? 'selected' : ''; ?>>40 - 59 ปี</option>
                <option value="morethan60" <?= $user['U_AgeRange'] === 'morethan60' ? 'selected' : ''; ?>>60 ปีขึ้นไป</option>
            </select>

            <label for="password">รหัสผ่าน:</label>
            <input type="password" name="U_Password" id="password" value="" placeholder="กรอกรหัสผ่านใหม่" required>

            <button type="submit">บันทึก</button>
            <button type="button" onclick="hideEditForm()">ยกเลิก</button>
        </form>
    </div>
    </div>
    </div>
        <!-- Transit plan list -->
        <div class="transit-plan">
            <div class="topic-header">
                <h4>แผนการเดินทางของคุณ</h4>
                <button id="edit-transitplan" class="change-info">แก้ไข <img src="icon/user/edit.png" class="edit-icon" alt="Edit Icon"></button>
            </div>
            <div class="favorite-container">
                <?php 
                $index = 1;
                foreach ($transitFavorites['transits'] as $transit): 
                ?>
                    <div class="fav-card">
                        <h7>แผนเดินทาง <?= $index ?></h7> 
                        <button class="details-btn"
                                onclick="showDetailsPopup(<?= htmlspecialchars(json_encode(['steps' => json_decode($transit['route_steps'], true),'distance' => $transit['route_distance'],'emission' => $transit['route_emission'],'fare' => $transit['route_fare']])) ?>)">
                            รายละเอียด
                        </button>
                    </div>
                <?php 
                    $index++; 
                endforeach; 
                ?>
                <div id="details-popup" class="popup" style="display: none;">
                    <div class="popup-content">
                        <h5>รายละเอียดการเดินทาง</h5>
                        <ul id="popup-details"></ul>
                        <button onclick="closePopup()" class="close-btn">x</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Favorite List-->
        <div class="user-fav">
        <h4>รายการโปรด</h4>
            <div class="fav-hotel">
                <div class="topic-header">
                    <h5>    ที่พักใบประกาศสีเขียว</h5>
                    <button id="edit-hotel" class="change-info">แก้ไข <img src="icon/user/edit.png" class="edit-icon" alt="Edit Icon"></button>
                </div>
                <div class="fav-content">
                    <?php if (!empty($favorites['hotels'])): ?>
                        <?php foreach ($favorites['hotels'] as $hotel): ?>
                            <div class="fav-card">
                                <img src="/ecovoyageCN/searching/<?= htmlspecialchars($hotel['H_ImgSource']); ?>" alt="<?= htmlspecialchars($hotel['H_EngName']); ?>">
                                <h6><?= htmlspecialchars($hotel['H_EngName']); ?></h6>
                                <button class="delete-fav-btn" data-id="<?= htmlspecialchars($hotel['favHotel_ID']); ?>" data-category="hotel">
                                    <img src="icon/user/garbage.png">
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="emp-fav">ไม่มีรายการโปรด</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="fav-place">
                <div class="topic-header">
                    <h5>    สถานที่ท่องเที่ยว</h5>
                    <button id="edit-place" class="change-info">แก้ไข <img src="icon/user/edit.png" class="edit-icon" alt="Edit Icon"></button>
                </div>
                <div class="fav-content">
                    <?php if (!empty($favorites['places'])): ?>
                        <?php foreach ($favorites['places'] as $place): ?>
                            <div class="fav-card">
                                <img src="/ecovoyageCN/searching/<?= htmlspecialchars($place['P_ImgSource']); ?>" alt="<?= htmlspecialchars($place['P_EngName']); ?>">
                                <h6><?= htmlspecialchars($place['P_EngName']); ?></h6>
                                <button class="delete-fav-btn" data-id="<?= htmlspecialchars($place['favPlace_ID']); ?>" data-category="place">
                                <img src="icon/user/garbage.png">
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="emp-fav">ไม่มีรายการโปรด</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="fav-activities">
                <div class="topic-header">
                    <h5>    กิจกรรม</h5>
                    <button id="edit-activity" class="change-info">แก้ไข <img src="icon/user/edit.png" class="edit-icon" alt="Edit Icon"></button>
                </div>
                <div class="fav-content">
                    <?php if (!empty($favorites['activities'])): ?>
                        <?php foreach ($favorites['activities'] as $activities): ?>
                            <div class="fav-card">
                                <img src="/ecovoyageCN/searching/<?= htmlspecialchars($activities['Act_ImgSource']); ?>" alt="<?= htmlspecialchars($activities['Act_Name']); ?>">
                                <h6>กิจกรรม<?= htmlspecialchars($activities['Act_Name']); ?></h6>
                                <button class="delete-fav-btn" data-id="<?= htmlspecialchars($activities['favAct_ID']); ?>" data-category="activity">
                                    <img src="icon/user/garbage.png">
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="emp-fav">ไม่มีรายการโปรด</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="editor.js"></script>
    <script src="Logout.js"></script>

    <script>
        function showDetailsPopup(data) {
            // ตรวจสอบ data ที่รับมา
            console.log(data);

            // แสดง Popup
            document.getElementById('details-popup').style.display = 'flex';

            // แสดงข้อมูล route_steps
            const detailsContainer = document.getElementById('popup-details');
            detailsContainer.innerHTML = ""; // เคลียร์ข้อมูลเดิม

            const distanceItem = document.createElement('li');
            distanceItem.textContent = `ระยะทาง: ${data.distance} km`;
            detailsContainer.appendChild(distanceItem);

            const emissionItem = document.createElement('li');
            emissionItem.textContent = `คาร์บอน: ${data.emission} kgCO2e`;
            detailsContainer.appendChild(emissionItem);

            const fareItem = document.createElement('li');
            fareItem.textContent = `ค่าใช้จ่าย: ${data.fare} บาท`;
            detailsContainer.appendChild(fareItem);

            // แสดงขั้นตอนการเดินทาง
            data.steps.forEach(step => {
                const listItem = document.createElement('li');
                listItem.textContent = step.instruction;
                detailsContainer.appendChild(listItem);
            });
        }

        function closePopup() {
            document.getElementById('details-popup').style.display = 'none';
        }

    </script>

   
</body>
</html> 