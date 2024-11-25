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
					label="Filter by"
					placeholder="Select a filter"
					clearable
					dense
					outlined
				/>
			</v-col>
		</v-row>

		<section v-if="hasData" style="height:100%;">
			<template v-if="modelLoading">
				<v-skeleton-loader type="article"></v-skeleton-loader>
			</template>
			<template v-else>
				<!-- Task Listings -->
				<v-list dense>
					<v-list-item
						v-for="task in tasks"
						:key="task.id"
					>
						<v-list-item-content>
							<v-list-item-title>{{ task.name }}</v-list-item-title>
							<v-list-item-subtitle>Status: {{ task.status }}</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
				</v-list>
				<!-- Pagination -->
				<v-pagination
					v-model="currentPage"
					:length="paginationLength"
					class="mt-4 pl-0"
				/>
				<div class="text-end mt-2">Total Tasks: {{ totalTasks }}</div>
			</template>
		</section>
		<section v-else style="height:100%;">
			<!-- Show No Projects Image if No Projects Available -->
			<v-row class="justify-center">
				<v-col cols="12" class="text-center">
					<img src="/images/no-product-available.png" alt="No projects available" class="my-4" />
					<p>No tasks available.</p>
				</v-col>
			</v-row>
		</section>
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
			currentPage: 1,
			itemsPerPage: 10,
			searchQuery: '',
			selectedFilter: null,
			statusOptions: ['All', 'Pending', 'Ongoing', 'Completed'],
			totalTasks: 0,
		};
	},
	watch: {
		searchQuery: "fetchTasks",
		selectedFilter: "fetchTasks",
		currentPage: "fetchTasks",
	},
	mounted() {
		this.fetchTasks();
	},
	methods: {
		fetchTasks() {
			this.modelLoading = true;
			this.hasData = true;
			let payload = {
				searchQuery: this.searchQuery,
				selectedFilter: this.selectedFilter,
				page: this.currentPage,
				itemsPerPage: this.itemsPerPage,
			};

			TaskClient.getTasksByProject({ id: this.projectId, ...payload })
				.then((response) => {
					console.log(response)
					let {data,...pagination} = response.data.tasks;
					this.tasks = data;
					this.hasData = data.length > 0;
					this.paginationLength = Math.ceil(pagination.total/pagination.per_page);
					this.paginator = pagination
					this.totalTasks = response.data.total;
				})
				.catch((error) => {
					console.error("Error fetching tasks:", error);
					this.hasData = false;
				}).finally(()=>{
					this.modelLoading = false
				});
		},
	},
};
</script>
