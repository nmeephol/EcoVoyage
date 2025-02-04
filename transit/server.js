const express = require('express');
const fetch = require('node-fetch'); 
const path = require('path'); 
const app = express();
const PORT = 3000;

const cors = require('cors');
app.use(cors());

const apiKey = 'YOUR API KEY';

app.get('/', (req, res) => {
    res.redirect('http://localhost:8080/ecovoyageCN/transit/index.php'); 
});

app.get('/route', async (req, res) => {
    const { origin, destination } = req.query;

    if (!origin || !destination) {
        return res.status(400).json({ error: 'Origin and destination are required' });
    }

    try {
        const response = await fetch(`https://maps.googleapis.com/maps/api/directions/json?origin=${origin}&destination=${destination}&mode=transit&key=${apiKey}&alternatives=true`);
        const data = await response.json();

        console.log('Google Maps API response:', JSON.stringify(data, null, 2)); // Debugging log

        if (data.status !== 'OK') {
            console.error('Google Maps API error:', data);
            throw new Error(data.error_message || 'Google Maps API Error');
        }

        
        res.json(data);
    } catch (error) {
        console.error('Server error:', error.message);
        res.status(500).json({ error: error.message });
    }
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});



