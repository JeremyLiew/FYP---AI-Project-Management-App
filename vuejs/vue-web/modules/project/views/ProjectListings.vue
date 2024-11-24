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

		<section v-if="hasData" style="height:100%;">
			<template v-if="modelLoading">
				<v-skeleton-loader type="article"></v-skeleton-loader>
			</template>
			<template v-else>
				<!-- Display Projects if Available -->
				<v-list two-line class="px-6 transparent-list">
					<v-divider></v-divider>
					<v-list-item
						v-for="project in projects"
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
					:length="paginationLength"
					class="mt-4 pl-0"
				></v-pagination>
				<!-- Project Count -->
				<div class="text-end mt-2">Total Projects: {{ totalProjects }}</div>
			</template>
		</section>

		<section v-else style="height:100%;">
			<!-- Show No Projects Image if No Projects Available -->
			<v-row class="justify-center">
				<v-col cols="12" class="text-center">
					<img src="images/no-product-available.png" alt="No projects available" class="my-4" />
					<p>No projects available.</p>
				</v-col>
			</v-row>
		</section>
	</v-container>
</template>


<script>
import ProjectClient from "../client"
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
		};
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
			this.modelLoading = true;
			this.hasData = true;
			this.totalProjects = 0;
			const payload = {
				searchQuery: this.searchQuery,
				selectedFilter: this.selectedFilter,
				page: this.currentPage,
				itemsPerPage: this.itemsPerPage,
			};

			ProjectClient.getProjectListings(payload)
				.then((response) => {
					let {data,...pagination} = response.data.projects
					this.projects = data
					console.log(response.projects)
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