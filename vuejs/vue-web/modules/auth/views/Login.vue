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
							<div v-if="isLockedOut" class="text-caption error--text font-italic">
								Locked out! Please wait {{ lockoutTimer }} seconds.
							</div>
							<v-text-field
								v-model="user.email"
								label="Email"
								placeholder="Email"
								prepend-icon="mdi-email"
								:error-messages="errors.email"
								:disabled="isLockedOut"
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
								:disabled="isLockedOut"
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
						<v-card-actions class="d-flex justify-center pa-0">
							<v-checkbox
								v-model="user.remember"
								label="Remember Me"
							></v-checkbox>
						</v-card-actions>
						<v-divider></v-divider>
						<v-card-actions class="pa-6 pt-3">
							<v-btn
								rounded block
								color="primary"
								class="white--text text-capitalize"
								:loading="is_loading"
								:disabled="isLockedOut"
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
				password : null,
				remember : null,
			},
			errors : {},
			error_else: null,
			is_loading : false,
			show_pass: false,
			isLockedOut: false,
			lockoutTimer: 0,
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
					remember : this.user.remember,
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
				if (err.response.status === 429) {
					const retryAfter = parseInt(err.response.headers["retry-after"], 10) || 60;
					this.lockoutTimer = retryAfter;
					this.isLockedOut = true;
					this.$toast.error(
						`Too many login attempts. Please try again in ${retryAfter} seconds.`
					);
					this.startLockoutTimer();
				} else if (err.response.status === 401) {
					this.$toast.error("Invalid credentials.");
				} else {
					this.errors = err.response.data.errors || {};
				}
			}).finally(()=>{
				this.is_loading = false
			});
		},
		startLockoutTimer() {
			const interval = setInterval(() => {
				if (this.lockoutTimer > 0) {
					this.lockoutTimer -= 1;
				} else {
					this.isLockedOut = false;
					clearInterval(interval);
					this.$toast.success("You can now attempt to login.");
				}
			}, 1000);
		},
	}
}
</script>