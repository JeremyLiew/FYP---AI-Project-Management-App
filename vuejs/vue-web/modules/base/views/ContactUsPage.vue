<template>
	<v-sheet class="fill-height" style="background:linear-gradient(0deg,#242424, #000000);">
		<section style="padding-bottom:150px;" class="pt-10">
			<v-sheet color="transparent">
				<v-container>
					<div class="text-h4 font-weight-black pb-12">Contact Us</div>
					<div>
						<v-row>
							<v-col cols="6">First name</v-col>
							<v-col cols="6">Last name</v-col>
							<v-col cols="6">
								<v-text-field
									v-model="form.firstName"
									class="noBorder"
									solo flat outlined
									label="First name"
									:error-messages="errors.firstName"
									:hide-details="errors.firstName == null"
								></v-text-field>
							</v-col>
							<v-col cols="6">
								<v-text-field
									v-model="form.lastName"
									class="noBorder"
									solo flat outlined
									label="Last name"
									:error-messages="errors.lastName"
									:hide-details="errors.lastName == null"
								></v-text-field>
							</v-col>
						</v-row>
						<v-row>
							<v-col cols="12">Email Address</v-col>
							<v-col cols="12">
								<v-text-field
									v-model="form.email"
									class="noBorder"
									solo flat outlined
									label="Email Address"
									:error-messages="errors.email"
									:hide-details="errors.email == null"
								></v-text-field>
							</v-col>
						</v-row>
						<!-- <v-row>
							<v-col cols="12">Phone Number</v-col>
							<v-col cols="3" md="2" class="pr-0">
								<v-select
									v-model="form.phone_prefix"
									class="noBorder"
									solo flat outlined
									item-color="grey"
									placeholder="+60"
									:items="phone_prefix"
									:error-messages="errors.phone_prefix"
									:hide-details="errors.phone_prefix == null"
								></v-select>
							</v-col>
							<v-col cols="9" md="10" class="pl-0 ml-n1">
								<v-text-field
									v-model="form.phone"
									type="tel"
									class="noBorder"
									solo flat outlined
									label="Phone Number"
									:error-messages="errors.phone"
									:hide-details="errors.phone == null"
								></v-text-field>
							</v-col>
						</v-row> -->
						<v-row>
							<v-col cols="12">Subject</v-col>
							<v-col cols="12">
								<v-text-field
									v-model="form.subject"
									class="noBorder"
									solo flat outlined
									label="Subject"
									:error-messages="errors.subject"
									:hide-details="errors.subject == null"
								></v-text-field>
							</v-col>
						</v-row>
						<v-row>
							<v-col cols="12">Message</v-col>
							<v-col cols="12">
								<v-textarea
									v-model="form.message"
									class="border"
									solo outlined
									placeholder="Leave us a message..."
									:error-messages="errors.message"
									:hide-details="errors.message == null"
									background-color="transparent"
								></v-textarea>
							</v-col>
						</v-row>
						<v-row></v-row>
					</div>
					<div class="py-12">
						<v-btn
							outlined color="white" :loading="loading"
							tile @click="submitForm(form)"
						>
							Submit Now
						</v-btn>
					</div>
				</v-container>
				<v-container class="pa-16 pb-0 pa-md-0">
					<v-row>
						<v-col
							cols="12" md="6" order="1"
							order-md="0" class="d-flex pl-md-5"
						>
							<div class="align-self-center">
								<div class="text-h5 text-md-h3 font-weight-black pt-6 pb-16">
									Contact Info
								</div>
								<div class="pb-8">
									<v-row justify="space-between">
										<v-col cols="1"><v-icon :size="$vuetify.display.mdAndUp?'36':'25'" class="align-self-start" color="secondary">mdi-map-marker</v-icon></v-col>
										<v-col cols="10" md="11" class="pl-0 pl-md-3"><div class="d-flex ml-md-2 text-caption text-md-h6">Address 1-5 Residence,Park 2 Persiaran Jalil 5 Bukit Jalil 57000,KL</div></v-col>
									</v-row>
								</div>
								<div class="pb-8">
									<v-row justify="space-between">
										<v-col cols="1"><v-icon :size="$vuetify.display.mdAndUp?'36':'25'" color="secondary">mdi-email</v-icon></v-col>
										<v-col cols="10" md="11" class="pl-0 pl-md-3"><div class="ml-md-2 text-caption text-md-h6">cronusart.co@gmail.com</div></v-col>
									</v-row>
								</div>
								<div class="pb-0 pb-md-8">
									<v-row justify="space-between">
										<v-col cols="1"><v-icon :size="$vuetify.display.mdAndUp?'36':'25'" color="secondary">mdi-phone</v-icon></v-col>
										<v-col cols="10" md="11" class="pl-0 pl-md-3"><div class="ml-md-2 text-caption text-md-h6">017-7891551</div></v-col>
									</v-row>
								</div>
							</div>
						</v-col>
						<v-col
							cols="12" md="6" order="0"
							order-md="1"
						>
							<v-img :aspect-ratio="$vuetify.display.mdAndUp?'':'1'" src="/images/contactUs/contactUsContent1_v2.png"></v-img>
						</v-col>
					</v-row>
				</v-container>
			</v-sheet>
		</section>
	</v-sheet>
</template>


<script>
import BaseClient from '../client';

export default {
	components: {
	},
	data() {
		return {
			form: {
				// recaptcha: '',
			},
			// phone_prefix: [],
			loading: false,
			errors:{},
		};
	},
	created(){
		// this.phone_prefix = ['010','011','012','013','014','015','016','017','018','019'];
		// this.form.phone_prefix = '010';
	},
	mounted(){
		this.initRecaptcha();
	},
	methods: {
		async initRecaptcha(){
			await this.$recaptchaLoaded()
		},
		async submitForm(item){
			// Execute reCAPTCHA with action "submit".
			const token = await this.$recaptcha('submit')
			this.form.recaptcha = token

			this.submitData(item);
		},
		submitData(item){
			let payload = item
			this.loading = true;
			this.errors = {}
			BaseClient.submitContactUs(payload).then((res) => {
				this.$toast.success("Successfully submitted")
				this.loading = false;
				this.form.firstName = ''
				this.form.lastName = ''
				this.form.email = ''
				// this.form.phone = ''
				this.form.subject = ''
				this.form.message = ''
			}).catch((err) => {
				this.errors = this.errorHandler_(err, [
					'firstName',
					'lastName',
					'email',
					'message',
					// 'phone',
					'subject',
				])
			}).finally(()=>{
				this.loading = false;
			})
		}
	},
};
</script>

<style scoped>
.noBorder.v-text-field--outlined >>> fieldset {
	border-color: transparent!important;
	border-bottom-color: white!important;
	border-radius: 0 !important;
}

.v-select >>> .v-select__slot > .v-select__selections > .v-select__selection--comma {
   overflow: unset !important;
}

.noBorder.v-text-field--outlined >>> .v-input__slot {
    background: transparent!important;
}

.border.v-text-field--outlined >>> fieldset {
	border-color:white;
}

.v-input >>> .v-label{
	font-style: italic !important;
}

.v-text-field.v-text-field--solo .v-label {
	width: 100px !important;
}

</style>