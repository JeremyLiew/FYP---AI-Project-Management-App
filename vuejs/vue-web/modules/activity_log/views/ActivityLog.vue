<template>
	<v-container>
		<template v-if="isAuthorized">
			<!-- Page Title -->
			<h2>Activity Log</h2>
			<v-divider></v-divider>
			<!-- Filters -->
			<v-row class="d-flex align-center mb-4">
				<v-col cols="12" md="4">
					<v-select
						v-model="logLevelFilter"
						:items="logLevels"
						label="Filter by Log Level"
						clearable
						outlined
						dense
					></v-select>
				</v-col>
				<v-col cols="12" md="4">
					<v-select
						v-model="modelTypeFilter"
						:items="modelTypes"
						label="Filter by Model Type"
						clearable
						outlined
						dense
					></v-select>
				</v-col>
				<v-col cols="12" md="4">
					<v-select
						v-model="userFilter"
						:items="users"
						label="Filter by User"
						clearable
						outlined
						dense
						item-title="name"
						item-value="id"
					>
						<template #prepend-item>
							<v-list-item ripple @click="userFilter = 'All'">
								<div>
									<v-list-item-title>All Users</v-list-item-title>
								</div>
							</v-list-item>
						</template>
					</v-select>
				</v-col>
			</v-row>
			<section v-if="hasData">
				<template v-if="modelLoading">
					<v-skeleton-loader type="article"></v-skeleton-loader>
				</template>
				<template v-else>
					<!-- Logs Table with Horizontal Scroll -->
					<div class="table-wrapper">
						<v-table class="table-scroll">
							<thead>
								<tr>
									<th>Action</th>
									<th>User</th>
									<th>Log Level</th>
									<th>Model Type</th>
									<th>Changes</th>
									<th>IP Address</th>
									<th>Timestamp</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="log in activityLogs" :key="log.id">
									<td class="text-wrap py-2">{{ log.action }}</td>
									<td>{{ log.user ? log.user.name : 'No user' }}</td>
									<td>
										<v-chip
											:color="getLogLevelColor(log.log_level)" dark outlined
											small
										>
											{{ log.log_level }}
										</v-chip>
									</td>
									<td>
										<v-chip
											:color="getModelTypeColor(log.model_type)" dark outlined
											small
										>
											{{ log.model_type }}
										</v-chip>
									</td>
									<td><pre>{{ formatChanges(log.changes) }}</pre></td>
									<td>{{ log.ip_address }}</td>
									<td>{{ formatDate(log.created_at) }}</td>
								</tr>
							</tbody>
						</v-table>
					</div>
					<!-- Pagination -->
					<v-pagination
						v-model="currentPage"
						:length="paginationLength"
						class="mt-4 pl-0"
					></v-pagination>
					<!-- Log Count -->
					<div class="text-end mt-2">Total Logs: {{ totalLogs }}</div>
				</template>
			</section>
			<section v-else>
				<!-- Show No Logs Image -->
				<v-row class="justify-center">
					<v-col cols="12" class="text-center">
						<img src="/images/no-product-available.png" alt="No logs available" class="my-4" />
						<p>No logs available.</p>
					</v-col>
				</v-row>
			</section>
		</template>
		<template v-else>
			<p>You do not have permission to view this page.</p>
		</template>
	</v-container>
</template>

<script>
import LogClient from "../client";

export default {
	data() {
		return {
			searchQuery: "",
			logLevelFilter: "All",
			modelTypeFilter: "All",
			userFilter: "All",
			currentPage: 1,
			itemsPerPage: 10,
			logLevels: ["All", "info", "debug", "error", "warning"],
			modelTypes: ["All", "Project", "Task"],
			users: [],
			activityLogs: [],
			paginationLength: 0,
			totalLogs: 0,
			hasData: true,
			modelLoading: true,
		};
	},
	computed: {
		isAuthorized() {
			const userRole = localStorage.getItem('userRole');
			return userRole === 'Admin';
		}
	},
	watch: {
		logLevelFilter: "fetchLogs",
		modelTypeFilter: "fetchLogs",
		userFilter: "fetchLogs",
		currentPage: "fetchLogs",
	},
	mounted() {
		this.fetchUsers();
		this.fetchLogs();
	},
	methods: {
		fetchUsers() {
			LogClient.getAllUsers()
				.then((response) => {
					this.users = response.data;
				})
				.catch((error) => {
					console.error("Error fetching users:", error);
				});
		},
		fetchLogs() {
			this.modelLoading = true;
			this.hasData = true;
			const payload = {
				logLevelFilter: this.logLevelFilter,
				modelTypeFilter: this.modelTypeFilter,
				userFilter: this.userFilter,
				page: this.currentPage,
				itemsPerPage: this.itemsPerPage,
			};

			LogClient.getActivityLogs(payload)
				.then((response) => {
					let { data, ...pagination } = response.data.logs;
					this.activityLogs = data;
					this.paginationLength = Math.ceil(pagination.total / pagination.per_page);
					this.totalLogs = response.data.total;
					this.hasData = this.activityLogs.length > 0;
				})
				.catch((error) => {
					console.error("Error fetching logs:", error);
					this.hasData = false;
				})
				.finally(() => {
					this.modelLoading = false;
				});
		},
		getLogLevelColor(logLevel) {
			const colors = {
				info: "blue",
				debug: "green",
				error: "red",
				warning: "yellow",
			};
			return colors[logLevel] || "grey";
		},
		getModelTypeColor(modelType) {
			const colors = {
				Project: "purple",
				Task: "teal",
			};
			return colors[modelType] || "grey";
		},
		formatDate(date) {
			return new Date(date).toLocaleDateString();
		},
		changePage(page) {
			this.currentPage = page;
		},
		formatChanges(changes) {
			try {
				const json = JSON.parse(changes);
				return JSON.stringify(json, null, 2);
			} catch (e) {
				return changes;
			}
		},
	},
};
</script>

  <style scoped>
  .table-wrapper {
	overflow-x: auto;
	-webkit-overflow-scrolling: touch;
  }

  .table-scroll {
	width: 100%;
	white-space: nowrap;
  }

  .v-list-item {
	transition: box-shadow 0.2s;
  }

  .v-list-item:hover {
	box-shadow: 0px 4px 12px #323232;
  }

  .v-list-item:not(:hover) {
	background-color: transparent !important;
  }

  .pagination {
	display: flex;
	justify-content: center;
	align-items: center;
	gap: 10px;
  }

  .pagination button {
	background-color: #f0f0f0;
	border: none;
	padding: 5px 10px;
	border-radius: 5px;
	cursor: pointer;
  }

  .pagination button.active {
	background-color: #3f51b5;
	color: white;
  }

  .pagination button:disabled {
	background-color: #e0e0e0;
	cursor: not-allowed;
  }

  .pagination button:hover:not(:disabled) {
	background-color: #c8c8c8;
  }

  .search-input,
  .filter-select {
	width: 100%;
  }

  .v-chip {
	margin: 2px;
  }

  </style>
