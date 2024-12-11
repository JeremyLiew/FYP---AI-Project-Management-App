<template>
	<div>
	  <h1>Weather Data</h1>
	  <div v-if="weatherData" class="sensor-grid">
		
		<!-- Light Intensity Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + lightIntensityBg + ')' }">
		  <h3>Light Intensity</h3>
		  <p>Status: {{ weatherData.Light?.Status }}</p>
		  <p>Photometer: {{ weatherData.Light?.photometer }}</p>
		</div>
	
		<!-- Sound Intensity Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + soundIntensityBg + ')' }">
		  <h3>Sound Intensity</h3>
		  <p>{{ weatherData.soundIntensity }}</p>
		</div>
	
		<!-- Ambient Temperature Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + ambientTemperatureBg + ')' }">
		  <h3>Ambient Temperature</h3>
		  <p>{{ weatherData.DHT?.temperature }}°C</p>
		</div>
	
		<!-- Soil Moisture Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + soilMoistureBg + ')' }">
		  <h3>Soil Moisture Level</h3>
		  <p>{{ weatherData.DHT?.moisture }}</p>
		</div>
	
		<!-- Touch Detection Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + touchDetectionBg + ')' }">
		  <h3>Touch Detection</h3>
		  <p>{{ weatherData.touchDetection }}</p>
		</div>
	
		<!-- Water Level Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + waterLevelBg + ')' }">
		  <h3>Water Level</h3>
		  <p>{{ weatherData.waterLevel }}</p>
		</div>
	
		<!-- Distance Measurement Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + distanceMeasurementBg + ')' }">
		  <h3>Distance Measurement</h3>
		  <p>{{ weatherData.distanceMeasurement }}</p>
		</div>
	
		<!-- Air Quality Index Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + airQualityBg + ')' }">
		  <h3>Air Quality Index</h3>
		  <p>Quality: {{ weatherData.AirQuality?.Quality }}</p>
		  <p>Type: {{ weatherData.AirQuality?.Type }}</p>
		</div>
	
		<!-- RFID Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + rfidBg + ')' }">
		  <h3>RFID</h3>
		  <p>{{ weatherData.rfid }}</p>
		</div>
	
		<!-- Motion Detection Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + motionDetectionBg + ')' }">
		  <h3>Motion Detection</h3>
		  <p>Status: {{ weatherData.Infrared?.Status }}</p>
		  <p>Movement: {{ weatherData.Infrared?.movement }}</p>
		</div>
	
		<!-- Humidity Card -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + humidityBg + ')' }">
		  <h3>Humidity</h3>
		  <p>{{ weatherData.DHT?.humidity }}%</p>
		</div>

		<div class="sensor-card" :style="{ backgroundImage: 'url(' + TimeBg + ')' }">
		  <h3>Time</h3>
		  <p>{{ weatherData.humidity }}</p>
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
      lightIntensityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExODVpazEzcjk0eHM4NTd3OXU1YXIxNGNzcTBvMjg1MGM0bHMxcmZqMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/49VB0PHxR5Vsc/giphy.gif",
      soundIntensityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNG9iM3FvN25wa3R6MDF2NjJrbTJxb3FnbGxkcjFrNXFvdzlhdWVhbiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/QFuBzoQdijNFztFEp6/giphy.gif",
      ambientTemperatureBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExZW5kdzg4c3Yxb21zemg5eTR5ZmFkdWl0MHVjd2ZlOWp2Y3I1ZmhwZCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/17bvpzBFFQ5Xi/giphy.gif",
      soilMoistureBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExenc1cm1lanFiZG03aWU3bWluNWlzdjgwZnQxcWF1M3JpOG9tY2g1OSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/fYYpNdX624AAU/giphy.gif",
      touchDetectionBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExcDcxdmtiejd6Y2hlcmxyNG1nazdycXY3dTB1bHVxMnFqemZkejNsayZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/9DgxhWOxHDHtF8bvwl/giphy-downsized-large.gif",
      waterLevelBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdXRrd205Yjl2bTR4YXp6aWFvY3lvZXB2cTYyaXhzNTF0NXBieHYwOCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/lNQ2RRsEfJqbjg1i0I/giphy.gif",
      distanceMeasurementBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExbjY3czl1MXg1cW5nc3B4N21nb3E3bmFzNmRwc3R5NmhoaWR0cWdheSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/T75GIRuXazJ4oHmE4Y/giphy.gif",
      airQualityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExcTc4dGc4dDE5NGlrc3l3a3Q3bjNlMWVwYTRlZms5YXBzeDdqOXlmOCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/KpAPQVW9lWnWU/giphy.gif",
      rfidBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdWg3dno0bTlndTVrOXhheDRmNXhidWEwcWtldXI5NzRqejBrOWJsMyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o85xvAaEm8nPHWMco/giphy.gif",
      motionDetectionBg: "https://path-to-your-motion-detection-gif.gif",
      humidityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExY2h1YTl2czkwZWJlbWZuNGc4ZXlydnJ1MWpjbmw1NGh4M3VqamtldiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/RK8K5c6tuPUsjEAL80/giphy.gif",
	  TimeBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExb25mcmdoeDQzYmFkZXE2ejV1a2k1MDJwOXNxc2ZieXVjcnVwdnl1dCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/l0NwSvw3kQWZuUW8E/giphy.gif",
    };
  },
  mounted() {
    this.fetchFirebaseData("Sensor");
    this.listenForRealtimeUpdates("Sensor", (data) => {
      console.log("Realtime Data:", data);
      this.weatherData = data;
    });
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
  