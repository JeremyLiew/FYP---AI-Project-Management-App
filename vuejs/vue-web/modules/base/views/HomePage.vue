<template>
	<v-container>
		<h2 class="text-h4 text-center mb-4">Welcome to Project AI</h2>
		<h6 class="text-center mb-0">Chat with our AI </h6>
		<div class="text-center"><small style="color: #666;">Powered by ChatGPT</small></div>
		<v-divider class="mb-4"></v-divider>

		<!-- Chat Section -->
		<v-row class="mb-4">
			<v-col cols="12">
				<v-textarea
					v-model="message"
					label="Ask something..."
					outlined
					dense
					append-inner-icon="mdi-send"
					@click:append-inner="sendMessage"
				></v-textarea>
			</v-col>
		</v-row>

		<!-- Response Section -->
		<v-row v-if="response" class="mb-4">
			<v-col cols="12">
				<v-card outlined>
					<v-card-title class="text-h6">AI Response</v-card-title>
					<v-card-text class="text-body-2">{{ response }}</v-card-text>
				</v-card>
			</v-col>
		</v-row>

		<!-- Feedback Section -->
		<v-row>
			<v-col cols="12" class="text-end">
				<v-btn
					:loading="isLoading" color="white" depressed
					@click="generateSummaryFeedback"
				>
					Generate AI Feedback
				</v-btn>
				<div class="mt-2 text-end">
					<small style="color: #666;">
						Note: This feature currently evaluates and summarizes insights on your tasks and projects.
					</small>
				</div>
			</v-col>
			<v-col cols="12">
				<v-card outlined>
					<v-card-title class="text-h6">Previous Feedback</v-card-title>
					<v-divider></v-divider>
					<v-card-text>
						<v-list dense>
							<v-list-item
								v-for="feedback in feedbacks"
								:key="feedback.id"
								class="feedback-item"
							>
								<v-list-item-content>
									<v-list-item-title class="feedback-title">
										{{ feedback.feedback }}
									</v-list-item-title>
									<v-list-item-subtitle class="feedback-meta">
										Model: {{ feedback.ai_model }}, Rating: {{ feedback.rating }}
									</v-list-item-subtitle>
								</v-list-item-content>
							</v-list-item>
						</v-list>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import BaseClient from "../client";

export default {
	data() {
		return {
			message: "",
			response: "",
			feedbacks: [],
			isLoading: false,
		};
	},
	mounted() {
		this.fetchFeedbacks();
	},
	methods: {
		sendMessage() {
			BaseClient.getGptMessage({ message: this.message })
				.then((res) => {
					this.response = res.data.message;
					this.fetchFeedbacks();
				})
				.catch((error) => {
					console.error("Error sending message:", error);
				});
		},
		generateSummaryFeedback() {
			this.isLoading = true;
			BaseClient.generateSummaryFeedback()
				.then(() => {
					this.isLoading = false
					this.fetchFeedbacks();
				})
				.catch((error) => {
					console.error("Error generating AI feedback:", error);
				}).finally(() => {
					this.isLoading = false
				})
		},
		fetchFeedbacks() {
			BaseClient.getFeedbacks()
				.then((res) => {
					this.feedbacks = res.data.feedbacks;
				})
				.catch((error) => {
					console.error("Error fetching feedbacks:", error);
				});
		},
	},
};
</script>

<style scoped>
.text-h4 {
	font-weight: 600;
}

.v-card {
	margin-bottom: 20px;
}

.feedback-item {
	margin-bottom: 16px;
	padding: 12px;
	border: 1px solid #dcdcdc;
	border-radius: 8px;
}

.feedback-title {
	white-space: normal;
	word-wrap: break-word;
	font-size: 16px;
	margin-bottom: 4px;
}

.feedback-meta {
	font-size: 14px;
	color: #666;
}

.v-card-text {
	padding: 16px;
}
</style>
