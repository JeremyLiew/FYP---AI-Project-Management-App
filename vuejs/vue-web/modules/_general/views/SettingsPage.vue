<template>
	<v-app>
		<v-container class="my-5">
			<v-card elevation="2" class="pa-8">
				<v-card-title class="text-h5">
					Settings
				</v-card-title>
				<v-divider class="my-3"></v-divider>

				<!-- Theme Toggle Section -->
				<v-row align="center" class="mb-4">
					<v-col cols="auto">
						<v-icon color="primary">mdi-theme-light-dark</v-icon>
					</v-col>
					<v-col>
						<v-row>
							<v-col cols="auto" class="text-body-1">Dark Mode</v-col>
						</v-row>
					</v-col>
					<v-col class="text-end">
						<v-row justify="end">
							<v-switch v-model="isDarkTheme" hide-details></v-switch>
						</v-row>
					</v-col>
				</v-row>

				<v-divider></v-divider>

				<!-- Timezone Selector Section -->
				<v-row align="center" class="mb-4">
					<v-col cols="auto">
						<v-icon color="primary">mdi-clock-outline</v-icon>
					</v-col>
					<v-col>
						<v-row>
							<v-col cols="auto" class="text-body-1">Timezone</v-col>
						</v-row>
					</v-col>
					<v-col class="text-end">
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

				<!-- Placeholder for Future Settings -->
				<v-row align="center" class="mt-4">
					<v-col cols="auto">
						<v-icon color="primary">mdi-cog-outline</v-icon>
					</v-col>
					<v-col>
						<v-row>
							<v-col cols="auto" class="text-body-1">Other Settings</v-col>
							<v-spacer></v-spacer>
							<v-btn icon>
								<v-icon>mdi-chevron-right</v-icon>
							</v-btn>
						</v-row>
					</v-col>
				</v-row>
			</v-card>
		</v-container>
	</v-app>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useTheme } from 'vuetify'
import SettingClient from "../client"

export default {
	setup() {
		const theme = useTheme()
		const isDarkTheme = ref(false)
		const selectedTimezone = ref('Asia/Kuala_Lumpur')
		const timezones = ['UTC', 'Asia/Kuala_Lumpur', 'America/New_York', 'Europe/London', 'Asia/Tokyo']

		// Fetch user settings on mount
		const fetchUserSettings = async () => {
			try {
				const res = await SettingClient.fetchUserSettings()
				const response = res.data
				if (response.timezone) {
					selectedTimezone.value = response.timezone
				}
				if (response.theme) {
					isDarkTheme.value = response.theme === 'dark'
					applyTheme()
				}
			} catch (error) {
				console.error('Error fetching user settings:', error)
			}
		}

		// Apply the theme
		const applyTheme = () => {
			theme.global.name.value = isDarkTheme.value ? 'dark' : 'light'
		}

		// Watch for theme changes and save them
		watch(isDarkTheme, async (newValue) => {
			applyTheme()
			try {
				await SettingClient.updateSetting({ theme: newValue ? 'dark' : 'light' })
			} catch (error) {
				console.error('Error updating theme:', error)
				alert('Failed to update theme. Please try again.')
			}
		})

		// Watch for timezone changes and save them
		watch(selectedTimezone, async (newValue) => {
			try {
				await SettingClient.updateSetting({ timezone: newValue })
			} catch (error) {
				console.error('Error updating timezone:', error)
				alert('Failed to update timezone. Please try again.')
			}
		})

		// Initialize settings on component mount
		onMounted(fetchUserSettings)

		return {
			isDarkTheme,
			selectedTimezone,
			timezones,
		}
	},
}
</script>
