<template>
	<v-container>
		<!-- My Account title and Logout button -->
		<v-row class="mb-4">
			<v-col class="text-h6" cols="6">
				My Account
				<v-row>
					<!-- Account Details -->
					<v-col cols="6">
						<div>
							<v-divider></v-divider>
							<v-row>
								<v-col>
									<v-row class="text-subtitle-1">
										<v-col cols="12" md="auto"><strong>Email :</strong></v-col>
										<v-col cols="12" md="auto">{{ user.email }}</v-col>
									</v-row>
									<!-- Add more details like phone number, address, etc. -->
								</v-col>
							</v-row>
						</div>
					</v-col>
				</v-row>
			</v-col>
			<v-col class="d-flex justify-end" cols="6">
				<v-btn :loading="is_loading" color="error" @click="logout">Logout</v-btn>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import GeneralClient from '../client';

export default {
	data() {
		return {
			user: {
				email: 'example@email.com',
			},
			is_loading : false,
			modelLoading: true,
		};
	},
	created(){
		this.user = this.$auth.user().user;
	},
	methods: {
		async logout(){
			this.is_loading = true;
			this.$auth.logout({
				makeRequest: true,
				data: {},
				redirect: false,
			}).finally((res)=>{
				this.is_loading = false
				this.$toast.success("Logged out successfully")

				setTimeout(() => {
					this.$router.push({ name: 'login-page' });
				}, 500);
			});
		},
	},
};
</script>

