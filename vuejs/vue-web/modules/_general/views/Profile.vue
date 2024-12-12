<template>
	<v-container>
		<!-- Header Section -->
		<v-row class="mb-4 align-center">
			<v-col cols="6">
				<h2 class="text-h5 font-weight-bold">My Account</h2>
			</v-col>
			<v-col cols="6" class="d-flex justify-end">
				<v-btn :loading="is_loading" color="error" @click="logout">Logout</v-btn>
			</v-col>
		</v-row>

		<template v-if="modelLoading">
			<v-skeleton-loader type="article"></v-skeleton-loader>
		</template>
		<template v-else>
			<!-- User Information -->
			<v-card outlined class="mb-4">
				<v-card-title>
					<h3 class="text-h6 font-weight-bold">User Information</h3>
				</v-card-title>
				<v-card-text>
					<v-row>
						<v-col cols="12" md="4" class="text-center">
							<v-avatar size="120" class="cursor-pointer" @click="triggerFileInput">
								<img
									:src="user.profilePicture ? `/storage/${user.profilePicture}` : defaultAvatar"
									alt="Profile Picture"
									class="profile-picture"
								/>
							</v-avatar>
							<!-- Hidden file input for profile picture upload -->
							<input
								ref="fileInput"
								type="file"
								accept="image/*"
								style="display: none"
								@change="uploadProfilePicture"
							/>
						</v-col>
						<v-col cols="12" md="4">
							<v-text-field
								v-model="user.name"
								label="Name"
								outlined
								dense
								:loading="is_loading"
							/>
						</v-col>
						<v-col cols="12" md="4">
							<p><strong>Email:</strong> {{ user.email }}</p>
						</v-col>
						<v-col cols="12" md="6">
							<p><strong>Role:</strong> {{ user.role }}</p>
						</v-col>
						<v-col cols="12" md="6" class="text-md-end">
							<v-btn color="white" depressed @click="updateName">Update Name</v-btn>
						</v-col>
					</v-row>
					<div class="text-center mt-2">
						<small style="color: #666; font-style: italic;">
							<strong>Important:</strong> After changing your profile picture / username, please <strong>refresh the page</strong> for the changes to take effect across the application.
						</small>
					</div>
				</v-card-text>
			</v-card>
			<!-- Projects Section -->
			<v-card outlined class="mb-4">
				<v-card-title>
					<h3 class="text-h6 font-weight-bold">Projects</h3>
				</v-card-title>
				<v-card-text>
					<v-row>
						<v-col
							v-for="project in projects"
							:key="project.id"
							cols="12"
							md="6"
						>
							<v-card class="pa-3 mb-3">
								<h4 class="text-subtitle-1">{{ project.name }}</h4>
								<p>{{ project.description }}</p>
							</v-card>
						</v-col>
					</v-row>
				</v-card-text>
			</v-card>
			<!-- Tasks Section -->
			<v-card outlined>
				<v-card-title>
					<h3 class="text-h6 font-weight-bold">My Tasks</h3>
				</v-card-title>
				<v-card-text>
					<v-list>
						<v-list-item
							v-for="task in tasks"
							:key="task.id"
							class="mb-2"
						>
							<v-row class="pa-2 align-center">
								<v-col cols="12" sm="8">
									<v-list-item-title>{{ task.name }}</v-list-item-title>
									<v-list-item-subtitle>Status: {{ task.status }}</v-list-item-subtitle>
								</v-col>
								<v-col cols="12" sm="4" class="d-flex flex-column align-end">
									<v-chip
										small
										:color="getTaskStatusColor(task.status)"
										class="white--text"
									>
										{{ task.status }}
									</v-chip>
								</v-col>
							</v-row>
						</v-list-item>
					</v-list>
				</v-card-text>
			</v-card>
		</template>
	</v-container>
</template>

<script>
import GeneralClient from "../client";

export default {
	data() {
		return {
			user: {},
			projects: [],
			tasks: [],
			is_loading: false,
			profile_picture: null,
			defaultAvatar: '/images/avatar.jpg',
			modelLoading: true,
		};
	},
	created() {
		this.user = this.$auth.user().user;
		this.fetchProfile();
	},
	methods: {
		fetchProfile() {
			this.modelLoading = true;
			GeneralClient.fetchProfile()
				.then((response) => {
					this.user = response.data.user;
					this.projects = response.data.projects;
					this.tasks = response.data.tasks;
				})
				.catch((error) => {
					console.error("Error fetching profile data:", error);
				}).finally(()=>{
					this.modelLoading = false
				});
		},
		getTaskStatusColor(status) {
			const statusColors = {
				pending: "yellow darken-2",
				in_progress: "blue darken-2",
				completed: "green darken-2",
			};
			return statusColors[status] || "grey darken-2";
		},
		async logout() {
			this.is_loading = true;
			this.$auth
				.logout({
					makeRequest: true,
					data: {},
					redirect: false,
				})
				.finally(() => {
					this.is_loading = false;
					this.$toast.success("Logged out successfully");
					setTimeout(() => {
						this.$router.push({ name: "login-page" });
					}, 500);
				});
		},
		triggerFileInput() {
			this.$refs.fileInput.click();
		},
		uploadProfilePicture(event) {
			const file = event.target.files[0];
			if (file) {
				const formData = new FormData();
				formData.append('profile_picture', file);

				GeneralClient.uploadProfilePicture(formData)
					.then((response) => {
						this.user.profilePicture = response.data.file_path;
						this.$toast.success('Profile picture updated successfully.');
					})
					.catch((error) => {
						console.error('Error uploading profile picture:', error);
						this.$toast.error('Failed to upload profile picture.');
					});
			}
		},
		updateName() {
			this.is_loading = true;
			GeneralClient.updateUserName(this.user.name)
				.then((response) => {
					this.$toast.success('Name updated successfully.');
				})
				.catch((error) => {
					console.error('Error updating name:', error);
					this.$toast.error(error.response.data.message);
				})
				.finally(() => {
					this.is_loading = false;
				});
		}
	},
};
</script>

<style scoped>
h3,
h4 {
	margin: 0;
}

.v-card {
	border-radius: 8px;
}

.v-list-item {
	border-radius: 4px;
	padding: 10px;
}

.profile-picture {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 50%;
	cursor: pointer;
}
</style>
