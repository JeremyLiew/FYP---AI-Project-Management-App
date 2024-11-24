<template>
	<v-container>
		<v-row class="d-flex align-center">
			<v-col cols="12" md="8">
				<v-text-field
					v-model="searchQuery"
					label="Search Projects"
					placeholder="Type to search..."
					clearable
					outlined
					dense
					class="mb-4"
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
					class="mb-4"
				></v-select>
			</v-col>
		</v-row>
		<v-row>
			<v-col
				v-for="project in filteredProjects"
				:key="project.id"
				cols="12"
				md="4"
				lg="3"
				class="d-flex"
			>
				<v-card
					outlined
					class="project-card"
					@click="goToProject(project.id)"
				>
					<v-card-title>{{ project.name }}</v-card-title>
					<v-card-subtitle>
						{{ project.description || "No description available." }}
					</v-card-subtitle>
					<v-card-actions>
						<v-btn text color="primary">View Details</v-btn>
					</v-card-actions>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			searchQuery: "",
			selectedFilter: null,
			filters: ["All", "Ongoing", "Completed", "Pending"],
			projects: [
				{ id: 1, name: "Project Alpha", status: "Ongoing", description: "Building a prototype." },
				{ id: 2, name: "Project Beta", status: "Completed", description: "Completed e-commerce platform." },
				{ id: 3, name: "Project Gamma", status: "Pending", description: "Approval stage." },
				// Add more projects as needed
			],
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

			console.log(filtered);

			return filtered;
		},
	},
	methods: {
		goToProject(projectId) {
			this.$router.push({ name: "ProjectDetails", params: { id: projectId } });
		},
	},
};
</script>

<style scoped>
.project-card {
  cursor: pointer;
  transition: transform 0.3s;
}

.project-card:hover {
  transform: translateY(-5px);
}
</style>

