<template>
	<div>
	  <h1>Weather Data</h1>
	  <div v-if="weatherData" class="sensor-grid">
		
		<!-- Light Intensity Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + lightIntensityBg + ')' }">
		  <h3>Light Intensity</h3>
		  <p>{{ weatherData.lightIntensity }}</p>
		</div>
  
		<!-- Sound Intensity Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + soundIntensityBg + ')' }">
		  <h3>Sound Intensity</h3>
		  <p>{{ weatherData.soundIntensity }}</p>
		</div>
  
		<!-- Ambient Temperature Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + ambientTemperatureBg + ')' }">
		  <h3>Ambient Temperature</h3>
		  <p>{{ weatherData.ambientTemperature }}</p>
		</div>
  
		<!-- Soil Moisture Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + soilMoistureBg + ')' }">
		  <h3>Soil Moisture Level</h3>
		  <p>{{ weatherData.soilMoisture }}</p>
		</div>
  
		<!-- Touch Detection Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + touchDetectionBg + ')' }">
		  <h3>Touch Detection</h3>
		  <p>{{ weatherData.touchDetection }}</p>
		</div>
  
		<!-- Water Level Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + waterLevelBg + ')' }">
		  <h3>Water Level</h3>
		  <p>{{ weatherData.waterLevel }}</p>
		</div>
  
		<!-- Distance Measurement Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + distanceMeasurementBg + ')' }">
		  <h3>Distance Measurement</h3>
		  <p>{{ weatherData.distanceMeasurement }}</p>
		</div>
  
		<!-- Air Quality Index Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + airQualityBg + ')' }">
		  <h3>Air Quality Index</h3>
		  <p>{{ weatherData.airQuality }}</p>
		</div>
  
		<!-- RFID Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + rfidBg + ')' }">
		  <h3>RFID</h3>
		  <p>{{ weatherData.rfid }}</p>
		</div>
  
		<!-- Motion Detection Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + motionDetectionBg + ')' }">
		  <h3>Motion Detection</h3>
		  <p>{{ weatherData.motionDetection }}</p>
		</div>
  
		<!-- Humidity Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + humidityBg + ')' }">
		  <h3>Humidity</h3>
		  <p>{{ weatherData.humidity }}</p>
		</div>
  
		<!-- Temperature Card with dynamic background -->
		<div class="sensor-card" :style="{ backgroundImage: 'url(' + temperatureBg + ')' }">
		  <h3>Temperature</h3>
		  <p>{{ weatherData.temperature }}</p>
		</div>
  
	  </div>
	</div>
</template>
  
  
<script>
import { initializeApp } from "firebase/app";
import { getDatabase, ref, get, onValue } from "firebase/database";

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyA80lBrwR2LXTho6svB85JGoQYXLCCv0Ec",
  authDomain: "ballon-iot2024v2.firebaseapp.com",
  databaseURL: "https://ballon-iot2024v2-default-rtdb.firebaseio.com",
  projectId: "ballon-iot2024v2",
  storageBucket: "ballon-iot2024v2.appspot.com",
  messagingSenderId: "8227081173",
  appId: "1:8227081173:web:26f33f0f5c57a7a9b44b5c",
};

export default {
  data() {
    return {
      weatherData: null,
      // Background images for each column
      lightIntensityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzFrMmk2NzFiZ2l0Z3RkM3g4ajB6MDVhdXdyb21nNjJ5dnltOXFhcSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/xUOwGpaKq5xjHNz8Bi/giphy.gif",
      soundIntensityBg: "https://path-to-your-sound-intensity-gif.gif",
      ambientTemperatureBg: "https://path-to-your-ambient-temperature-gif.gif",
      soilMoistureBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzFrMmk2NzFiZ2l0Z3RkM3g4ajB6MDVhdXdyb21nNjJ5dnltOXFhcSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/xUOwGpaKq5xjHNz8Bi/giphy.gif",
      touchDetectionBg: "https://path-to-your-touch-detection-gif.gif",
      waterLevelBg: "https://path-to-your-water-level-gif.gif",
      distanceMeasurementBg: "https://path-to-your-distance-measurement-gif.gif",
      airQualityBg: "https://path-to-your-air-quality-gif.gif",
      rfidBg: "https://path-to-your-rfid-gif.gif",
      motionDetectionBg: "https://path-to-your-motion-detection-gif.gif",
      humidityBg: "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzFrMmk2NzFiZ2l0Z3RkM3g4ajB6MDVhdXdyb21nNjJ5dnltOXFhcSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/xUOwGpaKq5xjHNz8Bi/giphy.gif", // New Background for Humidity
      temperatureBg: "https://path-to-your-temperature-gif.gif", // New Background for Temperature
    };
  },
  mounted() {
    this.fetchFirebaseData("Sensor/DHT");
    this.listenForRealtimeUpdates("Sensor/DHT", (data) => {
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
  