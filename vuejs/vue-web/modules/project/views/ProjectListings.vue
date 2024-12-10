<template>
	<v-container>
		<template v-if="isAuthorized">
			<!-- Page Title -->
			<h2>Project Management</h2>
			<v-divider></v-divider>
			<!-- Header with Create Button -->
			<v-row class="d-flex align-center mb-4">
				<v-col cols="12" md="4">
					<v-text-field
						v-model="searchQuery"
						label="Search Projects"
						placeholder="Type to search..."
						clearable
						outlined
						dense
					></v-text-field>
				</v-col>
				<v-col cols="12" md="4">
					<v-select
						v-model="selectedFilter"
						:items="filters"
						label="Filter by Status"
						placeholder="Select a filter"
						clearable
						outlined
						dense
					></v-select>
				</v-col>
				<v-col cols="12" md="4">
					<v-select
						v-model="selectedPriority"
						:items="priorityFilters"
						label="Filter by Priority"
						placeholder="Select priority"
						clearable
						outlined
						dense
					></v-select>
				</v-col>
				<v-col v-if="isAuthorized()" cols="12" class="d-flex justify-end">
					<v-btn depressed @click="createProject">Create Project</v-btn>
				</v-col>
			</v-row>
			<section v-if="hasData" style="height:100%;">
				<template v-if="modelLoading">
					<v-skeleton-loader type="article"></v-skeleton-loader>
				</template>
				<template v-else>
					<!-- Display Projects -->
					<v-list two-line class="px-6 transparent-list">
						<v-divider></v-divider>
						<v-list-item
							v-for="project in projects"
							:key="project.id"
							class="px-0 hover-elevate"
							@click="infoProject(project.id)"
						>
							<v-row class="pa-2 align-center">
								<!-- Project Details -->
								<v-col cols="12" sm="4">
									<v-list-item-title class="font-weight-bold text-wrap">{{ project.name }}</v-list-item-title>
									<v-list-item-subtitle style="line-height: unset !important;">
										<pre class="text-wrap">{{ project.description || "No description available." }}</pre>
									</v-list-item-subtitle>
								</v-col>
								<!-- Status and Priority -->
								<v-col cols="12" sm="3" class="d-flex flex-column align-center">
									<v-chip
										:color="getStatusColor(project.status)"
										dark
										class="mb-1"
										outlined
										small
									>
										Status: {{ project.status }}
									</v-chip>
									<v-chip
										v-if="project.priority !== '-'"
										:color="getPriorityColor(project.priority)"
										class="mb-1"
										outlined
										small
									>
										Priority: {{ project.priority }}
									</v-chip>
								</v-col>
								<!-- Dates -->
								<v-col cols="12" sm="3" class="text-end">
									<p class="text-caption mb-1">
										<strong>Start:</strong> {{ formatDate(project.start_date, dateFormat) }}
									</p>
									<p class="text-caption">
										<strong>End:</strong> {{ formatDate(project.end_date, dateFormat) }}
									</p>
								</v-col>
								<!-- Actions -->
								<v-col
									v-if="isAuthorized()" cols="12" sm="2"
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
												<v-list-item @click="editProject(project)">
													<v-list-item-title>Edit</v-list-item-title>
												</v-list-item>
												<v-list-item @click="confirmDelete(project.id)">
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
					<!-- Project Count -->
					<div class="text-end mt-2">Total Projects: {{ totalProjects }}</div>
				</template>
			</section>
			<section v-else style="height:100%;">
				<!-- Show No Projects Image -->
				<v-row class="justify-center">
					<v-col cols="12" class="text-center">
						<img src="/images/no-product-available.png" alt="No projects available" class="my-4" />
						<p>No projects available.</p>
					</v-col>
				</v-row>
			</section>
			<!-- Confirmation Dialog -->
			<v-dialog v-model="deleteDialog" max-width="500px">
				<v-card>
					<v-card-title class="text-h6">Confirm Delete</v-card-title>
					<v-card-text>
						Are you sure you want to delete this project? This action cannot be undone.
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn text @click="deleteDialog = false">Cancel</v-btn>
						<v-btn
							:loading="isLoading" color="red" text
							@click="deleteProject"
						>
							Delete
						</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>
		</template>
		<template v-else>
			<p>You do not have permission to view this page.</p>
		</template>
	</v-container>
</template>

<script>
import GeneralClient from '../../_general/client';
import ProjectClient from "../client"
import { formatDate } from '@utils/dateUtils';

export default {
	data() {
		return {
			hasData: true,
			modelLoading: true,
			searchQuery: "",
			selectedFilter: null,
			currentPage: 1,
			itemsPerPage: 10,
			filters: ["All", "Pending", "Ongoing", "Completed"],
			projects: [],
			paginator:{},
			paginationLength: 0,
			totalProjects: 0,
			isLoading: false,
			selectedPriority: null,
			priorityFilters: ["All", "High", "Medium", "Low"],

			deleteDialog: false,
			selectedProjectId: null,
			dateFormat: 'DD/MM/YYYY',
		};
	},
	watch: {
		searchQuery: "fetchProjects",
		selectedFilter: "fetchProjects",
		currentPage: "fetchProjects",
		selectedPriority: "fetchProjects",
	},
	mounted() {
		this.fetchAndApplyUserSettings();
		this.fetchProjects();
	},
	methods: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin' || userRole === 'Project Manager';
		},
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
		createProject() {
			this.$router.push({ name: "project-create-page" });
		},
		editProject(project) {
			this.$router.push({ name: "project-edit-page", params: { id: project.id } });
		},
		infoProject(projectId){
			this.$router.push({ name: "project-info-page", params: { id: projectId } });
		},
		confirmDelete(projectId) {
			this.selectedProjectId = projectId;
			this.deleteDialog = true;
		},
		deleteProject() {
			this.isLoading = true;
			if (this.selectedProjectId) {
				ProjectClient.deleteProject(this.selectedProjectId)
					.then(() => {
						this.isLoading = false
						this.projects = this.projects.filter(
							(project) => project.id !== this.selectedProjectId
						);
						this.totalProjects = this.projects.length
						this.$toast.success("Project deleted successfully.");
						if(this.totalProjects == 0){
							this.hasData = false;
						}
					})
					.catch((error) => {
						console.error("Error deleting project:", error);
					})
					.finally(() => {
						this.isLoading = false
						this.deleteDialog = false;
						this.selectedProjectId = null;
					});
			}
		},
		fetchProjects() {
			this.modelLoading = true;
			this.hasData = true;
			this.totalProjects = 0;
			const payload = {
				searchQuery: this.searchQuery,
				selectedFilter: this.selectedFilter,
				page: this.currentPage,
				itemsPerPage: this.itemsPerPage,
				selectedPriority: this.selectedPriority,
			};

			ProjectClient.getProjectListings(payload)
				.then((response) => {
					let {data,...pagination} = response.data.projects
					this.projects = data
					this.hasData = data.length > 0;
					this.paginationLength = Math.ceil(pagination.total/pagination.per_page);
					this.paginator = pagination
					this.totalProjects = response.data.total;
				})
				.catch((error) => {
					console.error("Error fetching projects:", error);
					this.hasData = false;
				}).finally(()=>{
					this.modelLoading = false
				});
		},
		getStatusColor(status) {
			const colors = {
				Pending: "red",
				Ongoing: "yellow",
				Completed: "green",
			};
			return colors[status];
		},
		getPriorityColor(priority) {
			const colors = {
				High: "red",
				Medium: "orange",
				Low: "green",
			};
			return colors[priority];
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