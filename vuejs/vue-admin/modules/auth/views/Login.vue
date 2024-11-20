<template>
	<div class="fill-height">
		<div class="rounded-banner secondary elevation-1" :style="$vuetify.breakpoint.mdAndUp ? 'height: 55vh' : 'height: 55vh'"></div>
		<v-container class="text-center justify-center fill-height">
			<v-row
				justify="center" dense
				class="px-3" style="z-index: 1;"
			>
				<!-- <v-col cols="12" md="8">
					<div>
						<img src="/images/new-logo-without-title.png" width="100px">
					</div>
				</v-col> -->
				<v-col cols="12" md="8">
					<v-card width="100%" class="radius-05">
						<v-card-title
							class="font-weight-bold justify-center primary white--text text-md-body-1"
						>
							<span>{{ $config.name }}</span>
						</v-card-title>
						<v-divider></v-divider>
						<v-card-text class="pa-6">
							<v-text-field
								v-model="user.email"
								label="Email"
								placeholder="Email"
								prepend-icon="mdi-email"
								:error-messages="errors.email"
								@keyup.enter="$refs['password'].focus()"
							></v-text-field>
							<v-text-field
								ref="password"
								v-model="user.password"
								:type="show_pass ? 'text' : 'password' "
								label="Password"
								placeholder="Enter Password"
								prepend-icon="mdi-lock"
								:error-messages="errors.password"
								@keyup.enter="login()"
							>
								<template #append>
									<v-btn small icon @click="show_pass = !show_pass">
										<v-icon small>{{ show_pass ? 'mdi-eye-off' : 'mdi-eye' }}</v-icon>
									</v-btn>
								</template>
							</v-text-field>
						</v-card-text>
						<div class="text-caption error--text font-italic px-10">
							{{ error_else }}
						</div>
						<v-card-actions class="pa-6 pt-3">
							<v-btn
								rounded block
								color="primary"
								class="white--text text-capitalize"
								:loading="is_loading"
								@click="login()"
							>
								Login
							</v-btn>
						</v-card-actions>
					</v-card>
					<div class="py-6">
						<router-link to="/forgot-password" class="text-body-2">Forgot password?</router-link>
					</div>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>
import { errorHandlerMixin } from "@web/mixins/ErrorHandlerMixin"
export default {
	mixins: [
		errorHandlerMixin
	],
	data() {
		return {
			user:{
				email : null,
				password : null
			},
			errors : {},
			error_else: null,
			is_loading : false,
			show_pass: false,
		}
	},
	created(){
		// check is logined
		if(this.$auth.check()){
			this.redirectUser()
		}
	},
	methods:{
		login(){
			this.errors = {}
			this.error_else = null
			this.is_loading = true;

			// await this.axios({ url: '/sanctum/csrf-cookie', baseURL: '/' }) // required for cross-domain

			this.$auth.login({
				data: {
					email: this.user.email,
					password: this.user.password,
					device_name: 'web',
				},
				staySignedIn: true,
				fetchUser: true,
			}).then((res)=>{
				this.redirectUser(res.data.data)
			}).catch((err)=>{
				this.errors = this.errorHandler_(err, [
					"email",
					"password"
				])
			}).finally(()=>{
				this.is_loading = false
			});
		},
		redirectUser(){
			this.$router.push({
				name: "home-page"
			})
		},
	}
}
</script>

<style scoped>
.rounded-banner {
    position: fixed;
    width: 100%;
    border-bottom-right-radius: 50% 35px;
    border-bottom-left-radius: 50% 35px;
}
</style>