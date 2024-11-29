<template>
	<v-container>
		<h2>Task Listings</h2>

		<!-- Search and Filter Section -->
		<v-row>
			<v-col cols="12" md="4">
				<v-text-field
					v-model="searchQuery"
					label="Search tasks"
					placeholder="Type to search..."
					clearable
					dense
					outlined
				/>
			</v-col>
			<v-col cols="12" md="4">
				<v-select
					v-model="selectedFilter"
					:items="statusOptions"
					label="Filter by Status"
					placeholder="Select a filter"
					clearable
					dense
					outlined
				/>
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
		</v-row>

		<template v-if="modelLoading">
			<v-skeleton-loader type="article"></v-skeleton-loader>
		</template>
		<template v-else>
			<!-- Task Listings -->
			<v-list two-line class="px-6 transparent-list">
				<v-divider></v-divider>
				<v-list-item class="text-center">
					<v-btn @click="addTask()">
						<v-icon>mdi-plus</v-icon> Add Item
					</v-btn>
				</v-list-item>
				<v-list-item
					v-for="task in tasks"
					:key="task.id"
					class="px-0 hover-elevate"
					@click="infoTask(task.id)"
				>
					<v-row class="pa-2 align-center">
						<!-- Task Details -->
						<v-col cols="12" sm="4">
							<v-list-item-title class="text-wrap">{{ task.name }}</v-list-item-title>
							<v-list-item-subtitle>
								<pre
									class="text-wrap"
								>{{ task.description }}</pre>
							</v-list-item-subtitle>
						</v-col>
						<!-- Status and Priority -->
						<v-col cols="12" sm="3" class="d-flex flex-column align-center">
							<v-chip
								:color="getStatusColor(task.status)"
								dark
								class="mb-1"
								outlined
								small
							>
								Status: {{ task.status }}
							</v-chip>
							<v-chip
								v-if="task.priority !== '-'"
								:color="getPriorityColor(task.priority)"
								class="mb-1"
								outlined
								small
							>
								Priority: {{ task.priority }}
							</v-chip>
						</v-col>
						<!-- Due Dates -->
						<v-col cols="12" sm="3" class="text-end">
							<p class="text-caption mb-1">
								<strong>Due Date:</strong> {{ formatDate(task.due_date) }}
							</p>
						</v-col>
						<!-- Actions -->
						<v-col cols="12" sm="2" class="text-end">
							<v-list-item-action class="justify-content-md-end">
								<v-menu>
									<template #activator="{ props }">
										<v-btn icon v-bind="props">
											<v-icon>mdi-dots-vertical</v-icon>
										</v-btn>
									</template>
									<v-list>
										<v-list-item @click="editTask(task)">
											<v-list-item-title>Edit</v-list-item-title>
										</v-list-item>
										<v-list-item @click="confirmDelete(task.id)">
											<v-list-item-title>Delete</v-list-item-title>
										</v-list-item>
									</v-list>
								</v-menu>
							</v-list-item-action>
						</v-col>
					</v-row>
				</v-list-item>
			</v-list>
			<div v-if="!hasData" class="text-center"><p>No tasks available.</p></div>
			<div class="text-end mt-2">Total Tasks: {{ totalTasks }}</div>
		</template>

		<!-- Confirmation Dialog -->
		<v-dialog v-model="deleteDialog" max-width="500px">
			<v-card>
				<v-card-title class="text-h6">Confirm Delete</v-card-title>
				<v-card-text>
					Are you sure you want to delete this task? This action cannot be undone.
				</v-card-text>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn text @click="deleteDialog = false">Cancel</v-btn>
					<v-btn
						:loading="isLoading" color="red" text
						@click="deleteTask"
					>
						Delete
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<!-- Add Task Dialog -->
		<v-dialog v-model="showAddEditDialog" max-width="600px">
			<v-card>
				<v-card-title class="text-h6">{{ isEdit ? 'Edit Task' : 'Add New Task' }}</v-card-title>
				<v-card-text>
					<v-text-field
						v-model="currentTask.name" label="Task Name *" outlined
						dense
						required
						:error-messages="errors.name"
					/>
					<v-textarea
						v-model="currentTask.description"
						label="Description"
						outlined
						dense
					/>
					<v-text-field
						v-model="currentTask.due_date"
						label="Due Date *"
						type="date"
						required
						outlined
						:min="tomorrow"
						:error-messages="errors.due_date"
					></v-text-field>
					<v-select
						v-model="currentTask.status"
						:items="statusOptions"
						label="Status *"
						outlined
						dense
						required
						:error-messages="errors.status"
					></v-select>
					<v-select
						v-model="currentTask.priority"
						:items="priorityOptions"
						label="Priority *"
						outlined
						dense
						required
						:error-messages="errors.priority"
					></v-select>
					<v-select
						v-model="currentTask.assigned_to"
						:items="members"
						item-title="name"
						item-value="id"
						label="Assign to Member"
						outlined
						dense
						required
						:error-messages="errors.assigned_to"
					/>
				</v-card-text>
				<v-card-actions>
					<v-btn text @click="resetNewTask();showAddEditDialog = false">Cancel</v-btn>
					<v-btn :loading="isLoading" color="primary" @click="isEdit ? saveTask('edit') : saveTask('create')">{{ isEdit? "Update":"Add" }}</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<v-dialog v-model="showViewDialog" max-width="600px">
			<v-card>
				<v-card-title class="text-h6">View Task</v-card-title>
				<v-card-text>
					<v-text-field
						v-model="currentTask.name"
						label="Task Name"
						outlined
						dense
						readonly
					/>
					<v-textarea
						v-model="currentTask.description"
						label="Description"
						outlined
						dense
						readonly
					/>
					<v-text-field
						v-model="currentTask.due_date"
						label="Due Date"
						type="date"
						outlined
						dense
						readonly
					/>
					<v-select
						v-model="currentTask.status"
						:items="statusOptions"
						label="Status"
						outlined
						dense
						readonly
					></v-select>
					<v-select
						v-model="currentTask.priority"
						:items="priorityOptions"
						label="Priority"
						outlined
						dense
						readonly
					></v-select>
					<v-select
						v-model="currentTask.assigned_to"
						:items="members"
						item-title="name"
						item-value="id"
						label="Assigned to Member"
						outlined
						dense
						readonly
					/>

					<!-- Comments Section -->
					<v-divider class="mt-4"></v-divider>
					<v-card-title class="text-h6">Comments</v-card-title>

					<!-- Add Comment Button -->
					<div class="text-center">
						<v-btn class="mt-2" @click="addCommentInput"><v-icon>mdi-plus</v-icon> Add Comment</v-btn>
					</div>

					<!-- Display Comment Input (If Active) -->
					<v-list>
						<!-- Existing Comments -->
						<template v-if="comments.length > 0">
							<v-list-item v-for="(comment, index) in comments" :key="comment.id">
								<v-row class="d-flex align-center">
									<!-- Author and Comment -->
									<v-col cols="10">
										<v-list-item-title class="text-subtitle-1 font-weight-bold">
											{{ comment.author }}
										</v-list-item-title>
										<v-list-item-subtitle>
											<pre
												class="text-wrap"
											>{{ comment.text }}</pre>
										</v-list-item-subtitle>
									</v-col>

									<!-- Actions (Edit and Delete) -->
									<v-col class="d-flex justify-end" cols="2">
										<v-btn size="small" icon @click="editComment(index)">
											<v-icon>mdi-pencil</v-icon>
										</v-btn>
										<v-btn size="small" icon @click="deleteComment(index)">
											<v-icon>mdi-delete</v-icon>
										</v-btn>
									</v-col>
								</v-row>
							</v-list-item>
						</template>



						<!-- No Comments Yet -->
						<v-list-item v-else>
							No comments yet.
						</v-list-item>

						<!-- New Comment Input -->
						<v-list-item v-if="showCommentInput">
							<v-textarea
								v-model="newComment"
								label="Write your comment..."
								outlined
								clearable
								append-inner-icon="mdi-send"
								:error-messages="errors.comment"
								@click:append-inner="submitComment"
							></v-textarea>
						</v-list-item>
					</v-list>
				</v-card-text>

				<v-card-actions>
					<v-btn text @click="showViewDialog = false">Close</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-container>
</template>

<script>
import TaskClient from '../client';

export default {
	props: {
		projectId: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			hasData: true,
			modelLoading: true,
			tasks: {},
			searchQuery: '',
			selectedFilter: null,
			statusOptions: ['All', 'Pending', 'Ongoing', 'Completed'],
			totalTasks: 0,
			isLoading: false,
			selectedPriority: null,
			priorityFilters: ["All", "High", "Medium", "Low"],
			priorityOptions: ["-", "High", "Medium", "Low"],

			deleteDialog: false,
			selectedTaskId: null,
			showAddEditDialog: false,
			showViewDialog: false,
			currentTask: {
				project_id: this.projectId,
				name: "",
				description: "",
				due_date: "",
				status: "Pending",
				priority: "-",
				assigned_to: null,
			},
			tomorrow: "",
			errors: {},

			members: [],
			isEdit: false,

			newComment: '',
			comments: [],
			showCommentInput: false,
		};
	},
	watch: {
		searchQuery: "fetchTasks",
		selectedFilter: "fetchTasks",
		selectedPriority: "fetchTasks",
	},
	mounted() {
		this.initializeDates();
		this.fetchTasks();
		this.fetchMembers();
	},
	methods: {
		initializeDates() {
			this.tomorrow = new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split("T")[0];
		},
		fetchMembers() {
			// Fetch project members from the backend
			TaskClient.fetchMembers(this.projectId)
				.then((response) => {
					this.members = response.data.members;
				})
				.catch((error) => {
					console.error("Error fetching project members:", error);
					this.$toast.error("Failed to fetch project members.");
				});
		},
		resetNewTask() {
			this.currentTask = {
				project_id: this.projectId,
				name: "",
				description: "",
				due_date: "",
				status: "Pending",
				priority: "-",
				assigned_to: null,
			};
			this.errors = {};
		},
		editTask(task) {
			this.isEdit = true
			this.currentTask = { ...task };
			this.showAddEditDialog = true;
		},
		addTask(){
			this.isEdit = false;
			this.showAddEditDialog = true;
			this.resetNewTask()
		},
		saveTask(action) {
			this.isLoading = true;
			this.errors = {};
			const assignedBy = this.$auth.user().user.id;

			const taskData = {
				...this.currentTask,
				assigned_by: assignedBy,
			};

			const taskRequest = action === 'create'
				? TaskClient.createTask(taskData)
				: TaskClient.updateTask(taskData);

			taskRequest
				.then(() => {
					this.$toast.success(`${action === 'create' ? 'Task created' : 'Task updated'} successfully!`);
					this.fetchTasks();
					this.showAddEditDialog = false;
					this.resetNewTask();
				})
				.catch((error) => {
					console.error(`Error ${action === 'create' ? 'creating' : 'updating'} task:`, error);
					this.errors = error.response?.data.errors || {};
				})
				.finally(() => {
					this.isLoading = false;
				});
		},
		infoTask(taskId) {
			const task = this.tasks.find(t => t.id === taskId);
			this.currentTask = { ...task };
			this.showCommentInput = false;
			this.showViewDialog = true;
			this.fetchComments(taskId);
		},
		confirmDelete(taskId) {
			this.selectedTaskId = taskId;
			this.deleteDialog = true;
		},
		deleteTask() {
			this.isLoading = true;
			if (this.selectedTaskId) {
				TaskClient.deleteTask(this.selectedTaskId)
					.then(() => {
						this.isLoading = false
						this.tasks = this.tasks.filter(
							(task) => task.id !== this.selectedTaskId
						);
						this.totalTasks = this.tasks.length
						this.$toast.success("Task deleted successfully.");
						if(this.totalTasks == 0){
							this.hasData = false;
						}
					})
					.catch((error) => {
						console.error("Error deleting task:", error);
					})
					.finally(() => {
						this.isLoading = false
						this.deleteDialog = false;
						this.selectedTaskId = null;
					});
			}
		},
		fetchTasks() {
			this.modelLoading = true;
			this.hasData = true;
			let payload = {
				searchQuery: this.searchQuery,
				selectedFilter: this.selectedFilter,
				selectedPriority: this.selectedPriority,
			};

			TaskClient.getTasksByProject({ id: this.projectId, ...payload })
				.then((response) => {
					let data = response.data.tasks;
					this.tasks = data;
					this.hasData = data.length > 0;
					this.totalTasks = data.length;
				})
				.catch((error) => {
					console.error("Error fetching tasks:", error);
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
		formatDate(date) {
			return new Date(date).toLocaleDateString();
		},
		fetchComments(taskId) {
			TaskClient.fetchComments(taskId)
				.then((response) => {
					this.comments = response.data.comments;
				})
				.catch((error) => {
					console.error('Error fetching comments:', error);
					this.$toast.error('Failed to fetch comments.');
				});
		},
		addCommentInput() {
			this.showCommentInput = true;
			this.newComment = "";
		},
		submitComment() {
			this.errors = {};
			const createdBy = this.$auth.user().user.id;
			TaskClient.addComment({
				task_id: this.currentTask.id,
				comment: this.newComment,
				creator: createdBy,
			})
				.then((response) => {
					this.comments.push(response.data.comment);
					this.newComment = '';
					this.$toast.success('Comment added successfully.');
					this.showCommentInput = false;
					this.fetchComments(this.currentTask.id)
				})
				.catch((error) => {
					console.error('Error adding comment:', error);
					this.errors = error.response?.data.errors || {};
				});
		},
		editComment(index) {
			const comment = this.comments[index];
			const newText = prompt('Edit Comment:', comment.text);
			if (newText !== null && newText.trim() !== '') {
				TaskClient.editComment({comment_id: comment.id, comment: newText })
					.then(() => {
						this.fetchComments(this.currentTask.id)
						this.$toast.success('Comment updated successfully.');
					})
					.catch((error) => {
						console.error('Error updating comment:', error);
						this.$toast.error('Failed to update comment.');
					});
			}
		},
		deleteComment(index) {
			const comment = this.comments[index];
			if (confirm('Are you sure you want to delete this comment?')) {
				TaskClient.deleteComment(comment.id)
					.then(() => {
						this.fetchComments(this.currentTask.id)
						this.$toast.success('Comment deleted successfully.');
					})
					.catch((error) => {
						console.error('Error deleting comment:', error);
						this.$toast.error('Failed to delete comment.');
					});
			}
		},
	},
};
</script>

<style scoped>
.v-row {
  flex-wrap: wrap;
}
</style>
