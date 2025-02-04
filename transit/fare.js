function calculateFare(shortName, line, distanceInKm, numStop, departure, arrival, type) {
    let maxfare = 0;
    let minfare = 0;

    //bus
    if(type === 'BUS'){
        if (shortName.includes('REG')) {
            minfare = 8; // ค่าโดยสารสำหรับ REG
        } else if (shortName.includes('MINI')) {
            minfare = 10; // ค่าโดยสารสำหรับ MINI
        } else if (shortName.includes('AC')) {
            if (distanceInKm <= 4) {
                minfare = 12;
            } else if (distanceInKm <= 8) {
                minfare = 14;
            } else if (distanceInKm <= 12) {
                minfare = 16;
            } else if (distanceInKm <= 16) {
                minfare = 18;
            } else {
                minfare = 20;
            }
        } else if (shortName.includes('EV')) {
            if (distanceInKm <= 4) {
                minfare = 15;
            } else if (distanceInKm <= 16) {
                minfare = 20;
            } else {
                minfare = 25;
            }
        } else if (shortName.includes('NGV')) {
            if (distanceInKm <= 4) {
                minfare = 15;
            } else if (distanceInKm <= 16) {
                minfare = 20;
            } else {
                minfare = 25;
            }
        }

        }else if(type === 'SUBWAY'){
            if  (line === 'Sukhumvit Line' || line ==='Silom Line'){
                //ส่วนต่อขยาย Sukhumvit Line
                if(departure.includes('Khu Khot' ||'Mo Chit' || 'Yaek KorPor Aor' || 'Royal Thai Air Force Museum'
                || 'Bhumibol Adulyadej Hospital' || 'Saphan Mai' || 'Sai Yud' || 'Phahon Yothin 59'
                || 'Wat Phra Sri Mahathat' || '11th Infantry Regiment' || 'Bang Bua' || 'Royal Forest Department'
                || 'Kasetsart University' || 'Sena Nikhom' || 'Ratchayothin' || 'Phahon Yothin 24' || 'Ha Yaek Lat Phrao') && 
                arrival.includes('Mo Chit' || 'Khu Khot' || 'Yaek KorPor Aor' || 'Royal Thai Air Force Museum'
                || 'Bhumibol Adulyadej Hospital' || 'Saphan Mai' || 'Sai Yud' || 'Phahon Yothin 59'
                || 'Wat Phra Sri Mahathat' || '11th Infantry Regiment' || 'Bang Bua' || 'Royal Forest Department'
                || 'Kasetsart University' || 'Sena Nikhom' || 'Ratchayothin' || 'Phahon Yothin 24' || 'Ha Yaek Lat Phrao')){
                    minfare = 15;
                }else if(departure.includes('Khu Khot' ||'Mo Chit' || 'Yaek KorPor Aor' || 'Royal Thai Air Force Museum'
                || 'Bhumibol Adulyadej Hospital' || 'Saphan Mai' || 'Sai Yud' || 'Phahon Yothin 59'
                || 'Wat Phra Sri Mahathat' || '11th Infantry Regiment' || 'Bang Bua' || 'Royal Forest Department'
                || 'Kasetsart University' || 'Sena Nikhom' || 'Ratchayothin' || 'Phahon Yothin 24' || 'Ha Yaek Lat Phrao')){   
                    if(arrival.includes('Saphan Khwai')){
                        minfare = 32;
                    }else if(arrival.includes('Ari')){
                        minfare = 43;
                    }else if(arrival.includes('Sanam Pao')){
                        minfare = 47;
                    }else if(arrival.includes('Victory Monument')){
                        minfare = 50;
                    }else if(arrival.includes('Phaya Thai')){
                        minfare = 55;
                    }else if(arrival.includes('Ratchadamri')){
                        minfare = 58;
                    }else{
                        minfare = 62;
                    }
                }else if(departure.includes('Bang Wa' || 'Wutthakat' || 'Talat Phlu' || 'Pho Nimit' || 'Wongwian Yai' &&
                arrival === 'Bang Wa' || 'Wutthakat' || 'Talat Phlu' || 'Pho Nimit' || 'Wongwian Yai')){
                    minfare = 15;
                }else if(departure.includes('Bang Wa' || 'Wutthakat' || 'Talat Phlu' || 'Pho Nimit' || 'Wongwian Yai')){
                    if(arrival.includes('Krung Thon Buri' || 'Saphan Taksin')){
                        minfare = 32;
                    }else if(arrival.includes('Surasak')){
                        minfare = 43;
                    }else if(arrival.includes('Saint Louis')){
                        minfare = 47;
                    }else if(arrival.includes('Chong Nonsi')){
                        minfare = 50;
                    }else if(arrival.includes('Sala Daeng')){
                        minfare = 55;
                    }else if(arrival.includes('Ratchadamri')){
                        minfare = 58;
                    }else{
                        minfare = 62;
                    }
                }else if(arrival.includes('Bang Wa' || 'Wutthakat' || 'Talat Phlu' || 'Pho Nimit' || 'Wongwian Yai')){
                    if(departure.includes('Krung Thon Buri' || 'Saphan Taksin')){
                        minfare = 32;
                    }else if(departure.includes('Surasak')){
                        minfare = 43;
                    }else if(departure.includes('Saint Louis')){
                        minfare = 47;
                    }else if(departure.includes('Chong Nonsi')){
                        minfare = 50;
                    }else if(departure.includes('Sala Daeng')){
                        minfare = 55;
                    }else if(departure.includes('Ratchadamri')){
                        minfare = 58;
                    }else{
                        minfare = 62;
                    }
                }else{
                    if(numStop == 1){
                        minfare = 17;
                    }else if(numStop == 2){
                        minfare = 25;
                    }else if(numStop == 3){
                        minfare = 28;
                    }else if(numStop == 4){
                        minfare = 32;
                    }else if(numStop == 5){
                        minfare = 35;
                    }else if(numStop == 6){
                        minfare = 40;
                    }else if(numStop == 7){
                        minfare = 43;
                    }else if(numStop == 8){
                        minfare = 47;
                    }    
                }

        }else if(line === 'Yellow Line'){       
            if(numStop == 1){
                minfare = 18;
                maxfare = 21;
            }else if(numStop === 2){
                minfare = 22;
                maxfare = 25;
            }else if(numStop === 3){
                minfare = 24;
                maxfare = 28;
            }else if(numStop === 4){
                minfare = 27;
                maxfare = 32;
            }else if(numStop === 5){
                minfare = 30;
                maxfare = 36;
            }else if(numStop === 6){
                minfare = 33;
                maxfare = 40;
            }else if(numStop === 7){
                minfare = 38;
                maxfare = 43;
            }else if(numStop === 8){
                minfare = 42;
                maxfare = 45;
            }else {
                minfare = 45;
            }
        }else if(line === 'Pink Line'){       
            if(numStop == 1){
                minfare = 15;
            }else if(numStop == 2){
                minfare = 17;
                maxfare = 21;
            }else if(numStop == 3){
                minfare = 21;
                maxfare = 25;
            }else if(numStop == 4){
                minfare = 23;
                maxfare = 29;
            }else if(numStop == 5){
                minfare = 26;
                maxfare = 32;
            }else if(numStop == 6){
                minfare = 28;
                maxfare = 36;
            }else if(numStop == 7){
                minfare = 30;
                maxfare = 39;
            }else if(numStop == 8){
                minfare = 36;
                maxfare = 42;
            }else if(numStop == 9){
                minfare = 39;
                maxfare = 45;
            }else if(numStop == 10){
                minfare = 41;
                maxfare = 45;
            }else if(numStop == 11){
                minfare = 44;
                maxfare = 45;
            }else{
                minfare = 45;
            } 
        }else if(line === 'Red Line' || 'Light Red Line'){
            if(numStop == 1){
                minfare = 14;
                maxfare = 23;
            }else if(numStop == 2){
                minfare = 16;
                maxfare = 29;
            }else if(numStop == 3){
                minfare = 19;
                maxfare = 35;
            }else if(numStop == 4){
                minfare = 23;
                maxfare = 38;
            }else if(numStop == 5){
                minfare = 26;
                maxfare = 41;
            }else if(numStop == 6){
                minfare = 29;
                maxfare = 42;
            }else if(numStop == 7){
                minfare = 33;
                maxfare = 42;
            }else if(numStop == 8){
                minfare = 39;
                maxfare = 42;
            }else {
                minfare = 42;
            }       
        }else if(line === 'Purple Line'){       
            if(numStop == 1){
                minfare = 16;
                maxfare = 17;
            }else if(numStop == 2){
                minfare = 18;
                maxfare = 20;
            }else if(numStop == 3){
                minfare = 21;
                maxfare = 23;
            }else if(numStop == 4){
                minfare = 23;
                maxfare = 25;
            }else if(numStop == 5){
                minfare = 26;
                maxfare = 28;
            }else if(numStop == 6){
                minfare = 29;
                maxfare = 30;
            }else if(numStop == 7){
                minfare = 31;
                maxfare = 33;
            }else if(numStop == 8){
                minfare = 34;
                maxfare = 36;
            }else if(numStop == 9){
                minfare = 36;
                maxfare = 38;
            }else if(numStop == 10){
                minfare = 39;
                maxfare = 41;
            }else{
                minfare = 42;
            }  
            
        }else if(line === 'Blue Line'){
            if(numStop == 1){
                minfare = 17;
            }else if(numStop == 2){
                minfare = 19;
            }else if(numStop == 3){
                minfare = 21;
            }else if(numStop == 4){
                minfare = 24;
            }else if(numStop == 5){
                minfare = 26;
            }else if(numStop == 6){
                minfare = 29;
            }else if(numStop == 7){
                minfare = 31;
            }else if(numStop == 8){
                minfare = 33;
            }else if(numStop == 9){
                minfare = 36;
            }else if(numStop == 10){
                minfare = 38;
            }else if(numStop == 11){
                minfare = 41;
            }else{
                minfare = 43;
            }

        }else if(line === 'Orange Line'){
            minfare = 20;
        }else if(line === 'Airport rail link'){
            if(numStop == 1){
                minfare = 15;
            }else if(numStop == 2){
                minfare = 20;
            }else if(numStop == 3){
                minfare = 25;
            }else if(numStop == 4){
                minfare = 30;
            }else if(numStop == 5){
                minfare = 35;
            }else if(numStop == 6){
                minfare = 40;
            }else{
                minfare = 45;
            }
            maxfare = minfare;
        }else if(line === 'Gold Line'){
            minfare = 16;
        }

    }
    // Return all fares
    return { minfare, maxfare};
}
