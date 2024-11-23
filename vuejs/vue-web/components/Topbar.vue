<template>
	<v-navigation-drawer
		app
		rail
		expand-on-hover
		class="elevation-1"
		style="background-color: #FFE1EA;"
		permanent
		@mouseenter="onHover(true)"
		@mouseleave="onHover(false)"
	>
		<!-- Sidebar Content -->
		<v-list dense style="display:flex; flex-direction: column; height: 100%;">
			<!-- User Profile -->
			<v-list-item
				prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg"
				subtitle="sandra_a88@gmail.com"
				title="Sandra Adams"
				@click="goToProfile"
			></v-list-item>

			<v-divider></v-divider>

			<!-- Navigation Links -->
			<template v-for="(nav_link, i) in nav_links" :key="i">
				<v-list-item
					:to="nav_link.to"
					exact
					class="font-primary"
					exact-active-class="btnActive"
					:ripple="false"
				>
					<v-list-item-content style="display: flex;">
						<v-list-item-icon>
							<v-icon>{{ nav_link.icon }}</v-icon>
						</v-list-item-icon>
						<!-- Title only shown when drawer is hovered -->
						<v-list-item-title v-if="drawerHovered" class="pl-3">
							{{ nav_link.title }}
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</template>

			<v-divider></v-divider>

			<!-- Settings Link (at the bottom) -->
			<v-list-item class="mt-auto" style="justify-self: end;" @click="goToSettings">
				<v-list-item-content style="display: flex;">
					<v-list-item-icon>
						<v-icon>mdi-cog</v-icon>
					</v-list-item-icon>
					<v-list-item-title v-if="drawerHovered" class="pl-3">Settings</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
export default {
	data() {
		return {
			nav_links: [],
			auth: false,
			drawerHovered: false,
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
			{ title: "Contact Us",icon: 'mdi-card-account-mail-outline', to : { name: "contact-us-page" } },
		];
		this.auth = false;
		if (this.$auth.check()) {
			this.auth = true;
		}
	},
	methods: {
		onHover(status) {
			this.drawerHovered = status;
		},
		goToProfile() {
			// Navigate to the profile page
			this.$router.push({ name: 'profile-page' });
		},
		goToSettings() {
			// Navigate to the settings page
			// this.$router.push({ name: 'settings-page' });
		},
	},
};
</script>

<style scoped>
.btnActive {
	color: #DE002B !important;
	font-weight: bold;
}
.custom-icon {
	width: 32px !important;
	height: 32px !important;
}
.mt-auto {
	margin-top: auto;
}
</style>
