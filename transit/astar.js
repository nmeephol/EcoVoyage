async function fetchRoutes() {
    const originInput = document.querySelector('input[id="startLocation"]');
    const destinationInput = document.querySelector('input[id="destination"]');

    if (!originInput || !destinationInput) {
        console.error('Input fields not found.');
        return;
    }

    const startLocation = originInput.value;
    const destination = destinationInput.value;

    try {
        const response = await fetch(`http://localhost:3000/route?origin=${startLocation}&destination=${destination}`);
        const data = await response.json();

        if (!data || !data.summary) {
            throw new Error("No valid route data received");
        }

        renderSuggestionRoute(data);
    } catch (error) {
        console.error('Error fetching routes:', error);
        document.getElementById('routeSuggest').innerHTML = `<p>ไม่พบเส้นทางที่เหมาะสม</p>`;
    }
}

function renderSuggestionRoute(route) {
    console.log("Rendering Route:", route);

    const routeSuggestDiv = document.getElementById('routeSuggest');

    if (!routeSuggestDiv) {
        console.error("Element routeSuggest not found in DOM");
        return;
    }

    routeSuggestDiv.innerHTML = `
        <div class="route-suggestion">
            <h3>${route.summary}</h3>
            <p>ระยะทาง: ${route.distance} km</p>
            <p>ปริมาณคาร์บอน: ${route.carbonEmission} kgCO2</p>
            <p>ค่าใช้จ่าย: ${route.fare} บาท</p>
        </div>
    `;
}
