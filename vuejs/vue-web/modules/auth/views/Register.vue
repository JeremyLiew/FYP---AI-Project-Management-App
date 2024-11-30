<template>
	<div class="fill-height">
		<div class="d-flex white--text justify-center font-family-secondary" :style="$vuetify.display.mdAndUp?'font-size:150px;':'font-size:80px;'">
			welcome
		</div>
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
							<span>Create Account</span>
						</v-card-title>
						<v-divider></v-divider>
						<v-card-text class="pa-6">
							<v-text-field
								v-model="user.name"
								label="Name"
								placeholder="Name"
								prepend-icon="mdi-account"
								:error-messages="errors.name"
								@keyup.enter="$refs['email'].focus()"
							></v-text-field>
							<v-text-field
								ref="email"
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
								@keyup.enter="$refs['confirmPassword'].focus()"
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
							<v-text-field
								ref="confirmPassword"
								v-model="user.confirmPassword"
								:type="show_pass ? 'text' : 'password' "
								label="Confirm Password"
								placeholder="Confirm Password"
								prepend-icon="mdi-lock"
								:error-messages="errors.confirmPassword"
								@keyup.enter="register()"
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
							<div class="d-flex justify-space-between"><div>Password Strength</div><div class="text-caption">{{ passwordStrengthText }}</div></div>
							<v-progress-linear
								:model-value="passwordStrength"
								:color="passwordStrengthColor"
								bg-color="transparent"
								class="px-2 py-4"
							>
							</v-progress-linear>
							<v-divider></v-divider>
							<!-- <div class="pb-4">Use 6 or more characters with a mix of letters, numbers & symbols</div> -->
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
								@click="register()"
							>
								Register
							</v-btn>
						</v-card-actions>
					</v-card>
					<div class="py-6">
						Already have an account? <router-link to="/login" class="text-body-2">Login here</router-link>
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
			user: {
				email: null,
				password: null,
				confirmPassword: null,
			},
			errors: {},
			error_else: null,
			is_loading: false,
			show_pass: false,
			passwordStrength: 0,
		};
	},
	computed: {
		passwordStrengthColor() {
			if (this.passwordStrength === 20) return 'red';
			if (this.passwordStrength === 40) return 'red';
			if (this.passwordStrength === 60) return 'orange';
			if (this.passwordStrength === 80) return 'light-green';
			if (this.passwordStrength === 100) return 'green';
			return 'grey';
		},
		passwordStrengthText() {
			if (this.passwordStrength === 20) return 'Very Weak';
			if (this.passwordStrength === 40) return 'Weak';
			if (this.passwordStrength === 60) return 'Normal';
			if (this.passwordStrength === 80) return 'Good';
			if (this.passwordStrength === 100) return 'Great';
			return 'Poor';
		},
	},
	watch: {
		'user.password': function (newPassword) {
			this.checkPasswordStrength(newPassword || "");
		},
	},
	methods: {
		checkPasswordStrength(password) {
			const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;
			let strength = 0;

			if (password.length >= 6) strength += 20; // Length check
			if (/[A-Z]/.test(password)) strength += 20; // Uppercase check
			if (/[a-z]/.test(password)) strength += 20; // Lowercase check
			if (/\d/.test(password)) strength += 20; // Number check
			if (/[\W_]/.test(password)) strength += 20; // Special character check

			if (!regex.test(password)) {
				this.errors.password = [
					"Password must have at least 6 characters, include an uppercase letter, a lowercase letter, a number, and a special character.",
				];
				this.passwordStrength = 0; // Reset if invalid
			} else {
				this.errors.password = null;
			}

			this.passwordStrength = strength;
		},
		register() {
			this.errors = {};
			this.error_else = null;
			this.is_loading = true;

			if (this.user.password !== this.user.confirmPassword) {
				this.errors.confirmPassword = ["Password and Confirm Password do not match"];
				this.is_loading = false;
				return;
			}

			this.$auth
				.register({
					data: {
						name: this.user.name,
						email: this.user.email,
						password: this.user.password,
						password_confirmation: this.user.confirmPassword,
					},
					fetchUser: true,
					redirect: false,
				})
				.then((res) => {
					this.$toast.success("Account successfully created");

					setTimeout(() => {
						this.$router.push({ name: 'login-page' });
					}, 500);
				})
				.catch((err) => {
					this.errors = err.response.data.errors
				})
				.finally(() => {
					this.is_loading = false;
				});
		},
		redirectUser() {
			this.$router.push({
				name: "home-page",
			});
		},
	},
};
</script>