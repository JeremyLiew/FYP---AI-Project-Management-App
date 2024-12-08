<template>
	<div>
	  <h2>Chat with GPT</h2>
	  <textarea v-model="message" placeholder="Ask something..."></textarea>
	  <button @click="sendMessage">Send</button>
  
	  <div v-if="response">
		<h3>GPT Response:</h3>
		<p>{{ response }}</p>
	  </div>
	</div>
  </template>
  
  <script>
  import BaseClient from "../client";
  
  export default {
	data() {
	  return {
		message: '',
		response: '',
	  };
	},
	methods: {
	  async sendMessage() {
		try {
		  // Use BaseClient to send the message to Laravel API
		  const res = await BaseClient.getGptMessage({ message: this.message });
		  this.response = res.message; // Assuming the response contains the message in 'message' field
		} catch (error) {
		  console.error("Error sending message:", error);
		}
	  },
	},
  };
  </script>
  