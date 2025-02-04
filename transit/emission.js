function calculateEmission(type, shortName, distanceInKm) {
    let emissionFactor;

    if (shortName.includes('REG')) {
        emissionFactor = 0.3;
    } else if (shortName.includes('MINI')) {
        emissionFactor = 0.3;
    } else if (shortName.includes('AC')) {
        emissionFactor = 0.3;
    } else if (shortName.includes('EV')) {
        emissionFactor = 0.15;
    } else if (shortName.includes('NGV')) {
        emissionFactor = 0.1;
    } else if (type === 'SUBWAY'){
        emissionFactor = 0.04;
    }

    const GWP = 1; // Global Warming Potential สำหรับ CO2 = 1 (AR5)
    return emissionFactor * distanceInKm * GWP;
}
