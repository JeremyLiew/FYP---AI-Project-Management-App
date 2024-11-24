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
							<v-switch hide-details @change="toggleTheme()"></v-switch>
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

<script setup>
import { useTheme } from 'vuetify'

const theme = useTheme()

// Function to toggle between light and dark themes
function toggleTheme() {
	theme.global.name.value = theme.global.current.value.dark ? 'light' : 'dark'
}
</script>
