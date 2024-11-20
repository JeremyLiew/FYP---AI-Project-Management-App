<template>
	<div class="d-flex flex-column fill-height">
		<router-view v-slot="{ Component }">
			<transition name="slide-fade">
				<component :is="Component" />
			</transition>
		</router-view>
	</div>
</template>

<script>
import Topbar from '../components/Topbar.vue'
import Footer from "../components/Footer.vue";
export default {
	components: {
		Topbar,
		Footer,
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