<?php
$servername = "localhost";
$username = "root";        
$password = "";            
$dbname = "ecovoyagedb";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $U_Name = $_POST['fullname'];
    $U_Gender = $_POST['gender'];
    $U_AgeRange = $_POST['age'];
    $U_Email = $_POST['email'];
    $U_Password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (U_Name, U_Gender, U_AgeRange, U_Email, U_Password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $U_Name, $U_Gender, $U_AgeRange, $U_Email, $U_Password);

    if ($stmt->execute()) {
        echo "<script>alert('ลงทะเบียนสำเร็จ!'); window.location.href='Login.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--custome Login & Register css;-->
    <link rel="stylesheet" href="custome.css">
    <link href="hello.js">
</head>

<body class="general-body">
    <div class="register-page">
        <div class="form">  
            <form class="register-form" method="POST" action="">
                <h1 onclick="document.location='Homepage.html'" style="cursor: pointer;">Ecovoyage</h1><br>
                <input name="fullname" type="text" placeholder="ชื่อ-นามสกุล" required />
                <div class="select-container">
                    <select name="gender" class="select-box" required>
                        <option disabled selected>เพศ</option>
                        <option value="male">ชาย</option>
                        <option value="female">หญิง</option>
                        <option value="none">ไม่ระบุ</option>
                    </select>
                    &nbsp;
                    <select name="age" class="select-box" required>
                        <option disabled selected>อายุ</option>
                        <option value="lower12y">ต่ำกว่า 12 ปี</option>
                        <option value="12to19y">12-19 ปี</option>
                        <option value="20to39y">20-39 ปี</option>
                        <option value="40to59y">40-59 ปี</option>
                        <option value="morethan60">60 ปี ขึ้นไป</option>
                    </select>
                </div>
                <br>
                <input name="password" type="password" placeholder="รหัสผ่าน" required />
                <input name="email" type="text" placeholder="อีเมลล์" required /><br><br>
                <button type="submit">สร้างบัญชี</button>
                <p class="message">เป็นสมาชิกแล้วใช่ไหม? <a href="Login.php">เข้าสู่ระบบ</a></p>
            </form>
        </div>
    </div>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
