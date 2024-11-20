<template>
	<v-container fluid fill-height class="justify-center primary">
		<v-container>
			<v-row dense justify="center">
				<v-col cols="12" class="text-center secondary--text">
					<div class="text-h2 text-md-h1" @dblclick="toLogin">{{ $config.name }}</div>
					<div class="text-body-1 pt-3 pb-6">Landing Page</div>
				</v-col>
				<v-col cols="auto">
					<v-btn :to="{ name: 'login' }" dark>To Login Page</v-btn>
				</v-col>
				<v-col cols="auto">
					<v-btn :to="{ name: 'home-page' }" dark>To Home Page</v-btn>
				</v-col>
			</v-row>
		</v-container>
	</v-container>
</template>

<script>
export default {
	data() {
		return {};
	},
	methods: {
		toLogin() {
			this.$router.push({ name: "login" });
		},
	},
};
</script>