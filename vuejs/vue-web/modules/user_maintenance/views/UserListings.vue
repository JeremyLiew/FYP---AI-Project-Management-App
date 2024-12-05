<template>
	<v-container>
		<!-- Page Title -->
		<h2>User Management</h2>
		<v-divider></v-divider>

		<!-- Header with Search and Filters -->
		<v-row class="d-flex align-center mb-4">
			<v-col cols="12" md="6">
				<v-text-field
					v-model="searchQuery"
					label="Search Users"
					placeholder="Type to search..."
					clearable
					outlined
					dense
				></v-text-field>
			</v-col>

			<v-col cols="12" md="6" class="d-flex justify-end">
				<v-btn depressed @click="fetchUsers">Refresh List</v-btn>
			</v-col>
		</v-row>

		<section v-if="hasData" style="height:100%;">
			<template v-if="modelLoading">
				<v-skeleton-loader type="table"></v-skeleton-loader>
			</template>
			<template v-else>
				<!-- Display Users -->
				<v-list two-line class="px-6 transparent-list">
					<v-divider></v-divider>
					<v-list-item
						v-for="(user) in users"
						:key="user.id"
						class="px-0 hover-elevate"
						@click="viewUserDetails(user)"
					>
						<v-row class="pa-2 align-center">
							<!-- User Details -->
							<v-col cols="12" sm="6">
								<v-list-item-title class="font-weight-bold text-wrap">
									{{ user.name }}
								</v-list-item-title>
								<v-list-item-subtitle style="line-height: unset !important;">
									Email: {{ user.email }}
								</v-list-item-subtitle>
							</v-col>
							<!-- Role -->
							<v-col cols="12" :sm="$auth.user().user.name == user.name?'6':'4'" class="d-flex align-center">
								<v-chip
									class="mb-1"
									outlined
									small
								>
									Role: {{ getRoleName(user.application_role_id) || "N/A" }}
								</v-chip>
							</v-col>
							<!-- Actions -->
							<v-col
								v-if="$auth.user().user.name !== user.name" cols="12" sm="2"
								class="text-end"
							>
								<v-list-item-action class="justify-content-md-end">
									<v-menu>
										<template #activator="{ props }">
											<v-btn icon v-bind="props">
												<v-icon>mdi-dots-vertical</v-icon>
											</v-btn>
										</template>
										<v-list>
											<v-list-item @click="editUser(user)">
												<v-list-item-title>Edit</v-list-item-title>
											</v-list-item>
											<v-list-item @click="confirmDelete(user.id)">
												<v-list-item-title>Delete</v-list-item-title>
											</v-list-item>
										</v-list>
									</v-menu>
								</v-list-item-action>
							</v-col>
						</v-row>
					</v-list-item>
					<v-divider></v-divider>
				</v-list>

				<!-- Pagination -->
				<v-pagination
					v-model="currentPage"
					:length="paginationLength"
					class="mt-4 pl-0"
				></v-pagination>
				<!-- User Count -->
				<div class="text-end mt-2">Total Users: {{ users.length }}</div>
			</template>
		</section>

		<section v-else>
			<!-- Show No Users Image -->
			<v-row class="justify-center">
				<v-col cols="12" class="text-center">
					<img src="/images/no-product-available.png" alt="No users available" class="my-4" />
					<p>No users available.</p>
				</v-col>
			</v-row>
		</section>

		<!-- Delete Confirmation Dialog -->
		<v-dialog v-model="deleteDialog" max-width="500px">
			<v-card>
				<v-card-title class="text-h6">Confirm Delete</v-card-title>
				<v-card-text>
					Are you sure you want to delete this user? This action cannot be undone.
				</v-card-text>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn text @click="deleteDialog = false">Cancel</v-btn>
					<v-btn
						:loading="isLoading"
						color="red"
						text
						@click="deleteUser"
					>
						Delete
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<!-- Dialog for Viewing/Editing User -->
		<v-dialog v-model="showUserDialog" max-width="600px">
			<v-card>
				<v-card-title class="text-h6">
					{{ isEditing ? "Edit User" : "View User" }}
				</v-card-title>
				<v-card-text>
					<v-text-field
						v-model="currentUser.name"
						label="Name"
						outlined
						dense
						:readonly="!isEditing"
						:error-messages="errors.name"
					/>
					<v-text-field
						v-model="currentUser.email"
						label="Email"
						outlined
						dense
						disabled
					/>
					<v-select
						v-model="currentUser.application_role_id"
						:items="roles"
						item-value="id"
						item-title="name"
						label="Role"
						outlined
						required
						:readonly="!isEditing"
					></v-select>
				</v-card-text>
				<v-card-actions>
					<v-btn text @click="showUserDialog = false">Close</v-btn>
					<v-btn v-if="isEditing" color="primary" @click="saveUser">Save</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-container>
</template>

<script>
import UserMaintenanceClient from "../client";

export default {
	data() {
		return {
			searchQuery: "",
			currentPage: 1,
			itemsPerPage: 10,
			users: [],
			paginationLength: 0,
			hasData: true,
			modelLoading: true,
			deleteDialog: false,
			selectedUserId: null,
			isLoading: false,
			showUserDialog: false,
			isEditing: false,
			currentUser: {},
			roles: [],
			errors: {},
		};
	},
	watch: {
		currentPage: "fetchUsers",
		searchQuery: "fetchUsers",
	},
	mounted() {
		this.fetchUsers();
		this.fetchApplicationRole();
	},
	methods: {
		getRoleName(roleId) {
			const role = this.roles.find(role => role.id === roleId);
			return role ? role.name : null;
		},
		fetchApplicationRole() {
			UserMaintenanceClient.getApplicationRoles()
				.then((response) => {
					this.roles = response.data;
				})
				.catch((error) => {
					console.error("Error fetching application roles:", error);
					this.$toast.error("Failed to fetch roles. Please try again.");
				});
		},
		fetchUsers() {
			this.modelLoading = true;
			const params = {
				searchQuery: this.searchQuery,
				page: this.currentPage,
				itemsPerPage: this.itemsPerPage,
			};

			UserMaintenanceClient.getAllUsers(params)
				.then((response) => {
					const { data, total, per_page } = response.data;
					this.users = data;
					this.hasData = data.length > 0;
					this.paginationLength = Math.ceil(total / per_page);
				})
				.catch((error) => {
					console.error("Error fetching users:", error);
					this.hasData = false;
				})
				.finally(() => {
					this.modelLoading = false;
				});
		},
		viewUserDetails(user) {
			this.currentUser = { ...user };
			this.isEditing = false;
			this.showUserDialog = true;
		},
		editUser(user) {
			this.currentUser = { ...user };
			this.isEditing = true;
			this.showUserDialog = true;
		},
		saveUser() {
			this.errors = {};
			if (!this.currentUser || !this.currentUser.id) {
				this.$toast.error("No user selected for update.");
				return;
			}

			const payload = { ...this.currentUser }

			UserMaintenanceClient.updateUser(payload)
				.then(() => {
					this.$toast.success("User updated successfully.");
					this.showUserDialog = false;
					this.fetchUsers();
				})
				.catch((error) => {
					console.error("Error updating user:", error);
					this.$toast.error("Failed to update user. Please try again.");
					this.errors = error.response?.data.errors || {};
				});
		},
		confirmDelete(userId) {
			this.selectedUserId = userId;
			this.deleteDialog = true;
		},
		deleteUser() {
			this.isLoading = true;
			UserMaintenanceClient.deleteUser(this.selectedUserId)
				.then(() => {
					this.users = this.users.filter((user) => user.id !== this.selectedUserId);
					this.$toast.success("User deleted successfully.");
					this.deleteDialog = false;
					if (!this.users.length) this.hasData = false;
				})
				.catch((error) => {
					console.error("Error deleting user:", error);
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		viewUser(userId) {
			console.log("Viewing user details for ID:", userId);
			// Implement navigation or logic for viewing user details
		},
	},
};
</script>

  <style scoped>
  .v-list-item {
    transition: box-shadow 0.2s;
  }
  .v-list-item:hover {
    box-shadow: 0px 4px 12px #323232;
  }
  .dark-theme-hover.v-list-item:hover {
    box-shadow: 0px 4px 12px #212121;
  }
  .v-list-item:not(:hover) {
    background-color: transparent !important;
  }
  </style>
