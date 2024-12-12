<template>
	<v-app>
		<v-container class="my-5">
			<template v-if="modelLoading">
				<v-skeleton-loader type="article"></v-skeleton-loader>
			</template>
			<template v-else>
				<v-card elevation="2" class="pa-8">
					<v-card-title class="text-h5">Settings</v-card-title>
					<v-divider class="my-3"></v-divider>

					<!-- Theme Toggle Section -->
					<v-row align="center" class="mb-4">
						<v-col cols="6" sm="auto">
							<v-icon color="primary">mdi-theme-light-dark</v-icon>
						</v-col>
						<v-col cols="6" sm="4" class="text-body-1">Dark Mode</v-col>
						<v-col cols="12" sm="4" class="text-end">
							<v-row class="justify-content-center justify-sm-start">
								<v-switch v-model="isDarkTheme" hide-details></v-switch>
							</v-row>
						</v-col>
					</v-row>
					<v-divider></v-divider>

					<!-- Timezone Selector Section -->
					<v-row align="center" class="mb-4">
						<v-col cols="6" sm="auto">
							<v-icon color="primary">mdi-clock-outline</v-icon>
						</v-col>
						<v-col cols="6" sm="4" class="text-body-1">
							Timezone
						</v-col>
						<v-col cols="12" sm="4" class="text-end">
							<v-row justify="end">
								<v-select
									v-model="selectedTimezone"
									:items="timezones"
									label="Select Timezone"
									dense
									hide-details
								></v-select>
							</v-row>
						</v-col>
					</v-row>
					<v-divider></v-divider>

					<!-- Time Format Section -->
					<v-row align="center" class="mb-4">
						<v-col cols="6" sm="auto">
							<v-icon color="primary">mdi-clock-time-four</v-icon>
						</v-col>
						<v-col cols="6" sm="4" class="text-body-1">
							Time Format
						</v-col>
						<v-col cols="12" sm="4" class="text-end">
							<v-row justify="end">
								<v-select
									v-model="selectedTimeFormat"
									:items="timeFormats"
									label="Select Time Format"
									dense
									hide-details
								></v-select>
							</v-row>
						</v-col>
					</v-row>
					<v-divider></v-divider>

					<!-- Date Format Section -->
					<v-row align="center" class="mb-4">
						<v-col cols="6" sm="auto">
							<v-icon color="primary">mdi-calendar</v-icon>
						</v-col>
						<v-col cols="6" sm="4" class="text-body-1">
							Date Format
						</v-col>
						<v-col cols="12" sm="4" class="text-end">
							<v-row justify="end">
								<v-select
									v-model="selectedDateFormat"
									:items="dateFormats"
									label="Select Date Format"
									dense
									hide-details
								></v-select>
							</v-row>
						</v-col>
					</v-row>
					<v-divider></v-divider>

					<!-- Time Picker Section for Report Scheduling -->
					<v-row align="center" class="mb-4">
						<v-col cols="6" sm="auto">
							<v-icon color="primary">mdi-calendar-clock</v-icon>
						</v-col>
						<v-col cols="6" sm="4" class="text-body-1">
							Select Report Time
						</v-col>
						<v-col cols="12" sm="4" class="text-end">
							<v-row justify="end">
								<v-select
									v-model="selectedEmailTime"
									:items="EmailTimeOptions"
									label="Pick Time Line"
									hide-details
									dense
								></v-select>
							</v-row>
						</v-col>
					</v-row>
					<v-divider></v-divider>

					<div class="text-center">
						<small style="color: #666; font-style: italic;">
							<strong>Important:</strong> After changing your timezone, time format and date format, please <strong>refresh the page</strong> for the changes to take effect across the application.
						</small>
					</div>
				</v-card>
			</template>
		</v-container>
	</v-app>
</template>

<script>
import SettingClient from "../client"

export default {
	data() {
		return {
			isDarkTheme: false,
			selectedTimezone: 'UTC',
			selectedTimeFormat: '24h',
			selectedDateFormat: 'MM/DD/YYYY',
			selectedEmailTime: 'Week/Month',
			timezones: ['UTC', 'Asia/Kuala_Lumpur', 'America/New_York', 'Europe/London', 'Asia/Tokyo'],
			timeFormats: ['24h', '12h'],
			dateFormats: ['MM/DD/YYYY', 'DD/MM/YYYY', 'YYYY/MM/DD'],
			EmailTimeOptions: ['Weekly','Monthly'], 
			modelLoading: true,
		}
	},
	watch: {
		isDarkTheme(newValue) {
			this.applyTheme()
			this.saveSettings()
		},
		selectedTimezone(newValue) {
			this.saveSettings()
		},
		selectedTimeFormat(newValue) {
			this.saveSettings()
		},
		selectedDateFormat(newValue) {
			this.saveSettings()
		},
		selectedEmailTime(newValue) {
			this.saveSettings()
		}
	},
	mounted() {
		this.fetchUserSettings()
	},
	methods: {
		// Fetch user settings on mount
		fetchUserSettings() {
			this.modelLoading = true
			SettingClient.fetchUserSettings().then((res) => {
				const response = res.data
				if (response.timezone) {
					this.selectedTimezone = response.timezone
				}
				if (response.theme) {
					this.isDarkTheme = response.theme === 'dark'
					this.applyTheme()
				}
				if (response.time_format) {
					this.selectedTimeFormat = response.time_format
				}
				if (response.date_format) {
					this.selectedDateFormat = response.date_format
				}
				if (response.email_time) {
					this.selectedEmailTime = response.email_time  
				}
			}).catch((error) => {
				console.error('Error fetching user settings:', error)
			}).finally(()=> {
				this.modelLoading = false
			})
		},

		// Apply the theme
		applyTheme() {
			console.log(this.isDarkTheme)
			this.$vuetify.theme.global.name = this.isDarkTheme ? 'dark' : 'light'
		},

		// Save settings to backend
		async saveSettings() {
			SettingClient.updateSetting({
				theme: this.isDarkTheme ? 'dark' : 'light',
				timezone: this.selectedTimezone,
				time_format: this.selectedTimeFormat,
				date_format: this.selectedDateFormat,
				email_time: this.selectedEmailTime,  
			}).then(() => {

			}).catch((error) => {
				console.error('Error saving settings:', error)
			})
		}
	}
}
</script>

