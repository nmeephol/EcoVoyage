<?php
session_start(); // เริ่มต้น session


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecovoyagedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $U_Email = $_POST['email'];
    $U_Password = $_POST['password'];

    $sql = "SELECT User_ID, U_Password FROM user WHERE U_Email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $U_Email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($U_Password, $hashed_password)) {
                // ตั้งค่า session
                $_SESSION['User_ID'] = $user_id;
                echo "<script>alert('เข้าสู่ระบบสำเร็จ!'); window.location.href='Homepage.php';</script>";
            } else {
                echo "<script>alert('รหัสผ่านไม่ถูกต้อง! กรุณาลองใหม่');</script>";
            }
        } else {
            echo "<script>alert('ไม่พบบัญชีผู้ใช้งานนี้! กรุณาลองใหม่');</script>";
        }

        $stmt->close();
    } else {
        echo "SQL Error: " . $conn->error . "<br>";
    }
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
        <div class="login-page">
            <div class="form">
              <h1 class="Eco" onclick="document.location='Homepage.html'" style="cursor: pointer;">Ecovoyage</h1><br>
              <form class="login-form" method="POST" action="">
                <input name="email" type="text" placeholder="อีเมลล์" required>
                <input name="password" type="password" placeholder="รหัสผ่าน" required><br><br>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <button type="submit">เข้าสู่ระบบ</button>
                <p class="message">ยังไม่ได้เป็นสมาชิกใช่ไหม? <a href="Register.php">สร้างบัญชี</a></p>
              </form>

            </div>
        </div>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

