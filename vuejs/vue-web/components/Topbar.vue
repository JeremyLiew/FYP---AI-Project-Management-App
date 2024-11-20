<template>
	<div>
		<!-- top bar -->
		<v-app-bar class="bg-default">
			<template v-if="$vuetify.display.mdAndUp">
				<v-row no-gutters align="center" class="px-9 py-9">
					<v-col cols="1"></v-col>
					<v-col cols="10">
						<v-row justify="center" no-gutters>
							<template v-for="(nav_link,i) in nav_links" :key="i">
								<v-col cols="auto">
									<v-btn
										variant="text"
										:to="nav_link.to"
										:ripple="false"
										class="font-weight-bold"
										exact
									>
										{{ nav_link.title }}
									</v-btn>
								</v-col>
							</template>
						</v-row>
					</v-col>
					<v-col cols="1" class="d-flex justify-content-end">
						<v-card
							flat style="width:28px" color="transparent"
							class="d-flex justify-center align-center"
							:to="authPage"
						>
							<v-icon icon="mdi-account-circle"></v-icon>
						</v-card>
					</v-col>
				</v-row>
			</template>

			<template v-else>
				<v-row justify="end" no-gutters class="px-5">
					<v-card
						flat style="width:28px" color="transparent"
						class="d-flex justify-center align-center mr-3"
						:to="authPage"
					>
						<v-icon icon="mdi-account-circle"></v-icon>
					</v-card>
					<v-card
						color="transparent" flat
						@click.stop="isOpen = !isOpen"
					>
						<v-icon icon="mdi-menu" size="x-large"></v-icon>
					</v-card>
				</v-row>
			</template>
		</v-app-bar>

		<!-- side bar -->
		<v-navigation-drawer
			ref="sidebar"
			v-model="isOpen"
			temporary
			floating
			class="elevation-1"
			style="background-color:#FFE1EA;"
			location="right"
		>
			<v-list class="py-0" dense flat>
				<v-card color="transparent" class="white--text pa-5 mb-3" tile>
					<v-row justify="start">
						<v-card color="transparent" flat @click.stop="isOpen = !isOpen">
							<v-icon icon="mdi-alpha-x" size="x-large"></v-icon>
						</v-card>
					</v-row>
				</v-card>
				<template v-for="(nav_link,i) in nav_links" :key="i">
					<v-list-item
						:to="nav_link.to"
						exact
						class="font-primary"
						:ripple="false"
						exact-active-class="btnActive"
					>
						<v-list-item-subtitle class="text-subtitle-2 ">
							<div class="font-weight-medium">{{ nav_link.title }}</div>
						</v-list-item-subtitle>
					</v-list-item>
				</template>
			</v-list>
		</v-navigation-drawer>
	</div>
</template>

<script>

export default {
	props: {
	},
	data(){
		return {
			nav_links : [],
			isOpen: null,
			hover:false,
			auth:false,
		}
	},
	computed: {
		authPage() {
			return this.auth ? { name: 'profile-page' } : { name: 'login-page' };
		},
	},
	created(){
		this.nav_links = [
			{ title : "Product", to : { name: "product-page" } },
			// { title : "Contact Us", to : { name: "contact-us-page" } },
		];
		this.auth = false
		if(this.$auth.check()){
			// this.$toast.success("Logged in successfully")
			this.auth = true
		}
	},
	methods: {
		// async authCheck(){
		// 	const data = await axios.get('api/user').then(res=>{
		// 		this.$toast.success("Authenticated")
		// 		this.auth = true
		// 	}).catch((err)=>{
		// 		this.$toast.error("Not authenticated")
		// 	});
		// }
	}
}
</script>

<style scoped>
.custom-icon {
  width: 32px !important; /* Adjust the width to your desired size */
  height: 32px !important; /* Adjust the height to your desired size */
}



</style>