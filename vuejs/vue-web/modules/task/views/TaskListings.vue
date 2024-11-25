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
							<v-list-item-title>{{ task.name }}</v-list-item-title>
							<v-list-item-subtitle>{{ task.description }}</v-list-item-subtitle>
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
					<v-btn :loading="isLoading" color="primary" @click="isEdit ? saveTask('edit') : saveTask('create')">Add</v-btn>
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
		infoTask(taskId){
			// this.$router.push({ name: "task-info-page", params: { id: taskId } });
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
	},
};
</script>
