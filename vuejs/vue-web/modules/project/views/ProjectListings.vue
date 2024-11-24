<template>
	<v-container>
		<!-- Page Title -->
		<v-row>
			<v-col cols="12">
				<h1 class="text-subtitle-1">Project Management</h1>
			</v-col>
		</v-row>
		<!-- Header with Create Button -->
		<v-row class="d-flex align-center mb-4">
			<v-col cols="12" md="8">
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
					label="Filter by"
					placeholder="Select a filter"
					clearable
					outlined
					dense
				></v-select>
			</v-col>

			<v-col cols="12" class="d-flex justify-end">
				<v-btn depressed @click="createProject">Create Project</v-btn>
			</v-col>
		</v-row>

		<v-list two-line class="px-6 transparent-list">
			<v-divider></v-divider>
			<v-list-item
				v-for="project in paginatedProjects"
				:key="project.id" class="px-0 hover-elevate"
			>
				<v-row class="pa-2">
					<v-col cols="12" sm="10">
						<v-list-item-title>{{ project.name }}</v-list-item-title>
						<v-list-item-subtitle style="line-height:unset !important;">
							{{ project.description || "No description available." }}
						</v-list-item-subtitle>
					</v-col>
					<v-col cols="12" sm="2">
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
									<v-list-item @click="deleteProject(project.id)">
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
			:length="totalPages"
			class="mt-4"
		></v-pagination>

		<!-- Project Count -->
		<div class="text-end mt-2">Total Projects: {{ projects.length }}</div>
	</v-container>
</template>

<script>
import ProjectClient from "../client"
export default {
	data() {
		return {
			searchQuery: "",
			selectedFilter: null,
			currentPage: 1,
			itemsPerPage: 10,
			filters: ["All", "Pending", "Ongoing", "Completed"],
			projects: [],
		};
	},
	computed: {
		filteredProjects() {
			let filtered = this.projects;

			if (this.selectedFilter && this.selectedFilter !== "All") {
				filtered = filtered.filter(
					(project) => project.status === this.selectedFilter
				);
			}

			if (this.searchQuery) {
				filtered = filtered.filter((project) =>
					project.name.toLowerCase().includes(this.searchQuery.toLowerCase())
				);
			}

			return filtered;
		},
		paginatedProjects() {
			const start = (this.currentPage - 1) * this.itemsPerPage;
			const end = start + this.itemsPerPage;
			return this.filteredProjects.slice(start, end);
		},
		totalPages() {
			return Math.ceil(this.filteredProjects.length / this.itemsPerPage);
		},
	},
	watch: {
		searchQuery: "fetchProjects",
		selectedFilter: "fetchProjects",
		currentPage: "fetchProjects",
	},
	mounted() {
		this.fetchProjects();
	},
	methods: {
		createProject() {
			this.$router.push({ name: "project-create-page" });
		},
		editProject(project) {
			// Logic to navigate to the edit project page
			this.$router.push({ name: "EditProject", params: { id: project.id } });
		},
		deleteProject(projectId) {
			// Logic to delete the project
			this.projects = this.projects.filter((project) => project.id !== projectId);
		},
		fetchProjects() {
			const payload = {
				search: this.searchQuery,
				filter: this.selectedFilter,
				page: this.currentPage,
				perPage: this.itemsPerPage,
			};

			ProjectClient.getProjectListings(payload)
				.then((response) => {
					this.projects = response.data.data;
				})
				.catch((error) => {
					console.error("Error fetching projects:", error);
				});
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