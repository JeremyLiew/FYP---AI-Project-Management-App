<template>
	<v-container>
		<h2>Notifications</h2>

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
		<div v-if="notifications.length === 0" class="text-center">
			<v-alert type="info" outlined>No notifications available.</v-alert>
		</div>

		<!-- Notification Details Dialog -->
		<v-dialog v-model="dialogOpen" max-width="600px">
			<v-card>
				<v-card-title class="text-h6">Notification Details</v-card-title>
				<v-card-text>
					<p>{{ selectedNotification.message }}</p>
					<p><strong>Created At:</strong> {{ formatDate(selectedNotification.created_at) }}</p>
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

export default {
	data() {
		return {
			notifications: [],
			dialogOpen: false,
			selectedNotification: null
		};
	},
	created() {
		this.fetchNotifications();
	},
	methods: {
		fetchNotifications() {
			GeneralClient.fetchNotifications()
				.then(response => {
					this.notifications = response.data;
				})
				.catch(error => {
					console.error("Error fetching notifications", error);
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
		formatDate(dateString) {
			const date = new Date(dateString);
			return date.toLocaleDateString();
		}
	}
}
</script>
