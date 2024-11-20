<template>
	<v-sheet class="fill-width bg-default">
		<v-container class="py-0" style="width:60%;">
			<v-divider class="border-opacity-100 my-0"></v-divider>
			<v-card
				flat tile
				color="transparent"
			>
				<v-row class="white--text my-0 my-md-1" justify="space-between">
					<!-- <v-col cols="3" md="1" class="d-flex pb-0 pl-0 pa-2 order-0 order-0 order-md-0">
						<router-link
							:to="{name:'product-page'}"
							class="text-decoration-none mx-auto"
							exact
						>
							<template v-if="$vuetify.display.mdAndUp">
								<v-img width="70" src="/images/nmslogo-removebg-preview.png"></v-img>
							</template>
							<template v-else>
								<v-img width="50" src="/images/nmslogo-removebg-preview.png"></v-img>
							</template>
						</router-link>
					</v-col> -->
					<!-- <v-col
						cols="12" md="9" class="text-caption grey--text text-center text-md-start pa-2 order-2 order-md-1"
						align-self="center"
					>
						{{ $t('footer.copyrights') }} {{ $dayjs().format("YYYY") }} © {{ copyrightTitle }} {{ $t('footer.ssm') }}. <span v-html="$t('footer.powered_by', {'author' : authorEl})"></span>
					</v-col> -->
					<v-col
						cols="12" align-self="center"
						class="d-flex justify-center"
					>
						@NormalizeStrange
						<v-card
							flat width="25" height="25"
							class="ml-2" color="transparent"
							href="https://www.instagram.com/normalizestrange/"
							target="_blank"
						>
							<v-img
								width="100%" height="100%"
								src="/images/Icons/Instagram.png"
							></v-img>
						</v-card>
					</v-col>
				</v-row>
			</v-card>
		</v-container>
	</v-sheet>
</template>

<script>
export default {
	data(){
		return {
			poweredBy: null,
			copyrightTitle: "",
			informationPages : [],
			backToTopBtn: false,
		}
	},
	computed:{
		// authorEl(){
		// 	return `<a href="${this.poweredBy.href}" class="text-color-unset text-decoration-underline" target="_blank">${this.poweredBy.text}</a>`
		// },
	},
	created(){
		// this.copyrightTitle = "GM TRACTOR PARTS SDN BHD"
		// this.poweredBy = {
		// 	text: "ETC Web Design",
		// 	href: "https://etctech.com.my",
		// }
		// this.informationPages = [
		// 	{ title : "Home", to : { name: "home-page" } },
		// 	{ title : "About Us", to : { name: "about-us-page" } },
		// 	{ title : "Contact Us", to : { name: "contact-us-page" } },
		// ]
	},
	methods:{
		onScroll (e) {
			if (typeof window === 'undefined') return
			const top = window.pageYOffset ||   e.target.scrollTop || 0
			this.backToTopBtn = (top > 50)
		},
		toTop() {
			this.$vuetify.goTo(0);
		},
	},
}
</script>

<style scoped>
.border-top-primary{
	border-top: 4px solid #DE002B !important;
}
</style>
