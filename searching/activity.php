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

// รับค่าจากฟอร์ม
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// สร้างคำสั่ง SQL  
$sql = "";

if ($search_query !== '') {
    $sql = "SELECT 
        activities.Act_ID, 
        activities.Act_Name, 
        activities.Act_Minutes,
        activities.ActGroup_ID,
        activities.Act_ImgSource,
        activities.Act_Description,
        activities_place.ActPlace_Price,
        place.P_Type
    FROM 
        activities
    LEFT JOIN 
        activities_place ON activities.Act_ID = activities_place.Act_ID
    LEFT JOIN 
        place ON activities_place.Place_ID = place.Place_ID
    WHERE 
        activities.Act_Name LIKE '%$search_query%'";

    for ($i=1; $i <= 15; $i++) { 
        $filter = isset($_GET['type'.$i]) ? $_GET['type'.$i] : '';
        if ($filter !== '') {
            $sql .= " OR place.P_Type LIKE '%$filter%'";
        }
    }
} else {
    $sql = "SELECT 
        activities.Act_ID, 
        activities.Act_Name, 
        activities.Act_Minutes,
        activities.ActGroup_ID,
        activities.Act_ImgSource,
        activities.Act_Description,
        activities_place.ActPlace_Price,
        place.P_Type
    FROM 
        activities
    LEFT JOIN 
        activities_place ON activities.Act_ID = activities_place.Act_ID
    LEFT JOIN 
        place ON activities_place.Place_ID = place.Place_ID";
    $count = 0;
    for ($i=1; $i <= 15; $i++) { 
        $filter = isset($_GET['type'.$i]) ? $_GET['type'.$i] : '';
        if ($filter !== '') {
            if ($count == 0) {
                $sql .= " WHERE place.P_Type LIKE '%$filter%'";
                $count = 1;
            } else {
                $sql .= " OR place.P_Type LIKE '%$filter%'";
            }
            
        }
    }
}

if ($sort == "cheapest") {
    $sql .= " ORDER BY activities_place.ActPlace_Price";
} elseif ($sort == "highest") {
    $sql .= " ORDER BY activities_place.ActPlace_Price DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoVoyage - ค้นหาสถานกิจกรรม</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="logo" onclick="document.location='/ecovoyageCN/Project/Homepage.php'">EcoVoyage</div> <!-- โลโก้ -->
        <div>
            <?php if (isset($_SESSION['User_ID'])): ?>
                <a class="loginOrRegist" onclick="logout()">ออกจากระบบ</a>
                <img src="/ecovoyageCN/Project/icon/user/user.png" class="UserIcon" onclick="document.location='/ecovoyageCN/Project/profile.php'">
            <?php else: ?>
                <a class="loginOrRegist" href="/ecovoyageCN/Project/Login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div> 
    <div class="content">
    <h1>ค้นหากิจกรรม</h1>
    <div class="search-container">
        <div class="search-section">
                <form method="GET" action="" id="search">
                    <div class="input-box">
                        <img src="icon/search/activities.png" class="icon" alt="Location Icon">
                        <input type="text" name="search" placeholder="กิจกรรม" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <button class="submitbutton" type="submit">ค้นหา</button>
                </form>
                <button class="filter-button" onclick="openFilterDrawer()" id="filter-button" style="width: 100%;border-radius: 50px;background-color: white;color: black;box-shadow: 3px 3px 5px #dbdbdb;">
                    <img src="icon/search/filter.png" alt="Filter Icon" style="width: 20px; margin-right: 5px;margin-right: 5px;">
                    ตัวกรอง
                </button>
        </div>
    </div>
    
    <div class="row" style="width: 100%;">
    <div class="drawer-overlay" onclick="closeFilterDrawer()" id="filter-overlay"></div>
        <div class="filter-container" id="filter-close">
            <button class="drawer-close" onclick="closeFilterDrawer()">×</button>
                <div class="filter-header">
                    <img src="icon/search/filter.png" class = "icon" alt="Filter Icon">
                    <h3>ตัวกรอง</h3>
                </div>
                <p class="filter-topic">การกรอง</p>
                    <div id = "visible-filters">
                        <input form="search" type="checkbox" name="type1" value="สวนสาธารณะ"> กิจกรรมทางน้ำ<br>
                        <input form="search" type="checkbox" name="type2" value="ป่า"> ถ่ายรูป/ชมวิว<br>
                        <input form="search" type="checkbox" name="type3" value="อุทยาน"> ชุมชน/วิถีชีวิต<br>
                        <input form="search" type="checkbox" name="type4" value="สวนสัตว์"> วัฒนธรรม<br>
                        <input form="search" type="checkbox" name="type5" value="คาเฟ่"> เกษตร/สิ่งแวดล้อม<br>
                    </div>
                    <div id="hidden-filters-type" style="display:none;">
                        <input form="search" type="checkbox" name="type6" value="วัด"> ประวัติศาสตร์<br>
                        <input form="search" type="checkbox" name="type7" value="สวนลอยฟ้า"> การเรียนรู้<br>
                        <input form="search" type="checkbox" name="type8" value="ตลาดน้ำ"> ออกกำลังกาย<br>
                        <input form="search" type="checkbox" name="type9" value="ศิลปะและวัฒนธรรม"> ศิลปะ/งานฝีมือ<br>
                        <input form="search" type="checkbox" name="type10" value="ทะเล"> ชมสัตว์<br>
                        <input form="search" type="checkbox" name="type11" value="พิพิธภัณฑ์"> อาหาร/ช็อปปิ้ง<br>
                        <input form="search" type="checkbox" name="type12" value="สถานที่ประวัติศาสตร์"> กิจกรรมครอบครัว<br>
                        <input form="search" type="checkbox" name="type13" value="แหล่งเรียนรู"> ปั่นจักรยาน<br>
                        <input form="search" type="checkbox" name="type14" value="เกาะ"> ธรรมชาติ/พักผ่อน<br>
                        <input form="search" type="checkbox" name="type15" value="สวนน้ำ"> ดอกไม้<br>
                    </div>

                    <button id="toggle-filters-type" class="toggle" onclick="toggleFilters('type')">แสดง</button>

                    <div class="drawer-buttons">
                        <button type="button" class="btn-cancel" onclick="closeFilterDrawer()">ยกเลิก</button>
                        <button type="button" class="btn-apply" onclick="applyFilters()">ค้นหา</button>
                    </div>
        </div>

        <style>
                #filter-button {
                    display: none;
                }

                #filter-overlay {
                    display: none;
                }

                #filter-close {
                    display: none;
                }

                @media screen and (max-width: 768px) {
                    #filter-button {
                        display: block;
                    }

                    #filter-close {
                        display: block;
                    }
                }
            </style>

        <div class="filter-container">
                <div class="filter-header">
                    <img src="icon/search/filter.png" class = "icon" alt="Filter Icon">
                    <h3>ตัวกรอง</h3>
                </div>
                <p class="filter-topic">การกรอง</p>
                    <div id = "visible-filters">
                        <input form="search" type="checkbox" name="type1" value="สวนสาธารณะ"> กิจกรรมทางน้ำ<br>
                        <input form="search" type="checkbox" name="type2" value="ป่า"> ถ่ายรูป/ชมวิว<br>
                        <input form="search" type="checkbox" name="type3" value="อุทยาน"> ชุมชน/วิถีชีวิต<br>
                        <input form="search" type="checkbox" name="type4" value="สวนสัตว์"> วัฒนธรรม<br>
                        <input form="search" type="checkbox" name="type5" value="คาเฟ่"> เกษตร/สิ่งแวดล้อม<br>
                    </div>
                    <div id="hidden-filters-type" style="display:none;">
                        <input form="search" type="checkbox" name="type6" value="วัด"> ประวัติศาสตร์<br>
                        <input form="search" type="checkbox" name="type7" value="สวนลอยฟ้า"> การเรียนรู้<br>
                        <input form="search" type="checkbox" name="type8" value="ตลาดน้ำ"> ออกกำลังกาย<br>
                        <input form="search" type="checkbox" name="type9" value="ศิลปะและวัฒนธรรม"> ศิลปะ/งานฝีมือ<br>
                        <input form="search" type="checkbox" name="type10" value="ทะเล"> ชมสัตว์<br>
                        <input form="search" type="checkbox" name="type11" value="พิพิธภัณฑ์"> อาหาร/ช็อปปิ้ง<br>
                        <input form="search" type="checkbox" name="type12" value="สถานที่ประวัติศาสตร์"> กิจกรรมครอบครัว<br>
                        <input form="search" type="checkbox" name="type13" value="แหล่งเรียนรู"> ปั่นจักรยาน<br>
                        <input form="search" type="checkbox" name="type14" value="เกาะ"> ธรรมชาติ/พักผ่อน<br>
                        <input form="search" type="checkbox" name="type15" value="สวนน้ำ"> ดอกไม้<br>
                    </div>

                    <button id="toggle-filters-type" class="toggle" onclick="toggleFilters('type')">แสดง</button>
        </div>
        <div class="container" style="align-items: center; justify-content: center;">
        <div class="content-list">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!isset($shown_ids[$row['Act_ID']])) {
                    $shown_ids[$row['Act_ID']] = true;
                    echo '<div class="content-card">';
                        $imagePath = htmlspecialchars($row['Act_ImgSource']);
                        echo '<img class="content-image" src="' . $imagePath . '" alt="Image">';
                        echo '<div class="content-info">';
                            echo '<div class="top-part">';
                            echo '<div class="title">';
                                echo '<h3>กิจกรรม' . htmlspecialchars($row['Act_Name']) . '</h3>';

                                echo '<div class="fav-button">';
                                    if (isset($_SESSION['User_ID'])){
                                        //เพิ่ม favorite
                                        if (array_key_exists('fav' . $row['Act_ID'], $_POST)) {
                                            // ตรวจสอบว่ามีรายการโปรดแล้วหรือไม่
                                            $check_fav_sql = "SELECT 1 
                                                            FROM favoriteact 
                                                            WHERE User_ID = '$userID' 
                                                            AND Act_ID = '" . $row['Act_ID'] . "'";
                                            $fav_check = $conn->query($check_fav_sql);
                                        
                                            if ($fav_check && $fav_check->num_rows > 0) {
                                                // หากมีอยู่แล้ว ไม่ต้องเพิ่ม
                                            } else {
                                                // หากไม่มี ให้เพิ่มเข้าไป
                                                $sql_insert_fav = "INSERT INTO favoriteact (User_ID, Act_ID) 
                                                                VALUES ('$userID', '" . $row['Act_ID'] . "')";
                                                $conn->query($sql_insert_fav);
                                            }
                                        }
                            
                                        // Check if the user clicked to remove the favorite
                                        if (isset($_POST['unfav' . $row['Act_ID']])) {
                                            // Remove from favoritehotel table
                                            $sql_remove_fav = "DELETE FROM favoriteact WHERE User_ID = '$userID' AND Act_ID = '" . $row['Act_ID'] . "'";
                                            $conn->query($sql_remove_fav);
                                        }
                                        
                                        $check_fav_sql = "SELECT 1 
                                        FROM favoriteact 
                                        WHERE User_ID = '$userID' 
                                        AND Act_ID = '" . $row['Act_ID'] . "'";
                                        $fav_check = $conn->query($check_fav_sql);

                                        if ($fav_check && $fav_check->num_rows > 0) {
                                            // Hotel is in favorites, show the "unfavorite" button
                                            echo '<form method="post">';
                                            echo '<button name="unfav' . $row['Act_ID'] . '" id="unfav' . $row['Act_ID'] . '"><img src="icon/search/heart.png" alt="Unfavorite"></button>';
                                            echo '</form>';
                                        } else {
                                            // Hotel is not in favorites, show the "favorite" button
                                            echo '<form method="post">';
                                            echo '<button name="fav' . $row['Act_ID'] . '" id="fav' . $row['Act_ID'] . '"><img src="icon/search/heartNofill.png" alt="Favorite"></button>';
                                            echo '</form>';
                                        }
                                    }else{
                                        echo '<button  onclick="alert(\'กรุณาเข้าสู่ระบบก่อน\')" name="fav' . $row['Act_ID'] . '" id="fav' . $row['Act_ID'] . '"><img src="icon/search/heartNofill.png" alt="Favorite"></button>';
                                    }
                                echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '<p>' . htmlspecialchars($row['Act_Description']) . '</p>';
                            echo '<div class="opentime">';
                                echo '<img src="icon/search/time.png" alt="Icon Time">';
                                echo '<p>ระยะเวลาทำกิจกรรม: ' . htmlspecialchars($row['Act_Minutes']) . ' นาที</p>';
                            echo '</div>';
                        echo '</div>';
         
                    echo '</div>';
                }
            }
        } else {
            echo '<p>ไม่พบข้อมูล</p>';
        }
        ?>
        </div>
    </div>
    </div>
    </div>
</body>
<script src="filtering.js"></script>
<script src="/ecovoyageCN/Project/Logout.js"></script>
</html>
