<template>
	<v-container
		fluid class="d-flex align-center justify-center bg-default" fill-height
		style="height:100%;"
	>
		<v-row>
			<v-col cols="12" class="text-center">
				<v-icon color="error" size="100">mdi-alert-circle-outline</v-icon>
				<h1 class="headline mb-4">Payment Canceled</h1>
				<p class="subheading mb-4">
					Unfortunately, your payment was canceled. This could be due to a technical issue.<br>
					If you encounter any problems or have questions, please contact our support team.
				</p>
				<router-link to="/" class="btn btn-secondary">Go back to Home</router-link>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	name: "CanceledPayment",
};
</script>

  <style scoped>
	/* Add any additional styling or adjustments here */
	.align-center {
	display: flex;
	align-items: center;
	}

	.justify-center {
	justify-content: center;
	}

	.btn-secondary {
	background-color: #f44336; /* Red */
	color: white;
	padding: 10px 20px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	cursor: pointer;
	border-radius: 5px;
	}

	.btn-secondary:hover {
	background-color: #d32f2f; /* Slightly darker red on hover */
	}
  </style>