<template>
	<div class="d-flex flex-column fill-height">
		<Topbar></Topbar>
		<v-main>
			<v-sheet class="fill-height position-relative bg-default">
				<!-- <div v-if="$route.name != 'home-page'" class="position-absolute" style="height: auto; min-height: 100% !important; bottom:0; right:0;">
					<div class="pb-4" style="top: 80vh; right: 0; z-index:2; position: sticky;">
						<v-row class="ma-0 text-end flex-column" dense>
							<v-col cols="auto" class="pr-0">
								<a class="clickable" href="https://api.whatsapp.com/send?phone=60177891551" target="_blank">
									<img src="/images/WhatsApp_v2.png" :width="$vuetify.breakpoint.mdAndUp?'140px':'100px'">
								</a>
							</v-col>
							<template v-if="$vuetify.breakpoint.mdAndUp">
								<v-col cols="auto" class="pr-11">
									<v-btn
										v-show="backToTopBtn"
										v-scroll="onScroll"
										style="width: 50px; height: 50px;"
										color="#626262"
										fab
										dark
										@click="toTop()"
									>
										<v-icon color="secondary" x-large>mdi-chevron-up</v-icon>
									</v-btn>
								</v-col>
							</template>
							<template v-else>
								<v-col cols="auto" class="pr-7">
									<v-btn
										v-show="backToTopBtn"
										v-scroll="onScroll"
										color="#626262"
										fab
										dark
										small
										@click="toTop()"
									>
										<v-icon color="secondary" x-large>mdi-chevron-up</v-icon>
									</v-btn>
								</v-col>
							</template>
						</v-row>
					</div>
				</div> -->
				<router-view v-slot="{ Component }">
					<transition name="slide-fade">
						<component :is="Component" />
					</transition>
				</router-view>
			</v-sheet>
		</v-main>
		<FooterVue></FooterVue>
	</div>
</template>

<script>
import Topbar from '../components/Topbar.vue'
import FooterVue from "../components/Footer.vue";
export default {
	components: {
		Topbar,
		FooterVue,
	},
	data(){
		return {
			backToTopBtn: false,
		}
	},
	created(){
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
	}
}
</script>