<template>
	<div class="fill-height">
		<v-container class="text-center justify-center fill-height">
			<v-row justify="center" dense>
				<v-col cols="12" md="8">
					<v-card width="100%" class="pt-5">
						<v-card-title
							class="text-capitalize primary white--text"
							style="letter-spacing: 1px;"
						>
							Setup Password
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
								<div class="title font-weight-bold">Your password has been setup.</div>
								<div class="font-italic">( You may close this window. )</div>
							</div>
							<!-- form -->
							<template v-else>
								<div class="text-left pb-1 pt-2">
									<div class="text-body-2 text-capitalize pb-1">Please setup your password before proceed</div>
								</div>
								<div>
									<v-row dense class="form-gap-2">
										<v-col cols="12">
											<v-text-field
												v-model="formData.password"
												type="password"
												label="Password"
												outlined
												dense
												:error-messages="errors.password"
											/>
										</v-col>
										<v-col cols="12">
											<v-text-field
												v-model="formData.password_confirmation"
												type="password"
												label="Re-type Password"
												outlined
												dense
												:error-messages="errors.password_confirmation"
											/>
										</v-col>
									</v-row>
								</div>
								<div class="text-right">
									<v-btn
										color="transparent"
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
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>

import AuthClient from '../client'
export default {

	data() {
		return {
			status : '',
			formData:{},
			submit_loading : false,
			errors: {},
		}
	},
	methods:{
		submitForm() {
			this.errors = {}
			if(this.formData.password !== this.formData.password_confirmation){
				this.errors.password = ["Password not match"]
			}else{
				this.resetPassword()
			}
		},
		resetPassword(){
			this.submit_loading = true
			let payload = {
				password : this.formData.password,
				password_confirmation : this.formData.password_confirmation,
				token: this.$route.params.token,
				email: this.$route.query.email,
			}
			AuthClient.resetPassword(payload).then(()=>{
				this.status = "success"
			}).catch(err=>{
			}).finally(()=>{
				this.submit_loading = false
			});
		},
	}
}
</script>