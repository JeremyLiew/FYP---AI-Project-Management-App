<template>
	<v-container
		fluid class="d-flex align-center justify-center bg-default" fill-height
		style="height:100%;"
	>
		<v-row>
			<v-col cols="12" class="text-center">
				<v-icon color="success" size="100">mdi-check-circle-outline</v-icon>
				<h1 class="headline mb-4">Payment Successful!</h1>
				<p class="subheading mb-4">
					Thank you for your purchase. A receipt has been sent to your email.
				</p>
				<router-link to="/" class="btn btn-primary">Go back to Home</router-link>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	name: "SuccessPayment",
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

	.btn-primary {
	background-color: #4caf50; /* Green */
	color: white;
	padding: 10px 20px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	cursor: pointer;
	border-radius: 5px;
	}

	.btn-primary:hover {
	background-color: #45a049; /* Slightly darker green on hover */
	}
  </style>