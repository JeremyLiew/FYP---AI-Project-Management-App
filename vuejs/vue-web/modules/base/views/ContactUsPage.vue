<template>
	<v-sheet class="fill-height">
		<section>
			<v-container>
				<v-row>
					<!-- Contact Form Section -->
					<v-col cols="12" md="6">
						<v-sheet
							elevation="2" class="pa-6"
							style="border-radius: 12px;"
						>
							<div class="text-h5 font-weight-bold pb-4">Get In Touch</div>
							<v-form ref="contactForm">
								<v-row>
									<v-col cols="12" md="6">
										<v-text-field
											v-model="form.firstName"
											solo
											outlined
											label="First Name"
											:rules="[rules.required]"
										></v-text-field>
									</v-col>
									<v-col cols="12" md="6">
										<v-text-field
											v-model="form.lastName"
											solo
											outlined
											label="Last Name"
											:rules="[rules.required]"
										></v-text-field>
									</v-col>
									<v-col cols="12">
										<v-text-field
											v-model="form.email"
											solo
											outlined
											label="Email Address"
											:rules="[rules.required, rules.email]"
										></v-text-field>
									</v-col>
									<v-col cols="12">
										<v-text-field
											v-model="form.subject"
											solo
											outlined
											label="Subject"
										></v-text-field>
									</v-col>
									<v-col cols="12">
										<v-textarea
											v-model="form.message"
											solo
											outlined
											label="Your Message"
										></v-textarea>
									</v-col>
									<v-col cols="12" class="text-center">
										<v-btn
											:loading="loading"
											depressed
											@click="submitForm"
										>
											Submit Now
										</v-btn>
									</v-col>
								</v-row>
							</v-form>
						</v-sheet>
					</v-col>

					<!-- Contact Info Section -->
					<v-col
						cols="12" md="6" class="d-flex flex-column justify-center align-center text-center"
					>
						<!-- <div>
							<v-img
								:width="300"
								aspect-ratio="16/9"
								cover
								src="images/projectailogo.png"
							></v-img>
						</div> -->
						<div class="text-h5 font-weight-bold pb-4">Contact Info</div>
						<div class="d-flex align-center mb-4">
							<v-icon class="mr-3">mdi-map-marker</v-icon>
							<span>Address 1-5 Residence, Park 2 Persiaran Jalil 5, Bukit Jalil, 57000, KL</span>
						</div>
						<div class="d-flex align-center mb-4">
							<v-icon class="mr-3">mdi-email</v-icon>
							<span>projectai.co@gmail.com</span>
						</div>
						<div class="d-flex align-center">
							<v-icon class="mr-3">mdi-phone</v-icon>
							<span>017-7891551</span>
						</div>
					</v-col>
				</v-row>
			</v-container>
		</section>
	</v-sheet>
</template>

<script>
import GeneralClient from "../../_general/client"

export default {
	data() {
		return {
			form: {
				firstName: "",
				lastName: "",
				email: "",
				subject: "",
				message: "",
			},
			loading: false,
			rules: {
				required: (value) => !!value || "This field is required",
				email: (value) => {
					const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
					return pattern.test(value) || "Invalid email";
				},
			},
		};
	},
	methods: {
		submitForm() {
			this.loading = true;

			GeneralClient.submitContactUs(this.form)
				.then(() => {
					this.$toast.success("Form submitted successfully!");
					this.$refs.contactForm.reset();
				})
				.catch((err) => {
					this.$toast.error(err.response.data.message || "An error occurred!");
				})
				.finally(() => {
					this.loading = false;
				});
		},
	},
};
</script>

