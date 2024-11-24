<template>
	<div class="fill-height">
		<div class="white--text text-center font-family-secondary" :style="$vuetify.display.mdAndUp?'font-size:150px;':'font-size:80px;'">welcome</div>
		<v-container class="text-center justify-center">
			<v-row
				justify="center" dense
				class="px-3" style="z-index: 1;"
			>
				<v-col cols="12" md="8">
					<v-card width="100%" class="radius-05 pt-5">
						<v-card-title
							class="font-weight-bold justify-center primary white--text text-md-body-1"
						>
							<span>Login</span>
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
									<v-btn
										small icon
										@click="show_pass = !show_pass"
									>
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
					<div class="pt-6 pb-3">
						<router-link to="/forgot-password" class="text-body-2">Forgot password?</router-link>
					</div>
					<div class="py-3">
						Not a member yet? <router-link to="/register" class="text-body-2">Sign up now</router-link>
					</div>
					<div class="py-3">
						<router-link to="/" class="text-body-2">Continue as guest</router-link>
					</div>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>

export default {
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
		// if(this.$auth.check()){
		// 	this.redirectUser()
		// }
	},
	methods:{
		async login(){
			this.errors = {}
			this.error_else = null
			this.is_loading = true;

			this.$auth.login({
				data: {
					email : this.user.email,
					password : this.user.password,
				},
				staySignedIn: true,
				fetchUser: true,
				redirect: false,
			}).then((res)=>{
				this.$toast.success("Logged in successfully")

				setTimeout(() => {
					this.$router.push({ name: 'home-page' });
				}, 500);
			}).catch((err)=>{
				this.user.email = ''
				this.user.password = ''
				if(err.response.status == '404'){
					this.$toast.error("Credentials not match")
				}
				else{
					this.errors = err.response.data.errors
				}
			}).finally(()=>{
				this.is_loading = false
			});
		},
	}
}
</script>