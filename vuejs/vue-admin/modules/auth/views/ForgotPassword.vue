<template>
	<v-container class="text-center justify-center fill-height">
		<v-row justify="center" dense>
			<v-col cols="12" md="8">
				<v-card width="100%">
					<v-card-title
						class="text-capitalize primary white--text"
						style="letter-spacing: 1px;"
					>
						Forgot Password
					</v-card-title>
					<v-divider></v-divider>
					<v-card-text class="px-10 py-4">
						<!-- loading -->
						<v-skeleton-loader v-if="status == 'loading' " type="list-item-three-line"></v-skeleton-loader>
						<!-- success -->
						<div v-else-if="status == 'success' " class="pa-12">
							<div class="pb-6">
								<v-icon size="80" color="info">mdi-check-circle</v-icon>
							</div>
							<div class="title font-weight-bold">We have sent you a password recovery email.</div>
							<div v-if="email" class="font-italic">( {{ email }} )</div>
						</div>
						<!-- form -->
						<template v-else>
							<div class="text-left pb-1 pt-2">
								<div class="text-body-2 text-capitalize pb-1">Enter your email to reset your password :</div>
							</div>
							<div>
								<v-row dense class="form-gap-2">
									<v-col cols="12">
										<v-text-field
											v-model="form.email"
											label="Email"
											outlined
											dense
											:error-messages="errors.email"
											@keyup.enter="submitForm()"
										/>
									</v-col>
								</v-row>
							</div>
							<div class="text-right">
								<v-btn
									class="primary"
									type="submit"
									:loading="submit_loading"
									@click="submitForm()"
								>
									Submit
								</v-btn>
							</div>
						</template>
					</v-card-text>
				</v-card>
				<div class="py-6">
					<router-link to="/" class="text-body-2">Back to login page</router-link>
				</div>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { errorHandlerMixin } from "@web/mixins/ErrorHandlerMixin"
import AuthClient from '../client'
export default {
	mixins: [
		errorHandlerMixin
	],
	data() {
		return {
			email: null,
			errors: {},
			form:{},
			submit_loading : false,
			status : '',
		}
	},
	methods:{
		submitForm() {
			this.forgotPassword()
		},
		forgotPassword(){
			this.submit_loading = true
			this.errors = {}
			let payload = {
				email: this.form.email,
			}
			AuthClient.forgotPassword(payload).then((res)=>{
				this.status = "success"
				this.email = res.data.data.email
			}).catch(err=>{
				this.errors = this.errorHandler_(err, ['email'])
			}).finally(()=>{
				this.submit_loading = false
			});
		},
	}
}
</script>