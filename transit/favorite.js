function saveToFavorites(index) {
    const route = allRoutesArray[index]; // ตรวจสอบว่า allRoutesArray มีค่าหรือไม่

    if (!route) {
        console.error('Route data not found for index:', index);
        return;
    }

    fetch('/ecovoyageCN/transit/favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: USER_ID,
            distance: route.distance,
            emission: route.emission,
            fare: route.fare,
            steps: route.steps
        })
    })
    .then(response => response.text()) // เปลี่ยนเป็น text เพื่อตรวจสอบ response
    .then(data => {
        console.log('Response from server:', data);
        try {
            const jsonData = JSON.parse(data); // แปลงเป็น JSON
            if (jsonData.success) {
                alert('บันทึกการเดินทางเรียบร้อยแล้ว!');
            } else {
                alert('เกิดข้อผิดพลาดในการบันทึก: ' + jsonData.message);
            }
        } catch (e) {
            console.error('Invalid JSON:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
