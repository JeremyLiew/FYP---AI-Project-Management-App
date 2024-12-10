<template>
	<div class="d-flex flex-column fill-height">
		<Topbar></Topbar>
		<v-main>
			<div class="pa-5">
				<router-view v-slot="{ Component }">
					<transition name="slide-fade">
						<component :is="Component" />
					</transition>
				</router-view>
			</div>
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

		}
	},
	created(){
	},
	methods:{
	}
}
</script>