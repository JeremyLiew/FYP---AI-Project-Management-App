<template>
	<v-carousel
		v-model="carousel"
		:height="showHeight"
		continuous
		delimiter-icon="mdi-minus"
		hide-delimiter-background
		:show-arrows="showArrows"
		:cycle="cycle"
		class="productCarousel"
		@change="emitChangeEvent"
	>
		<slot></slot>

		<v-item-group
			v-model="carousel"
			class="transform-center-x"
			:class="{
				'position-absolute' : absoluteDelimiter,
			}"
			:style="absoluteDelimiter ? 'bottom: 8px;' : '' "
			@change="emitChangeEvent"
		>
		</v-item-group>

		<template #prev="{ on }">
			<div v-on="on">
				<!-- <v-img sizes="34" src="/images/LeftRight/Left.png"></v-img> -->
				<v-icon
					color="secondary"
					class="text-h4"
				>
					mdi-chevron-left
				</v-icon>
			</div>
		</template>

		<template #next="{ on }">
			<div v-on="on">
				<!-- <v-img sizes="34" src="/images/LeftRight/Right.png"></v-img> -->
				<v-icon
					color="secondary"
					class="text-h4"
				>
					mdi-chevron-right
				</v-icon>
			</div>
		</template>
	</v-carousel>
</template>

<script>
export default {
	props: {
		value:{
			type: [Number, String],
			default: null,
		},
		length: {
			type: Number,
			required: true,
		},
		showArrows: {
			type: Boolean,
			default: true,
		},
		absoluteDelimiter: {
			type: Boolean,
			default: true,
		},
		cycle: {
			type: Boolean,
			default: false,
		},
		showHeight: {
			type: String,
			default: '100%',
		}
	},
	data() {
		return {
			carousel: null,
		}
	},
	watch: {
		value: {
			handler(newVal, oldVal) {
				this.carousel = newVal;
			},
			immediate: true,
		}
	},
	methods: {
		emitChangeEvent() {
			this.$emit('input', this.carousel)
		}
	}
}
</script>

<style>
.productCarousel .v-window__prev , .productCarousel .v-window__next{
	background: transparent!important;
}
.productCarousel .v-btn:before {
    background-color: transparent;
}
.productCarousel .v-btn--icon.v-size--small {
	width: 28px;
}
.productCarousel .v-btn > .v-btn__content .v-icon {
    color: grey;
	font-size: 50px!important;
	width: 100%;
	filter: drop-shadow(0px 0px 2px #000000);
}
.productCarousel .v-btn--active > .v-btn__content .v-icon {
    color: white;
	opacity: 1;
}
.productCarousel .v-carousel__controls{
	padding-bottom: 20px;
}

@media only screen and (min-width: 960px){
	.productCarousel .v-btn--icon.v-size--small {
	width: 78px;
}
}

</style>