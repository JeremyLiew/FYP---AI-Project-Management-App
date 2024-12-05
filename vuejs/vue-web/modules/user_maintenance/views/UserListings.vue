<template>
	<template v-if="isAuthorized">
		<div>hi</div>
	</template>
	<template v-else>
		<p>You do not have permission to view this page.</p>
	</template>
</template>

<script>
import UserMaintenanceClient from "../client";

export default {
	data() {
		return {
			searchQuery: "",
			currentPage: 1,
			itemsPerPage: 10,
			paginationLength: 0,
			totalUsers: 0,
			users: [],
			hasData: true,
			modelLoading: true,
		};
	},
	computed: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		}
	},
	watch: {
		currentPage: "fetchUsers",
	},
	mounted() {
		this.fetchUsers();
	},
	methods: {
		fetchUsers() {
			UserMaintenanceClient.getAllUsers()
				.then((response) => {
					this.users = response.data;
				})
				.catch((error) => {
					console.error("Error fetching users:", error);
				});
		},
	}
}
</script>