function logout() {
    fetch('/ecovoyageCN/Project/Logout.php', {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(response => {
        if (response.ok) {
            window.location.reload(); // โหลดหน้าใหม่หลังจากออกจากระบบ
        } else {
            alert('ไม่สามารถออกจากระบบได้ กรุณาลองอีกครั้ง');
        }
    })
    .catch(error => console.error('Error:', error));
}