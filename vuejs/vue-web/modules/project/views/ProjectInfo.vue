<template>
	<v-container>
		<!-- Project Title -->
		<v-row>
			<v-col cols="12">
				<h1>{{ project.name }}</h1>
				<p>{{ project.description }}</p>
			</v-col>
		</v-row>

		<!-- Tabs for Navigation -->
		<v-tabs v-model="activeTab" grow>
			<v-tab>Overview</v-tab>
			<v-tab>Task Listings</v-tab>
		</v-tabs>

		<v-tabs-items v-model="activeTab">
			<!-- Overview Tab -->
			<v-tab-item v-if="activeTab === 0">
				<v-container>
					<h2>Project Overview</h2>
					<v-row>
						<v-col cols="12">
							<p><strong>Details:</strong> {{ project.description }}</p>
							<h3>Project Members</h3>
							<ul>
								<li v-for="member in projectMembers" :key="member.id">
									{{ member.name }} - {{ getRoleName(member.role_id) }}
								</li>
							</ul>
						</v-col>
					</v-row>
				</v-container>
			</v-tab-item>

			<!-- Task Listings Tab -->
			<v-tab-item v-if="activeTab === 1">
				<TaskListings :project-id="project.id" />
			</v-tab-item>
		</v-tabs-items>
	</v-container>
</template>

<script>
import TaskListings from "../../task/views/TaskListings.vue";
import ProjectClient from "../client";

export default {
	components: {
		TaskListings,
	},
	data() {
		return {
			project: {},
			projectMembers: [],
			activeTab: 0,
			roles: [],
		};
	},
	mounted() {
		const projectId = this.$route.params.id;
		this.fetchProjectDetails(projectId);
	},
	methods: {
		fetchProjectDetails(id) {
			ProjectClient.fetchProject(id)
				.then((response) => {
					this.project = response.data.project;
					this.projectMembers = response.data.members;
					this.roles = response.data.roles;
				})
				.catch((error) => {
					console.error("Error fetching project details:", error);
				});
		},
		getRoleName(roleId) {
			const role = this.roles.find(role => role.id === roleId);
			return role ? role.name : 'Unknown Role';
		},
	},
};
</script>