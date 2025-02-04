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
    <title>EcoVoyage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
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
        <div class="container search-bar">
            <form id="searchForm" class="d-flex">
                <div class="input-box">
                    <img src="icon/location.png" class="icon" alt="Location Icon">
                    <input id="startLocation" class="form-control" type="text" placeholder="จุดเริ่มต้น" aria-label="Start location" required>
                </div>
                <div class="arrow"><img src="icon/arrow.png" alt="arrow Icon"></div>
                <div class="input-box">  
                    <img src="icon/location.png" class="icon" alt="Location Icon">
                    <input id="destination" class="form-control" type="text" placeholder="จุดหมาย" aria-label="Destination" required>
                </div>
                <button class="btn btn-primary" type="submit">ค้นหา</button>
            </form>
            <button class="filter-button" onclick="openFilterDrawer()" id="filter-button" style="width: 100%;border-radius: 50px;background-color: white;color: black;box-shadow: 3px 3px 5px #dbdbdb;">
                    <img src="icon/filter.png" alt="Filter Icon" style="width: 20px; margin-right: 5px;margin-right: 5px;">
                    ตัวกรอง
            </button>

        <div class="row" style="width: 100%;">
            <div class="drawer-overlay" onclick="closeFilterDrawer()" id="filter-overlay"></div>

            <div class="filter-container" id="filter-close">
                <button class="drawer-close" onclick="closeFilterDrawer()">×</button>

                <div class="filter-header">
                    <img src="icon/filter.png" class = "icon" alt="Filter Icon">
                    <h3>ตัวกรอง</h3>
                </div>
                <p class="filter-topic">จัดเรียง</p>
                <input form="search" type="radio" name="sort" value="shortestDistance"> ระยะทางที่สั้นที่สุด<br>
                <input form="search" type="radio" name="sort" value="lowestCO2"> ปริมาณคาร์บอนต่ำที่สุด<br>
                <input form="search" type="radio" name="sort" value="lowestPrice"> ค่าใช้จ่ายน้อยที่สุด<br>
                
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
                    <img src="icon/filter.png" class = "icon" alt="Filter Icon">
                    <h3>ตัวกรอง</h3>
                </div>
                <p class="filter-topic">จัดเรียง</p>
                <input form="search" type="radio" name="sort" value="shortestDistance"> ระยะทางที่สั้นที่สุด<br>
                <input form="search" type="radio" name="sort" value="lowestCO2"> ปริมาณคาร์บอนต่ำที่สุด<br>
                <input form="search" type="radio" name="sort" value="lowestPrice"> ค่าใช้จ่ายน้อยที่สุด<br>
            </div>
            
            <div class="allRoute">
                <div class="container route-suggest" id="routeSuggest">
                </div>
                <div class="container route-info" id="routeInfo">
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    const USER_ID = <?php echo json_encode($userID); ?>;
    let allRoutesArray = [];

    document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const startLocation = document.getElementById('startLocation').value;
    const destination = document.getElementById('destination').value;

    const fetchRouteData = async () => {
        try {
            const response = await fetch(`http://localhost:3000/route?origin=${encodeURIComponent(startLocation)}&destination=${encodeURIComponent(destination)}`);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }

            const data = await response.json();
            if (data.routes && data.routes.length > 0) {
                let allRoutesHtml = ''; 
    
                data.routes.forEach((route, index) => {
                    const legs = route.legs[0]; // Assuming each route has one leg
                    const steps = legs.steps || [];
                    let totalMin = 0;
                    let totalMax = 0;
                    let emission = 0;
                    let totalEmission = 0;

                    let routeInfoHtml = `
                        <div class="eachtransit">
                        <h5>แผนการเดินทางที่ ${index + 1}</h5>
                        <div class = "content-card">
                        <div class = "total-step">
                        <div class="transit-flow"></div>
                        <ul>
                    `;


                    steps.forEach(step => {
                        routeInfoHtml += `<li class="route-step">${step.html_instructions}`;
                        const travelMode = step.travel_mode
                        //console.log('travel mode:', travelMode);

                        if (step.transit_details) {
                            const transitDetails = step.transit_details;
                            const shortName = transitDetails.line.short_name || '';
                            const line = transitDetails.line.name;
                            const distanceInKm = parseFloat(step.distance.value) / 1000;
                            const numStop = transitDetails.num_stops;
                            const departure = transitDetails.departure_stop.name;
                            const arrival = transitDetails.arrival_stop.name;
                            const type = transitDetails.line.vehicle?.type || 'Unknown';
                            emission = calculateEmission(type, shortName, distanceInKm);
                            
                            console.log('transitDetails:', transitDetails);

                            let { minfare, maxfare} = calculateFare(shortName, line, distanceInKm, numStop, departure, arrival, type);

                            totalMin += minfare;
                            totalMax += maxfare;

                            totalEmission += emission;

                            // console.log('typeof calculateFare:', typeof calculateFare);
                            // console.log('short name', shortName);
                            // console.log(`Estimated fare is: ${estimatedFare}`);
                            // console.log('distanceInKm is',distanceInKm);
                            // console.log('Calling calculateFare with:', shortName, distanceInKm);

                            //console.log('stop:',numStop);
                            //console.log('type:',type);
                            //console.log('Transit Details:', step.transit_details);

                            routeInfoHtml += `
                            <div class="details">
                                <ul>
                                    <li class="line">${transitDetails.line.short_name || transitDetails.line.name}</li>
                                    <li>${departure} -> ${arrival}</li>
                                </ul>
                            </div>
                            `;
                        }
                        routeInfoHtml += `</li>`;
                        
                    });
                    
                    routeInfoHtml += `</div>`;

                    let fareDisplay = '';
                    if (totalMax === 0) {
                        fareDisplay = `${totalMin} บาท`;
                    } else {
                        fareDisplay = `${totalMin} - ${totalMax} บาท`; 
                    }

                    routeInfoHtml += `
                        </ul>
                        <div class="total-summary">
                        <div class="total-diatance">
                            <p class="header">ระยะทาง</p>
                            <img src="icon/distance.png" alt="price Icon" id="distance-pic">
                            <p class="route-distance">${legs.distance.text}</p>
                        </div>
                        <div class="total-emission">
                            <p class="header">ปริมาณคาร์บอน</p>
                            <img src="icon/co2.png" alt="price Icon">
                            <p class="route-emission">${totalEmission.toFixed(2)} kgCO2e</p>
                        </div>
                        <div class="total-fare">
                            <p class="header">ค่าใช้จ่าย</p>
                            <img src="icon/pricing.png" alt="price Icon">
                            <p class="route-cost">${fareDisplay}</p>
                        </div>
                        </div>
                        <div class="fav-button">
                            <button class="btn-fav" onclick="saveToFavorites(${index})"><img src="/ecovoyageCN/searching/icon/search/heartNofill.png" alt="Favorite"></button>
                        </div>
                    `;

                    routeInfoHtml += `</div>`;
                    routeInfoHtml += `</div>`;

                    const routesteps = route.legs[0].steps.map(step => ({
                        instruction: step.html_instructions,
                        distance: step.distance.text
                    }));

                    allRoutesArray.push({
                        html: routeInfoHtml,
                        distance: parseFloat(legs.distance.text),
                        emission: totalEmission.toFixed(2),
                        fare: totalMin,
                        steps: routesteps 
                    });


                    allRoutesHtml += routeInfoHtml;

                    console.log('Route Data:', route);
                    console.log('All Routes:', allRoutesArray);
                    console.log('Steps:', routesteps);
                });
                

                document.getElementById('routeInfo').innerHTML = allRoutesHtml;
            } else {
                document.getElementById('routeInfo').innerHTML = `<p>No route found.</p>`;
            }
        } catch (error) {
            console.error('Error fetching route data:', error);
            document.getElementById('routeInfo').innerHTML = `<p>Error fetching route data: ${error.message}</p>`;
        }
    };

    fetchRouteData();

    document.querySelectorAll('input[name="sort"]').forEach(radio => {
    radio.addEventListener('change', function() {
        sortAndRenderRoutes(this.value);
    });
});

function sortAndRenderRoutes(type) {
    let sortedRoutes = [...allRoutesArray];

    if (type === 'shortestDistance') {
        sortedRoutes.sort((a, b) => a.distance - b.distance);
    } else if (type === 'lowestCO2') {
        sortedRoutes.sort((a, b) => a.emission - b.emission);
    } else if (type === 'lowestPrice') {
        sortedRoutes.sort((a, b) => a.fare - b.fare);
    }

    // แสดงผลใหม่
    document.getElementById('routeInfo').innerHTML = sortedRoutes.map(route => route.html).join('');
}

    });

function logout() {
    fetch('/logout', { method: 'POST' })
        .then(() => {
            window.location.href = '/ecovoyageCN/Project/Login.php';
        })
        .catch(error => console.error('Logout failed:', error));
}

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="emission.js"></script>
    <script src="fare.js"></script>
    <script src="/ecovoyageCN/searching/filtering.js"></script>
    <script src="astar.js"></script>
    <script src="favorite.js"></script>
</html>
