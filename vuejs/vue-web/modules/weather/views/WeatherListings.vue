<template>
	<div>
	  <h1>Weather Data</h1>
	  <div v-if="weatherData" class="sensor-grid">
		
		<!-- Light Intensity Card -->
		<div class="sensor-card" :style="{ backgroundImage: lightIntensityBg }">
			<h3>Light Intensity</h3>
			<p>Status: {{ weatherData.Light?.Status }}</p>
			<p>Photometer: {{ weatherData.Light?.photometer }}</p>
		</div>
	

		<!-- Ambient Temperature Card -->
		<div class="sensor-card" :style="{ backgroundImage: ambientTemperatureBg }">
		<h3>Ambient Temperature</h3>
		<p>{{ weatherData.DHT?.temperature }}°C</p>
		</div>

		<!-- Soil Moisture Card -->
		<div class="sensor-card" :style="{ backgroundImage: soilMoistureBg }">
		<h3>Soil Moisture Level</h3>
		<p>{{ weatherData.DHT?.moisture }}</p>
		</div>

		<!-- Water Level Card -->
		<div class="sensor-card" :style="{ backgroundImage: waterLevelBg }">
		<h3>Water Level</h3>
		<p>Level: {{ weatherData.WaterLevel?.Level }}</p>
		<p>Status: {{ weatherData.WaterLevel?.Status }}</p>
		</div>

		<!-- Distance Measurement Card -->
		<div class="sensor-card" :style="{ backgroundImage: distanceMeasurementBg }">
		<h3>Car Detection</h3>
		<p>Distance: {{ weatherData.Ultrasonic?.Distance }}</p>
		</div>

		<!-- Humidity Card -->
		<div class="sensor-card" :style="{ backgroundImage: humidityBg }">
		<h3>Humidity</h3>
		<p>{{ weatherData.DHT?.humidity }}%</p>
		</div>

		<!-- Touch Detection Card -->
		<div class="sensor-card" :style="{ backgroundImage: touchDetectionBg }">
		<h3>Touch Detection</h3>
		<p>
			{{ weatherData.Touch?.Quantity === 1 ? 'Visitor' : weatherData.Touch?.Quantity === 3 ? 'Delivery' : 'Unknown' }}
		</p>
		</div>

	  </div>
	</div>
</template>
  
  
<script>
import { initializeApp } from "firebase/app";
import { getDatabase, ref, get, onValue } from "firebase/database";

// Firebase configuration
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
  databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL,
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
  appId: import.meta.env.VITE_FIREBASE_APP_ID,
};


export default {
  data() {
    return {
      weatherData: null,
    };
  },
  mounted() {
    this.fetchFirebaseData("Sensor");
    this.listenForRealtimeUpdates("Sensor", (data) => {
      console.log("Realtime Data:", data);
      this.weatherData = data;
    });
  },
  computed: {
	lightIntensityBg() {
		const status = this.weatherData.Light?.Status;
		if (status === 'rain') {
		return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExcXE2Y3UzZWNtenJ3NzM0anNhOGh5bjRpNWo3Z2hheDdmcnJxNTVqdyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/9UhZtQ3hl2TEuCGeC1/giphy.gif)'; 
		}
		return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExODVpazEzcjk0eHM4NTd3OXU1YXIxNGNzcTBvMjg1MGM0bHMxcmZqMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/49VB0PHxR5Vsc/giphy.gif)'; // Replace with the original image path
	},
	soundIntensityBg() {
    const noiseLevel = this.weatherData.Sound?.Noise;
    return noiseLevel === 'high'
      ? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNG9iM3FvN25wa3R6MDF2NjJrbTJxb3FnbGxkcjFrNXFvdzlhdWVhbiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/QFuBzoQdijNFztFEp6/giphy.gif)'
      : 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExbmJvcW52dXN1czh0ZTVobmNsdHZnc3FkZmU3MWJnMXQwd2Qyb3VhbCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/A7XLVY8QoE3n8KzpjP/giphy.gif)';
	},
	ambientTemperatureBg() {
		const temperature = this.weatherData.DHT?.temperature;
		if(temperature >= 40){
			return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExZW5kdzg4c3Yxb21zemg5eTR5ZmFkdWl0MHVjd2ZlOWp2Y3I1ZmhwZCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/17bvpzBFFQ5Xi/giphy.gif)';
		}else if(temperature >= 25 && temperature < 40){
			return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExcHpmOWN3MzQ3YTIxcjZiNDNqeXRyMnRyanlvZ3p5aG83bm4xNzJuOSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/UVBBpNIOsbfuu3Uufx/giphy.gif)';
		}else if(temperature < 25) {
			return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExbzRreXEzdXptczRpNDZpdnE0djZteGhpMXZtMnB4NnRxZnJkbzFjdCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/s4Bi420mMDRBK/giphy.gif)';
		}
	},
	soilMoistureBg() {
		const moisture = this.weatherData.DHT?.moisture;
		return moisture < 20
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdjMwbTZyZHY2cG4xd25xOTM1Y3d0a25nMjFneXk2aTEyOHNmdnl3YyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/azdENk47bR7iGPTtXf/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExenc1cm1lanFiZG03aWU3bWluNWlzdjgwZnQxcWF1M3JpOG9tY2g1OSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/fYYpNdX624AAU/giphy.gif)';
	},
	waterLevelBg() {
		const waterStatus = this.weatherData.WaterLevel?.Status;
		return waterStatus === 'danger'
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdXRrd205Yjl2bTR4YXp6aWFvY3lvZXB2cTYyaXhzNTF0NXBieHYwOCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/lNQ2RRsEfJqbjg1i0I/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExa2xmdjVnbmphM3J5dGxlZXVtYTR1M3d0azBjYm5reWltMHA2YTc1ayZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/h7uFKaf3xMFAsCJ6Ib/giphy.gif)';
	},
	distanceMeasurementBg() {
		const distance = this.weatherData.Ultrasonic?.Distance;
		return distance < 30
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExbjY3czl1MXg1cW5nc3B4N21nb3E3bmFzNmRwc3R5NmhoaWR0cWdheSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/T75GIRuXazJ4oHmE4Y/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNmxpY2sxYzgyaTE5d2Z6N2tvdTFxbzR6OW80cHB5NXcxejduZjB0biZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/ulxHhvKW9X6459dtOn/giphy.gif)';
	},
	rfidBg() {
		const member = this.weatherData.RFID?.Member;
		return member === 'boss'
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdWg3dno0bTlndTVrOXhheDRmNXhidWEwcWtldXI5NzRqejBrOWJsMyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o85xvAaEm8nPHWMco/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExZmU5d242YzM2aW00ZGlpZnE2azhia24xemtjaGVhZzMyOG5lcWFrMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o7TKFTNdu9nHbBUju/giphy.gif)';
	},
	motionDetectionBg() {
		const motionStatus = this.weatherData.Infrared?.Status;
		return motionStatus === 'detected'
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExZTh2Y2Z6cW5neHJidTA2cmEwcGYzemtqZjRoeWk5YXUxdG1wd2lmciZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/l0EwYRXhyq1rpn4Gc/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNHFsdDFpczBiZ3d4YjZpeTZmczhjMm54YThibnlqejFranRja2hwaiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/11ISwbgCxEzMyY/giphy.gif)';
	},
	humidityBg() {
		const humidity = this.weatherData.DHT?.humidity;
		return humidity > 50
		? 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExY2h1YTl2czkwZWJlbWZuNGc4ZXlydnJ1MWpjbmw1NGh4M3VqamtldiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/RK8K5c6tuPUsjEAL80/giphy.gif)'
		: 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNWU0dmNrY3Mwb2R5ZHZibm9qd3ZvN3R5Z3liejN1Zmg5bDhoY20wOCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/U3O6bnlgwdK5nj5Lrf/giphy.gif)';
	},
	touchDetectionBg() {
		const quantity = this.weatherData.Touch?.Quantity;
		if (quantity === 1) {
		return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdnY3aHh6bG8yd2o4dzdyeGI2NWVoczdoOXd0Yms2aXNudHJ4MHZ1eCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/DUmO07yBNdRCcLv0xM/giphy.gif)'
		} else if (quantity === 3) {
		return 'url(https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExcDcxdmtiejd6Y2hlcmxyNG1nazdycXY3dTB1bHVxMnFqemZkejNsayZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/9DgxhWOxHDHtF8bvwl/giphy-downsized-large.gif)';
		} 
  	},
  },
  methods: {
    // Fetch data from Firebase (one-time fetch)
    async fetchFirebaseData(path) {
      const firebaseApp = initializeApp(firebaseConfig);
      const database = getDatabase(firebaseApp);
      const dbRef = ref(database, path);
      try {
        const snapshot = await get(dbRef);
        if (snapshot.exists()) {
          this.weatherData = snapshot.val();
        } else {
          console.log("No data available at", path);
          this.weatherData = null;
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    },
    // Real-time listener for Firebase data
    listenForRealtimeUpdates(path, callback) {
      const firebaseApp = initializeApp(firebaseConfig);
      const database = getDatabase(firebaseApp);
      const dbRef = ref(database, path);
      onValue(dbRef, (snapshot) => {
        const data = snapshot.val();
        console.log("Realtime data received:", data);
        callback(data);
      });
    },
  },
};
</script>

  
  <style scoped>
  .sensor-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 16px;
  }
  
  .sensor-card {
	background-color: rgba(0, 0, 0, 0.5); /* Optional overlay to enhance text visibility */
	color: white;
	padding: 20px;
	text-align: center;
	border-radius: 10px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
	background-size: cover;
	background-position: center;
	height: 250px; /* Adjust based on content */
  }
  
  .sensor-card h3 {
	font-size: 18px;
	margin-bottom: 10px;
  }
  
  .sensor-card p {
	font-size: 24px;
  }
  </style>
  