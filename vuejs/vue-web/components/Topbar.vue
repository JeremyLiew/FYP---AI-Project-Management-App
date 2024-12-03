<template>
	<v-navigation-drawer
		app
		rail
		expand-on-hover
		class="elevation-1"
		permanent
		@mouseenter="onHover(true)"
		@mouseleave="onHover(false)"
	>
		<!-- Sidebar Content -->
		<v-list dense style="display: flex; flex-direction: column; height: 100%;">
			<!-- User Profile Section in Sidebar -->
			<v-list-item class="pa-0" @click="goToProfile">
				<v-row v-if="drawerHovered" no-gutters class="d-flex flex-column align-center">
					<v-col class="text-center">
						<img
							:src="user.profilePicture ? `/storage/${user.profilePicture}` : defaultAvatar"
							alt="Profile Picture"
							class="profile-picture"
						/>
					</v-col>
					<v-col class="d-flex justify-center">
						<v-list-item-title>{{ user.name || 'User Name' }}</v-list-item-title>
					</v-col>
				</v-row>
				<v-row v-else no-gutters class="d-flex justify-center">
					<v-col class="text-center">
						<img
							:src="user.profilePicture ? `/storage/${user.profilePicture}` : defaultAvatar"
							alt="Profile Picture"
							class="profile-picture"
						/>
					</v-col>
				</v-row>
			</v-list-item>

			<v-divider></v-divider>

			<!-- Navigation Links -->
			<template v-for="(nav_link, i) in nav_links" :key="i">
				<v-list-item
					:to="nav_link.to" exact class="font-primary d-flex"
					:ripple="false"
				>
					<v-list-item-media>
						<!-- Use v-icon inside v-list-item-media for the icon -->
						<v-icon>{{ nav_link.icon }}</v-icon>
					</v-list-item-media>
					<!-- Title only shown when drawer is hovered -->
					<v-list-item-title v-if="drawerHovered" class="pl-3">
						{{ nav_link.title }}
					</v-list-item-title>
				</v-list-item>
			</template>

			<v-divider></v-divider>

			<div class="d-flex flex-column justify-content-end h-100">
				<v-list-item v-if="isAuthorized()" @click="goToActivityLog">
					<v-list-item-media>
						<v-icon>mdi-math-log</v-icon>
					</v-list-item-media>
					<v-list-item-title v-if="drawerHovered" class="pl-3">Activity Log</v-list-item-title>
				</v-list-item>
				<v-list-item @click="goToNotifications">
					<v-list-item-media>
						<v-icon>mdi-bell</v-icon>
						<!-- Notification Counter -->
						<span v-if="notificationCount > 0" class="notification-counter">{{ notificationCount }}</span>
					</v-list-item-media>
					<v-list-item-title v-if="drawerHovered" class="pl-3">Notifications</v-list-item-title>
				</v-list-item>
				<!-- Settings Link (at the bottom) -->
				<v-list-item @click="goToSettings">
					<v-list-item-media>
						<!-- Use v-icon inside v-list-item-media for the icon -->
						<v-icon>mdi-cog</v-icon>
					</v-list-item-media>
					<v-list-item-title v-if="drawerHovered" class="pl-3">Settings</v-list-item-title>
				</v-list-item>
			</div>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
import GeneralClient from '../modules/_general/client';

export default {
	data() {
		return {
			nav_links: [],
			auth: false,
			drawerHovered: false,
			notificationCount: 0,

			user: {},
			projects: [],
			tasks: [],
			profile_picture: null,
			defaultAvatar: '/images/avatar.jpg',
		};
	},
	computed: {
		authPage() {
			return this.auth ? { name: 'profile-page' } : { name: 'login-page' };
		},
	},
	created() {
		this.nav_links = [
			{ title: 'Home', icon: 'mdi-home', to: { name: 'home-page' } },
			{ title: 'Projects', icon: 'mdi-briefcase', to: { name: 'project-listings-page' } },
			{ title: "Contact Us", icon: 'mdi-card-account-mail-outline', to: { name: "contact-us-page" } },
		];
		this.auth = false;
		if (this.$auth.check()) {
			this.auth = true;
			this.fetchNotificationCount();
			this.fetchProfile();
		}
	},
	methods: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		},
		fetchNotificationCount() {
			GeneralClient.fetchNotificationCount()
				.then((response) => {
					this.notificationCount = response.data.unreadCount;
				})
				.catch((error) => {
					console.error('Error fetching notifications:', error);
				});
		},
		fetchProfile() {
			GeneralClient.fetchProfile()
				.then((response) => {
					this.user = response.data.user;
					this.projects = response.data.projects;
					this.tasks = response.data.tasks;
				})
				.catch((error) => {
					console.error("Error fetching profile data:", error);
				});
		},
		onHover(status) {
			this.drawerHovered = status;
		},
		goToProfile() {
			this.$router.push({ name: 'profile-page' });
		},
		goToSettings() {
			this.$router.push({ name: 'settings-page' });
		},
		goToNotifications() {
			this.$router.push({ name: 'notifications-page' });
		},
		goToActivityLog() {
			this.$router.push({ name: 'activity-log-page' });
		},
	},
};
</script>

  <style scoped>
  .mt-auto {
	margin-top: auto;
  }

  .v-list-item >>> .v-list-item__content {
	display: flex;
  }

  .notification-counter {
	position: absolute;
	top: -5px;
	right: 5px;
	background-color: red;
	color: white;
	font-size: 8px;
	font-weight: bold;
	border-radius: 50%;
	padding: 2px 6px;
  }

  .profile-picture {
	width: 40px;
	height: 40px;
	object-fit: cover;
	border-radius: 50%;
  }

  .d-flex {
	display: flex;
  }

  .flex-column {
	flex-direction: column;
  }
  </style>
