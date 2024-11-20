<template>
	<v-sheet class="fill-height d-flex flex-column justify-center" color="black">
		<div class="video-player d-flex white--text justify-center font-family-secondary" :style="$vuetify.display.mdAndUp?'font-size:150px;':'font-size:80px;'">
			welc
			<video
				id="vid" class="video px-2" muted
				loop autoplay
				playsinline style="margin-top:23px;"
				:style="$vuetify.display.smAndUp?'width:8vw':'width:20vw'"
			>
				<source src="videos/nmscropglobe.mp4" type="video/mp4">
			</video>
			me
		</div>
		<v-container>
			<div class="white--text text-center pb-8 font-family-secondary">Til Further Notice</div>
			<v-text-field
				v-model="form.email"
				class="noBorder"
				solo flat outlined
				label="We'll keep you posted"
				:error-messages="email_errors"
				:hide-details="email_errors == null"
				type="email"
				style="width:70vw; margin:0 auto;"
			></v-text-field>
			<div class="py-12 text-center">
				<v-btn
					outlined color="white" :loading="loading"
					tile class="font-family-secondary" @click="submitData(form)"
				>
					Submit Now
				</v-btn>
			</div>
		</v-container>
	</v-sheet>
</template>

<script>
import GeneralClient from '../client.js';

export default {
	components:{
	},
	data() {
		return {
			form: {},
			loading: false,
			email_errors:'',
			dialog: false,
		};
	},
	created(){
		setTimeout(() => {
			this.startPlayback();
		}, 100);
	},
	methods: {
		startPlayback() {
			document.getElementById('vid').play();
		},
		submitData(item){
			let payload = item
			this.loading = true;
			this.email_errors = ''
			GeneralClient.submitContactUs(payload).then((res) => {
				this.$toast.success("Successfully submitted")
				this.loading = false;
				this.form.email = ''
			}).catch((err) => {
				this.email_errors = err.response.data.message
			}).finally(()=>{
				this.loading = false;
			})
		}
	},
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 2.5s ease;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>