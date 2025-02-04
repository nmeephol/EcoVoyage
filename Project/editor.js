//Edite User Info
document.addEventListener('DOMContentLoaded', function () {
    function showEditForm() {
        document.getElementById('popup-overlay').style.display = 'flex'; // แสดง popup
    }

    function hideEditForm() {
        document.getElementById('popup-overlay').style.display = 'none'; // ซ่อน popup
    }

    // เชื่อมโยงฟังก์ชันกับปุ่ม
    document.getElementById('change-info').addEventListener('click', showEditForm);
    document.querySelector('#popup-form button[type="button"]').addEventListener('click', hideEditForm);
});

//Edit Favorite
document.getElementById('edit-hotel').addEventListener('click', function () {
    const hotelSection = this.closest('.fav-hotel'); // เลือกเฉพาะส่วนที่เกี่ยวข้อง
    const favCards = hotelSection.querySelectorAll('.fav-card');
    const editButtons = hotelSection.querySelectorAll('.fav-card .delete-fav-btn');

    favCards.forEach((card, index) => {
        // สลับการแสดงปุ่มแก้ไขและคลาส dimmed
        if (editButtons[index].style.display === 'none' || editButtons[index].style.display === '') {
            editButtons[index].style.display = 'block'; // แสดงปุ่ม
            card.classList.add('dimmed'); // เพิ่มความจาง
        } else {
            editButtons[index].style.display = 'none'; // ซ่อนปุ่ม
            card.classList.remove('dimmed'); // เอาความจางออก
        }
    });
});

document.getElementById('edit-place').addEventListener('click', function () {
    const placeSection = this.closest('.fav-place'); // เลือกเฉพาะส่วนที่เกี่ยวข้อง
    const favCards = placeSection.querySelectorAll('.fav-card');
    const editButtons = placeSection.querySelectorAll('.fav-card .delete-fav-btn');

    favCards.forEach((card, index) => {
        // สลับการแสดงปุ่มแก้ไขและคลาส dimmed
        if (editButtons[index].style.display === 'none' || editButtons[index].style.display === '') {
            editButtons[index].style.display = 'block'; // แสดงปุ่ม
            card.classList.add('dimmed'); // เพิ่มความจาง
        } else {
            editButtons[index].style.display = 'none'; // ซ่อนปุ่ม
            card.classList.remove('dimmed'); // เอาความจางออก
        }
    });
});

document.getElementById('edit-activity').addEventListener('click', function () {
    const activitySection = this.closest('.fav-activities'); // เลือกเฉพาะส่วนที่เกี่ยวข้อง
    const favCards = activitySection.querySelectorAll('.fav-card');
    const editButtons = activitySection.querySelectorAll('.fav-card .delete-fav-btn');

    favCards.forEach((card, index) => {
        // สลับการแสดงปุ่มแก้ไขและคลาส dimmed
        if (editButtons[index].style.display === 'none' || editButtons[index].style.display === '') {
            editButtons[index].style.display = 'block'; // แสดงปุ่ม
            card.classList.add('dimmed'); // เพิ่มความจาง
        } else {
            editButtons[index].style.display = 'none'; // ซ่อนปุ่ม
            card.classList.remove('dimmed'); // เอาความจางออก
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-fav-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const category = this.getAttribute('data-category');

            if (confirm('คุณต้องการลบรายการโปรดนี้ใช่หรือไม่?')) {
                fetch('unfavorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'delete_favorite',
                        id: id, // Replace with the actual ID
                        category: category // Replace with the actual category (hotel, place, activity)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('ลบรายการโปรดเรียบร้อยแล้ว');
                        this.closest('.fav-card').remove();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาด กรุณาลองอีกครั้ง');
                });
                
            }
        });
    });
});
