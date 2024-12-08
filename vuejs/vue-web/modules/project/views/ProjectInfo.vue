<template>
	<v-container>
		<!-- Project Title -->
		<v-row>
			<v-col cols="12">
				<h1>{{ project.name }}</h1>
				<p>{{ project.description || "No description available." }}</p>
			</v-col>
		</v-row>

		<!-- Generate AI Feedback Button -->
		<v-row>
			<v-col cols="12" class="text-end mb-4">
				<v-btn
					:loading="isLoading" color="white" depressed
					@click="generateAIInsights"
				>
					Generate AI Feedback
				</v-btn>
			</v-col>
		</v-row>

		<!-- AI Feedback Section -->
		<v-row v-if="aiFeedback">
			<v-col cols="12">
				<h3>AI Feedback</h3>
				<p v-html="renderMarkdown(aiFeedback.gpt_reply)"></p>
			</v-col>
		</v-row>

		<!-- Tabs for Navigation -->
		<v-tabs v-model="activeTab" grow>
			<v-tab>Overview</v-tab>
			<v-tab>Task Listings</v-tab>
		</v-tabs>

		<v-tabs-window v-model="activeTab">
			<!-- Overview Tab -->
			<v-tab-window-item v-if="activeTab === 0">
				<v-container>
					<h2>Project Overview</h2>
					<v-row>
						<v-col cols="12" sm="6">
							<p>
								<strong>Status:</strong> <v-chip
									:color="getStatusColor(project.status)"
									dark
									class="mb-1"
									outlined
									small
								>
									{{ project.status }}
								</v-chip>
							</p>
							<p>
								<strong>Priority:</strong> <v-chip
									:color="getPriorityColor(project.priority)"
									class="mb-1"
									outlined
									small
								>
									{{ project.priority }}
								</v-chip>
							</p>
						</v-col>
						<v-col cols="12" sm="6">
							<p><strong>Start Date:</strong> {{ project.start_date }}</p>
							<p><strong>End Date:</strong> {{ project.end_date }}</p>
						</v-col>
					</v-row>
					<v-divider class="my-4"></v-divider>

					<h3>Project Members</h3>
					<ul>
						<li v-for="member in projectMembers" :key="member.id">
							{{ member.name }} - {{ getRoleName(member.role_id) }}
						</li>
					</ul>

					<v-divider class="my-4"></v-divider>

					<h3>Attachment</h3>
					<p v-if="attachment">
						<strong>Attachment Type:</strong> {{ attachment.file_type }} <br />
						<a :href="downloadUrl" :download="attachment.file_name" target="_blank">Download</a>
					</p>
					<p v-else>No attachment found</p>

					<v-divider class="my-4"></v-divider>

					<h3>Additional Information</h3>
					<p>
						<strong>Budget: </strong>
						<span v-if="project.budget">{{ project.budget.amount }}</span>
						<span v-else>N/A</span>
					</p>
				</v-container>
			</v-tab-window-item>

			<!-- Task Listings Tab -->
			<v-tab-window-item v-if="activeTab === 1">
				<TaskListings :project-id="project.id" />
			</v-tab-window-item>
		</v-tabs-window>
	</v-container>
</template>

<script>
import TaskListings from "../../task/views/TaskListings.vue";
import ProjectClient from "../client";
import AIClient from "../../base/client";
import { marked } from "marked";

export default {
	components: {
		TaskListings,
	},
	data() {
		return {
			project: {},
			projectMembers: [],
			attachment: null,  // Store the attachment data
			activeTab: 0,
			roles: [],
			aiFeedback: null,
			isLoading: false,
		};
	},
	computed: {
		downloadUrl() {
			return `/api/project/download/${this.project.id}`;
		}
	},
	mounted() {
		const projectId = this.$route.params.id;
		this.fetchProjectDetails(projectId);
		this.fetchAttachment(projectId);
	},
	methods: {
		generateAIInsights() {
			this.isLoading = true;
			AIClient.getProjectInsight(this.project.id)
				.then((response) => {
					this.aiFeedback = response.data;
					this.isLoading = false
				})
				.catch((error) => {
					console.error("Error generating AI insights:", error);
				}).finally(() => {
					this.isLoading = false
				});
		},
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
		fetchAttachment(id) {
			ProjectClient.fetchAttachment(id)  // Call the API to fetch attachment details
				.then((response) => {
					this.attachment = response.data;  // Store attachment details
				})
				.catch((error) => {
					console.error("Error fetching attachment:", error);
				});
		},
		getFileName(filePath) {
		// Extract the file name from the file path
			return filePath.split('/').pop();
		},
		getRoleName(roleId) {
			const role = this.roles.find((role) => role.id === roleId);
			return role ? role.name : "Unknown Role";
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
		renderMarkdown(text){
			return marked(text);
		}
	},
};
</script>

<style scoped>
.v-tabs {
	margin-bottom: 20px;
}
</style>
