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
		<v-row v-if="isAuthorized">
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

		<!-- Suggested Tasks Section -->
		<v-row v-if="suggestedTasks.length">
			<v-col cols="12">
				<h3 class="pb-2">Suggested Tasks</h3>
				<v-list two-line class="px-6 rounded-4">
					<v-divider></v-divider>
					<v-list-item v-for="(task, index) in suggestedTasks" :key="index">
						<div>
							<v-list-item-title>{{ task.name }}</v-list-item-title>
							<v-list-item-subtitle>
								<strong>Suggested Assignee:</strong> {{ task.assignee }}
							</v-list-item-subtitle>
						</div>
						<v-list-item-action>
							<v-btn
								color="white"
								small
								depressed
								class="mt-4"
								@click="approveTask(task)"
							>
								Approve
							</v-btn>
						</v-list-item-action>
					</v-list-item>
					<v-divider></v-divider>
					<div class="text-center">
						<v-alert
							type="info"
							class="px-6 py-3"
						>
							<span class="font-weight-bold">Reminder:</span> Please make sure to <strong>review</strong> and add a detailed description to the newly created tasks for better clarity. This will help the assignee and the entire team understand the task goals clearly.
						</v-alert>
					</div>
				</v-list>
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
							<p><strong>Start Date:</strong> {{ formatDate(project.start_date, dateFormat) }}</p>
							<p><strong>End Date:</strong> {{ formatDate(project.end_date, dateFormat) }}</p>
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
import { formatDate } from '@utils/dateUtils';
import GeneralClient from '../../_general/client';

export default {
	components: {
		TaskListings,
	},
	data() {
		return {
			project: {},
			projectMembers: [],
			attachment: null,
			activeTab: 0,
			roles: [],
			aiFeedback: null,
			suggestedTasks: [],
			isLoading: false,
			dateFormat: 'DD/MM/YYYY',
		};
	},
	computed: {
		downloadUrl() {
			return `/api/project/download/${this.project.id}`;
		}
	},
	mounted() {
		this.fetchAndApplyUserSettings();
		const projectId = this.$route.params.id;
		this.fetchProjectDetails(projectId);
		this.fetchAttachment(projectId);
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
		generateAIInsights() {
			this.isLoading = true;
			AIClient.getProjectInsight(this.project.id)
				.then((response) => {
					this.aiFeedback = response.data;
					console.log(response)
					this.suggestedTasks = response.data.suggested_tasks || [];
				})
				.catch((error) => {
					console.error("Error generating AI insights:", error);
				}).finally(() => {
					this.isLoading = false
				});
		},
		approveTask(task) {
			const assignee = this.projectMembers.find(member => member.name === task.assignee);
			if (!assignee) {
				this.$toast.error(`Assignee ${task.assignee} not found.`);
				return;
			}

			// Prepare task data to send to the backend
			const taskData = {
				name: task.name,
				description: task.description || "",
				due_date: task.due_date || null,
				assignee_id: assignee.id,
				project_id: this.project.id,
			};

			// Call the API to approve the task
			this.isLoading = true;
			AIClient.approveTask(taskData)
				.then(() => {
					this.$toast.success(`Task "${task.name}" has been approved and created.`);
					this.suggestedTasks = this.suggestedTasks.filter(t => t !== task);
				})
				.catch((error) => {
					console.error("Error approving task:", error);
					this.$toast.error(`Failed to approve task "${task.name}".`);
				})
				.finally(() => {
					this.isLoading = false;
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
