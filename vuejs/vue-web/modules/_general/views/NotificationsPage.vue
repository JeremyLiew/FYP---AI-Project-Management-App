<template>
	<v-container>
		<h2>Notifications</h2>

		<template v-if="modelLoading">
			<v-skeleton-loader type="article"></v-skeleton-loader>
		</template>

		<template v-else>
			<!-- Notification List -->
			<v-list two-line class="px-6 transparent-list">
				<v-divider></v-divider>
				<v-list-item
					v-for="notification in notifications"
					:key="notification.id"
					class="px-0 hover-elevate"
					@click="openNotificationDialog(notification)"
				>
					<v-row class="pa-2 align-center">
						<!-- Notification Message -->
						<v-col cols="12" sm="8">
							<v-list-item-title class="text-wrap">{{ notification.message }}</v-list-item-title>
							<v-list-item-subtitle>
								<span>{{ formatDate(notification.created_at) }}</span>
							</v-list-item-subtitle>
						</v-col>
						<!-- Status Indicator (Unread/Read) -->
						<v-col cols="12" sm="4" class="d-flex flex-column align-center">
							<v-chip
								:color="notification.is_read ? 'green' : 'gray'"
								dark
								class="mb-1"
								outlined
								small
							>
								{{ notification.is_read ? 'Read' : 'Unread' }}
							</v-chip>
						</v-col>
					</v-row>
				</v-list-item>
			</v-list>
			<!-- No Notifications Available -->
			<div v-if="!hasData" class="text-center"><p>No notifications available.</p></div>
		</template>

		<!-- Notification Details Dialog -->
		<v-dialog v-model="dialogOpen" max-width="600px">
			<v-card>
				<v-card-title class="text-h6">Notification Details</v-card-title>
				<v-card-text>
					<p>{{ selectedNotification.message }}</p>
					<p><strong>Created At:</strong> {{ formatDate(selectedNotification.created_at, dateFormat) }}</p>
					<p><strong>Status:</strong> {{ selectedNotification.is_read ? 'Read' : 'Unread' }}</p>
				</v-card-text>
				<v-card-actions>
					<v-btn text @click="closeDialog">Close</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-container>
</template>

<script>
import GeneralClient from '../client';
import { formatDate } from '@utils/dateUtils';

export default {
	data() {
		return {
			notifications: [],
			dialogOpen: false,
			selectedNotification: null,
			hasData: true,
			modelLoading: true,
			dateFormat: 'DD/MM/YYYY',
		};
	},
	created() {
		this.fetchAndApplyUserSettings();
		this.fetchNotifications();
	},
	methods: {
		formatDate,
		fetchAndApplyUserSettings() {
			GeneralClient.fetchUserSettings().then((res) => {
				const settings = res.data;
				if (settings.date_format) {
					this.dateFormat = settings.date_format;
				}
			}).catch((error) => {
				console.error("Error fetching user settings:", error);
			});
		},
		fetchNotifications() {
			this.modelLoading = true;
			this.hasData = true;
			GeneralClient.fetchNotifications()
				.then(response => {
					this.notifications = response.data;
					this.hasData = this.notifications.length > 0;
				})
				.catch(error => {
					console.error("Error fetching notifications", error);
					this.hasData = false;
				}).finally(()=>{
					this.modelLoading = false
				});
		},
		openNotificationDialog(notification) {
			this.selectedNotification = notification;
			this.dialogOpen = true;

			// Mark the notification as read when it's clicked
			this.markAsRead(notification.id);
		},
		closeDialog() {
			this.dialogOpen = false;
			this.fetchNotifications();
		},
		markAsRead(notificationId) {
			GeneralClient.markAsRead(notificationId)
				.then(() => {
					// Update the notification status locally
					const notification = this.notifications.find(n => n.id === notificationId);
					if (notification) {
						notification.is_read = true;
					}
				})
				.catch(error => {
					console.error("Error marking notification as read", error);
				});
		},
	}
}
</script>
