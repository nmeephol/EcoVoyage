**ชื่อโครงงาน (ไทย):** การวางแผนท่องเที่ยวเชิงอนุรักษ์ธรรมชาติ

**Project Title (Eng):** ECO-FRIENDLY TRAVEL PLANNING

**อาจารย์ที่ปรึกษาโครงงาน:** ผศ. ดร. อรจิรา สิทธิศักดิ์

**ผู้จัดทำโครงงาน:**
1. นางสาวนภัสวรรณ มีผล 6309681390 napatsawan.mee@dome.tu.ac.th
2. นางสาวอคิราภ์ เกื้อหนุน 6309681481 akira.kun@dome.tu.ac.th


   
Manual / Instructions for your projects starts here !

# ตารางเนื้อหา

2. [รายการที่ต้องติดตั้ง](#รายการที่ต้องติดตั้ง)  
3. [การใช้ google maps API](#การใช้googlemapsAPI)  
4. [วิธีการใช้งานแอปพลิเคชัน](#วิธีการใช้งานแอปพลิเคชัน)
5. [โครงสร้างโปรเจกต์](#โครงสร้างโปรเจกต์)
   
---------

# รายการที่ต้องติดตั้ง
   
   	1. ติดตั้ง XAMPP  
	   • ดาวน์โหลดจาก apachefriends.org
        
	2. ดาวน์โหลดไฟล์ข้อมูล database
	   • เปิด command
	   • ใช้คำสั่ง node -v
	
	3. ตรวจสอบว่าเครื่องมีการติดตั้ง node.js แล้วหรือไม่
	   • ดาวน์โหลกไฟล์ 'ecovoyagedb.sql' บน github
           • start Apache และ MySQL บน XAMPP
           • Run 'http://localhost:8080/phpmyadmin/' บน browser
           • import ไฟล์ 'ecovoyagedb.sql'

 	5. หากยังไม่มีการติดตั้ง node.js
  	   • ดาวน์โหลดจาก nodejs.org
	   • ตรวจสอบการติดตั้ง : node -v
	   • ติดตั้ง defualt package : npm init -y
	   • ติดตั้ง express : npm install express --save

---------

# การใช้googlemapsAPI

Google Maps API ช่วยให้คุณสามารถแสดงแผนที่, คำนวณเส้นทาง, ค้นหาสถานที่ใกล้เคียง, และใช้งานฟีเจอร์อื่นๆ ของแผนที่ Google บนเว็ปแอพลิเคชัน

การสมัครและขอ API Key :
   1. ไปที่ [Google Cloud Console](https://console.cloud.google.com/)
   2. สร้างโปรเจ็กต์ใหม่ หรือเลือกโปรเจ็กต์ที่มีอยู่แล้ว
   3. ไปที่ **APIs & Services** > **Library** แล้วค้นหา "Google Maps JavaScript API"
   4. ค้นหาและเปิดใช้งาน API ทั้งหมดต่อไปนี้

 	- Directions API
	- Distance Matrix API
	- Geocoding API
	- Places API
	- Maps JavaScript API 
 
   5. ไปที่ **Credentials** แล้วสร้าง **API Key**
   6. คัดลอก API Key ที่ได้รับมา

การตั้งค่าคีย์ในระบบ :
   1. ไฟล์ EcovoyageApp/php_app/ecovoyageCN/transit/script.js บรรทัดที่ 10 แทนที่ YOUR API KEY ด้วย Api Key ที่ได้คัดลอกมา : const apiKey = 'YOUR API KEY';
   1. ไฟล์ EcovoyageApp/flask_app/app.py บรรทัดที่ 12 แทนที่ <กรอกคีย์ googlemap ตรงนี้> ด้วย Api Key ที่ได้คัดลอกมา : API_KEY = "<กรอกคีย์ googlemap ตรงนี้>"

ข้อควรระวัง :
   ควรตรวจสอบว่าเปลี่ยนคีย์ตามไฟล์ที่กำหนดแล้ว และที่สำคัญ enable API ทั้งหมดที่ระบุ ไม่เช่นนั้นแอพจะไม่สามารถทำงานหรือคำนวณใดๆได้

---------

# วิธีการใช้งานแอปพลิเคชัน
   1. เปิดใช้งาน XAMPP
   2. Start Apache และ MySQL
   3. เปิด terminal และ cd ไปยัง /ecovoyageCN/transit
   4. ใช้คำสั่ง node server.js เพื่อ Run server ที่เชื่อมต่อกับ API
   5. terminal จะแสดงผลว่าทำงานอยู่บน port 3000
   6. เปิด browser
   7. Run ระบบผ่าน 'http://localhost:8080/ecovoyageCN/Project/start.html'

---------

# โครงสร้างโปรเจกต์

	      EcovoyageApp/
		├── php_app/
		│   └── ecovoyageCN/
		│       ├── Project/
		│       │   ├── icon/
		│       │   ├── image/
		│       │   ├── custome.css
		│       │   ├── profilestyle.css
		│       │   ├── Start.html                # หน้าเริ่มต้นการใช้งาน
		│       │   ├── Register.php
		│       │   ├── Homepage.php
		│       │   ├── Login.php
		│       │   ├── Logout.php
		│       │   ├── profile.php
		│       │   ├── favorite.php              # การเพิ่มรายการโปรดเข้าฐานข้อมูล
		│       │   ├── unfavorite.php            # การลบรายการโปรดเข้าฐานข้อมูล
		│       │   ├── editor.js
		│       │   ├── hello.js
		│       │   ├── Logout.js
		│       │   └── main.js
		│       ├── searching/                    # การค้นหาสถานที่ท่องเที่ยว กิจกรรม และที่พัก
		│       │   ├── icon/
		│       │   ├── image/
		│       │   ├── activity.php
		│       │   ├── hotel.php
		│       │   ├── place.php
		│       │   └── filtering.js
		│       └── transit/                      # การค้นหาเส้นทางการเดินทาง
		│           ├── icon/
		│           ├── node_modules/
		│           ├── index.php                 # หน้าแสดงผลสำหรับใช้ค้นหาการเดินทาง
		│           ├── favorite.php
		│           ├── favorite.js
		│           ├── server.js
		│           ├── fare.js                   # คำนวณค่าใช้จ่ายการเดินทาง
		│           └── emission.js               # คำนวณปริมาณคาร์บอนฟุทพริ้น


