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
				<template v-if="isAuthorized()">
					<v-list-item @click="goToUserMaintenance">
						<v-list-item-media>
							<v-icon>mdi-account-multiple</v-icon>
						</v-list-item-media>
						<v-list-item-title v-if="drawerHovered" class="pl-3">User Maintenance</v-list-item-title>
					</v-list-item>
					<v-list-item @click="goToActivityLog">
						<v-list-item-media>
							<v-icon>mdi-math-log</v-icon>
						</v-list-item-media>
						<v-list-item-title v-if="drawerHovered" class="pl-3">Activity Log</v-list-item-title>
					</v-list-item>
				</template>
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
	<v-app-bar app dense>
		<v-toolbar-title>Project AI</v-toolbar-title>
		<v-spacer></v-spacer>
		<!-- Add date and time here -->
		<v-chip class="mr-4" outlined>{{ formattedDateTime }}</v-chip>
	</v-app-bar>
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
			currentDate: new Date(),
		};
	},
	computed: {
		authPage() {
			return this.auth ? { name: 'profile-page' } : { name: 'login-page' };
		},
		formattedDateTime() {

			const userTimezone = localStorage.getItem('timezone') || 'UTC';

			const options = {
				day: '2-digit',
				month: '2-digit',
				year: 'numeric',
				hour: '2-digit',
				minute: '2-digit',
				second: '2-digit',
				hour12: false,
			};

			const localTime = this.currentDate.toLocaleString('en-GB', options);

			return `${localTime} (${userTimezone})`;
		},
	},
	beforeUnmount() {
		clearInterval(this.clockInterval);
	},
	mounted() {
		this.startClock();
	},
	created() {
		this.nav_links = [
			{ title: 'Home', icon: 'mdi-home', to: { name: 'home-page' } },
			{ title: 'Projects', icon: 'mdi-briefcase', to: { name: 'project-listings-page' } },
			{ title: 'Budgets', icon: 'mdi-currency-usd', to: { name: 'budget-listings-page' } },
			{ title: 'Expenses', icon: 'mdi-file-multiple', to: { name: 'expense-listings-page' } },
			{ title: 'Expense Categories', icon: 'mdi-tag-multiple', to: { name: 'expense-category-listings-page' } },
			{ title: 'Reports', icon: 'mdi-chart-box-outline', to: { name: 'report-page' }  },
			{ title: "Contact Us",icon: 'mdi-card-account-mail-outline', to : { name: "contact-us-page" } },
		];
		this.auth = false;
		if (this.$auth.check()) {
			this.auth = true;
			this.fetchNotificationCount();
			this.fetchProfile();
		}
	},
	methods: {
		startClock() {
			this.clockInterval = setInterval(() => {
				this.currentDate = new Date();
			}, 1000);
		},
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
		goToBudgets() {
			this.$router.push({ name: 'budget-listings-page' });
		},
		goToExpenses() {
			this.$router.push({ name: 'expense-listings-page' });
		},
		goToCategories() {
			this.$router.push({ name: 'expense-category-listings-page' });
		},
		goToReports() {
			this.$router.push({ name: 'report-page' });
		},
		goToNotifications() {
			this.$router.push({ name: 'notifications-page' });
		},
		goToActivityLog() {
			this.$router.push({ name: 'activity-log-page' });
		},
		goToUserMaintenance() {
			this.$router.push({ name: 'user-maintenance-page' });
		}
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
