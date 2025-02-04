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
    $sql = "
    SELECT 
        hotel.Hotel_ID, 
        hotel.H_EngName, 
        hotel.H_ThaiName,
        hotel.H_Rating,
        hotel.H_Ranking,
        hotel.H_LowestPrice,
        hotel.H_HighestPrice,
        hotel.H_ImgSource,
        facility.Facility_ID,   
        facility.Facility_Name, 
        facility.F_ImgSource
    FROM 
        hotel
    LEFT JOIN 
        hotelfacility ON hotel.Hotel_ID = hotelfacility.Hotel_ID
    LEFT JOIN 
        facility ON hotelfacility.Facility_ID = facility.Facility_ID
    WHERE 
        hotel.H_ThaiName LIKE '%$search_query%' OR 
        hotel.H_EngName LIKE '%$search_query%'
    ";
    for ($i = 1; $i <= 7; $i++) {
        $filter = isset($_GET['type' . $i]) ? $_GET['type' . $i] : '';
        if ($filter !== '') {
            $sql .= " OR hotel.H_Type LIKE '%$filter%'";
        }
    }
    for ($i = 1; $i <= 10; $i++) {
        $filter = isset($_GET['typef' . $i]) ? $_GET['typef' . $i] : '';
        if ($filter !== '') {
            $sql .= " OR facility.Facility_Name LIKE '%$filter%'";
        }
    }
} else {
    $sql = "
    SELECT 
        hotel.Hotel_ID, 
        hotel.H_EngName, 
        hotel.H_ThaiName,
        hotel.H_Rating,
        hotel.H_Ranking,
        hotel.H_LowestPrice,
        hotel.H_HighestPrice,
        hotel.H_ImgSource,
        facility.Facility_ID,   
        facility.Facility_Name, 
        facility.F_ImgSource
    FROM 
        hotel
    LEFT JOIN 
        hotelfacility ON hotel.Hotel_ID = hotelfacility.Hotel_ID
    LEFT JOIN 
        facility ON hotelfacility.Facility_ID = facility.Facility_ID
    ";
    $count = 0;
    for ($i = 1; $i <= 7; $i++) {
        $filter = isset($_GET['type' . $i]) ? $_GET['type' . $i] : '';
        if ($filter !== '') {
            if ($count == 0) {
                $sql .= " WHERE hotel.H_Type LIKE '%$filter%'";
                $count = 1;
            } else {
                $sql .= " OR hotel.H_Type LIKE '%$filter%'";
            }
        }
    }
    for ($i = 1; $i <= 10; $i++) {
        $filter = isset($_GET['typef' . $i]) ? $_GET['typef' . $i] : '';
        if ($filter !== '') {
            if ($count == 0) {
                $sql .= " WHERE facility.Facility_Name LIKE '%$filter%'";
                $count = 1;
            } else {
                $sql .= " OR facility.Facility_Name LIKE '%$filter%'";
            }
        }
    }
}

if ($sort == "popular") {
    $sql .= " ORDER BY hotel.H_Rating DESC";
} elseif ($sort == "cheapest") {
    $sql .= " ORDER BY hotel.H_LowestPrice, hotel.H_HighestPrice";
} elseif ($sort == "highest") {
    $sql .= " ORDER BY hotel.H_LowestPrice DESC, hotel.H_HighestPrice DESC";
} else {
    $sql .= " ORDER BY hotel.Hotel_ID";
}

$result = $conn->query($sql);

$hotels = []; // กำหนดตัวแปรให้เป็น Array
while ($row = $result->fetch_assoc()) {
    $hotel_id = $row['Hotel_ID'];

    if (!isset($hotels[$hotel_id])) {
        $hotels[$hotel_id] = [
            'id' => $hotel_id,
            'name' => $row['H_EngName'],
            'thai_name' => $row['H_ThaiName'],
            'rating' => $row['H_Rating'],
            'ranking' => $row['H_Ranking'],
            'image' => $row['H_ImgSource'],
            'lowprice' => $row['H_LowestPrice'],
            'highprice' => $row['H_HighestPrice'],
            'facilities' => []
        ];
    }

    if (!empty($row['Facility_ID'])) {
        $hotels[$hotel_id]['facilities'][] = [
            'id' => $row['Facility_ID'],
            'name' => $row['Facility_Name'],
            'icon' => $row['F_ImgSource']
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoVoyage - ค้นหาที่พัก</title>
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
        <h1>ค้นหาที่พักใบประกาศสีเขียว</h1>
        <div class="search-container">
            <div class="search-section">
                <form method="GET" action="" id="search">
                    <div class="input-box">
                        <img src="icon/search/location.png" class="icon" alt="Location Icon">
                        <input type="text" name="search" placeholder="ที่พัก" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <button class="submitbutton" type="submit">ค้นหา</button>
                </form>
                <button class="filter-button" onclick="openFilterDrawer()" id="filter-button" style="width: 100%;border-radius: 50px;background-color: white;color: black;box-shadow: 3px 3px 5px #dbdbdb;">
                    <img src="/icon/filter.png" alt="Filter Icon" style="width: 20px; margin-right: 5px;margin-right: 5px;">
                    ตัวกรอง
                </button>
            </div>
        </div>

        <div class="row" style="width: 100%;">


            <div class="drawer-overlay" onclick="closeFilterDrawer()" id="filter-overlay"></div>

            <div class="filter-container" id="filter-close">
                <button class="drawer-close" onclick="closeFilterDrawer()">×</button>

                <div class="sort-content">
                    <p class="filter-topic">จัดเรียง</p>
                    <input form="search" type="radio" name="sort" value="popular" <?php echo ($sort == 'popular') ?  "checked" : "";  ?>> ระดับความนิยม<br>
                    <input form="search" type="radio" name="sort" value="cheapest" <?php echo ($sort == 'cheapest') ?  "checked" : "";  ?>> ราคาต่ำสุด<br>
                    <input form="search" type="radio" name="sort" value="highest" <?php echo ($sort == 'highest') ?  "checked" : "";  ?>> ราคาสูงสุด<br>
                </div>
                <br>
                <p class="filter-topic">การกรอง</p>
                <div class="filter-hoteltype">
                    <p>ประเภทที่พัก</p>
                    <div id="visible-filters">
                        <input form="search" type="checkbox" name="type1" value="Resort"> Resort<br>
                        <input form="search" type="checkbox" name="type2" value="Hostel"> Hostel<br>
                        <input form="search" type="checkbox" name="type3" value="Hotel"> Hotel<br>
                        <input form="search" type="checkbox" name="type4" value="Motel"> Motel<br>
                        <input form="search" type="checkbox" name="type5" value="Boutigue"> Boutigue<br>
                    </div>
                    <div id="hidden-filters-type" style="display:none;">
                        <input form="search" type="checkbox" name="type6" value="Serviced Residence"> Serviced Residence<br>
                        <input form="search" type="checkbox" name="type7" value="Budget"> Budget<br>
                    </div>
                    <button id="toggle-filters-type" class="toggle" onclick="toggleFilters('type')">แสดง</button>
                </div>
                <br>
                <div class="filter-facilities">
                    <p>สิ่งอำนวยความสะดวก</p>
                    <div id="visible-filters">
                        <input form="search" type="checkbox" name="typef1" value="สระว่ายน้ำ"> สระว่ายน้ำ<br>
                        <input form="search" type="checkbox" name="typef2" value="สปา/ซาวน่า"> สปา/ซาวน่า<br>
                        <input form="search" type="checkbox" name="typef3" value="ฟิตเนส"> ฟิตเนส<br>
                        <input form="search" type="checkbox" name="typef4" value="บริการรถรับส่ง"> บริการรถรับส่ง<br>
                        <input form="search" type="checkbox" name="typef5" value="ซัก/รีด"> ซัก/รีด<br>
                    </div>
                    <div id="hidden-filters-facilities" style="display:none;">
                        <input form="search" type="checkbox" name="typef6" value="ร้านอาหารและบาร์"> ร้านอาหารและบาร์<br>
                        <input form="search" type="checkbox" name="typef7" value="คลับสำหรับเด็ก"> คลับสำหรับเด็ก<br>
                        <input form="search" type="checkbox" name="typef8" value="ห้องประชุม"> ห้องประชุม<br>
                        <input form="search" type="checkbox" name="typef9" value="พื้นที่นันทนาการ"> พื้นที่นันทนาการ<br>
                        <input form="search" type="checkbox" name="typef10" value="พื้นที่สำหรับสัตว์เลี้ยง"> พื้นที่สำหรับสัตว์เลี้ยง<br>
                    </div>
                    <button id="toggle-filters-facilities" class="toggle" onclick="toggleFilters('facilities')">แสดง</button>
                </div>
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
                    <img src="icon/search/filter.png" class="icon" alt="Filter Icon">
                    <h3>ตัวกรอง</h3>
                </div>
                <div class="sort-content">
                    <p class="filter-topic">จัดเรียง</p>
                    <input form="search" type="radio" name="sort" value="popular" <?php echo ($sort == 'popular') ?  "checked" : "";  ?>> ระดับความนิยม<br>
                    <input form="search" type="radio" name="sort" value="cheapest" <?php echo ($sort == 'cheapest') ?  "checked" : "";  ?>> ราคาต่ำสุด<br>
                    <input form="search" type="radio" name="sort" value="highest" <?php echo ($sort == 'highest') ?  "checked" : "";  ?>> ราคาสูงสุด<br>
                </div>
                <br>
                <p class="filter-topic">การกรอง</p>
                <div class="filter-hoteltype">
                    <p>ประเภทที่พัก</p>
                    <div id="visible-filters">
                        <input form="search" type="checkbox" name="type1" value="Resort"> Resort<br>
                        <input form="search" type="checkbox" name="type2" value="Hostel"> Hostel<br>
                        <input form="search" type="checkbox" name="type3" value="Hotel"> Hotel<br>
                        <input form="search" type="checkbox" name="type4" value="Motel"> Motel<br>
                        <input form="search" type="checkbox" name="type5" value="Boutigue"> Boutigue<br>
                    </div>
                    <div id="hidden-filters-type" style="display:none;">
                        <input form="search" type="checkbox" name="type6" value="Serviced Residence"> Serviced Residence<br>
                        <input form="search" type="checkbox" name="type7" value="Budget"> Budget<br>
                    </div>
                    <button id="toggle-filters-type" class="toggle" onclick="toggleFilters('type')">แสดง</button>
                </div>
                <br>
                <div class="filter-facilities">
                    <p>สิ่งอำนวยความสะดวก</p>
                    <div id="visible-filters">
                        <input form="search" type="checkbox" name="typef1" value="สระว่ายน้ำ"> สระว่ายน้ำ<br>
                        <input form="search" type="checkbox" name="typef2" value="สปา/ซาวน่า"> สปา/ซาวน่า<br>
                        <input form="search" type="checkbox" name="typef3" value="ฟิตเนส"> ฟิตเนส<br>
                        <input form="search" type="checkbox" name="typef4" value="บริการรถรับส่ง"> บริการรถรับส่ง<br>
                        <input form="search" type="checkbox" name="typef5" value="ซัก/รีด"> ซัก/รีด<br>
                    </div>
                    <div id="hidden-filters-facilities" style="display:none;">
                        <input form="search" type="checkbox" name="typef6" value="ร้านอาหารและบาร์"> ร้านอาหารและบาร์<br>
                        <input form="search" type="checkbox" name="typef7" value="คลับสำหรับเด็ก"> คลับสำหรับเด็ก<br>
                        <input form="search" type="checkbox" name="typef8" value="ห้องประชุม"> ห้องประชุม<br>
                        <input form="search" type="checkbox" name="typef9" value="พื้นที่นันทนาการ"> พื้นที่นันทนาการ<br>
                        <input form="search" type="checkbox" name="typef10" value="พื้นที่สำหรับสัตว์เลี้ยง"> พื้นที่สำหรับสัตว์เลี้ยง<br>
                    </div>
                    <button id="toggle-filters-facilities" class="toggle" onclick="toggleFilters('facilities')">แสดง</button>
                </div>
            </div>
            <div class="container">
                <div class="content-list">
                    <?php
                    foreach ($hotels as $hotel) {
                        echo '<div class="content-card">';
                        echo '<img class="content-image" src="' . htmlspecialchars($hotel['image']) . '" alt="Hotel Image">';
                        echo '<div class="content-info">';
                        echo '<div class="top-part">';
                        echo '<div class="title">';
                        echo '<h3 class="hotel-title">' . htmlspecialchars($hotel['name']) . '</h3>';
                        echo '<div class="fav-button">';
                        if (isset($_SESSION['User_ID'])) {
                            //เพิ่ม favorite
                            if (array_key_exists('fav' . $hotel['id'], $_POST)) {
                                // ตรวจสอบว่ามีรายการโปรดแล้วหรือไม่
                                $check_fav_sql = "SELECT 1 
                                                        FROM favoritehotel 
                                                        WHERE User_ID = '$userID' 
                                                        AND Hotel_ID = '" . $hotel['id'] . "'";
                                $fav_check = $conn->query($check_fav_sql);

                                if ($fav_check && $fav_check->num_rows > 0) {
                                    // หากมีอยู่แล้ว ไม่ต้องเพิ่ม
                                } else {
                                    // หากไม่มี ให้เพิ่มเข้าไป
                                    $sql_insert_fav = "INSERT INTO favoritehotel (User_ID, Hotel_ID) 
                                                            VALUES ('$userID', '" . $hotel['id'] . "')";
                                    $conn->query($sql_insert_fav);
                                }
                            }

                            // Check if the user clicked to remove the favorite
                            if (isset($_POST['unfav' . $hotel['id']])) {
                                // Remove from favoritehotel table
                                $sql_remove_fav = "DELETE FROM favoritehotel WHERE User_ID = '$userID' AND Hotel_ID = '" . $hotel['id'] . "'";
                                $conn->query($sql_remove_fav);
                            }

                            $check_fav_sql = "SELECT 1 
                                    FROM favoritehotel 
                                    WHERE User_ID = '$userID' 
                                    AND Hotel_ID = '" . $hotel['id'] . "'";
                            $fav_check = $conn->query($check_fav_sql);

                            if ($fav_check && $fav_check->num_rows > 0) {
                                // Hotel is in favorites, show the "unfavorite" button
                                echo '<form method="post">';
                                echo '<button name="unfav' . $hotel['id'] . '" id="unfav' . $hotel['id'] . '"><img src="icon/search/heart.png" alt="Unfavorite"></button>';
                                echo '</form>';
                            } else {
                                // Hotel is not in favorites, show the "favorite" button
                                echo '<form method="post">';
                                echo '<button name="fav' . $hotel['id'] . '" id="fav' . $hotel['id'] . '"><img src="icon/search/heartNofill.png" alt="Favorite"></button>';
                                echo '</form>';
                            }
                        } else {
                            echo '<button  onclick="alert(\'กรุณาเข้าสู่ระบบก่อน\')" name="fav' . $hotel['id'] . '" id="fav' . $hotel['id'] . '"><img src="icon/search/heartNofill.png" alt="Favorite"></button>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '<p>' . htmlspecialchars($hotel['thai_name']) . '</p>';
                        echo '<div class="rating">';
                        echo '<img src="icon/search/star.png" alt="Star Icon">';
                        echo '<p>' . htmlspecialchars($hotel['rating']) . '</p>';
                        echo '</div>';

                        if (!empty($hotel['facilities'])) { // แสดงเฉพาะถ้ามี Facilities
                            echo '<ul class="facilities">';
                            foreach ($hotel['facilities'] as $facility) {
                                echo '<li>';
                                echo '<img src="' . htmlspecialchars($facility['icon']) . '" alt="' . htmlspecialchars($facility['name']) . '">';
                                echo htmlspecialchars($facility['name']);
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>No facilities available</p>';
                        }
                        echo '</div>';

                        echo '<div class="bottom-part">';
                        echo '<div class="hotelranking">';
                        echo '<img src="image/Hotel/Rank/' . strtolower($hotel['ranking']) . '.png" alt="Rank Image">';
                        echo '<p>Green Hotel ' . htmlspecialchars($hotel['ranking']) . '</p>';
                        echo '</div>';

                        echo '<div class="Price">';
                        echo '<img src="icon/search/price.png" alt="Icon price">';
                        echo '<p>ราคา</p>';
                        echo '<div class="getPrice">';
                        echo '<p>' . htmlspecialchars($hotel['lowprice']) . ' - ' . htmlspecialchars($hotel['highprice']) . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';

                        echo '</div>';
                        echo '</div>';
                    }

                    ?>
                </div>
            </div>
        </div>
</body>
<script src="filtering.js"></script>
<script src="/ecovoyageCN/Project/Logout.js"></script>

</html>