<template>
	<v-card flat color="transparent">
		<div
			id="swiper"
			class="overflow-hidden"
		>
			<div class="swiper-wrapper">
				<template v-for="(photo, i) in items" :key="i">
					<div
						class="swiper-slide"
					>
						<div class="swiper-zoom-container">
							<slot
								name="image"
								:item="photo"
								:index="i"
							>
							</slot>
						</div>
					</div>
				</template>
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
		<div
			id="thumbSwiper"
			class="overflow-hidden mt-5"
			style="height:110px;"
		>
			<div class="swiper-wrapper">
				<template v-for="(photo, i) in items" :key="i">
					<div
						class="swiper-slide text-center"
					>
						<slot
							name="thumbImage"
							:item="photo"
							:index="i"
						></slot>
					</div>
				</template>
			</div>
		</div>
	</v-card>
</template>

<script>
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

export default {
	props:{
		items: {
			type: Array,
			default: () => [],
		}
	},
	data() {
		return {
			swiper: null,
			thumbSwiper: null,
		}
	},
	mounted(){
		this.initSwiper()
	},
	methods:{
		initSwiper(){
			this.thumbSwiper = new Swiper("#thumbSwiper", {
				loop: false,
				spaceBetween: 2,
				slidesPerView: 4,
				freeMode: true,
				watchSlidesProgress: true,
			});

			this.swiper = new Swiper("#swiper", {
				loop: false,
				spaceBetween: 1,
				grabCursor: true,
				thumbs: {
					swiper: this.thumbSwiper,
				},
				zoom: {
					maxRatio: 3,
					minRation: 1,
				},
				on:{
					tap:function(){
						this.zoom.toggle()
					},
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
			});
		},
		zoomIn(){
			this.swiper.zoom.in()
		},
		zoomOut(){
			this.swiper.zoom.out()
		}
	}
}
</script>

<style scoped>
.swiper-button-next, .swiper-button-prev{
    color: grey;
	--swiper-navigation-size:33px !important;
}

.swiper-slide-thumb-active {
    transform: scale(1.1); /* You can adjust the scale factor */
    transition: transform 0.3s ease; /* Add a smooth transition effect */
  }
</style>

