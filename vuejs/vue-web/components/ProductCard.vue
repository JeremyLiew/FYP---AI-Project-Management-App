<template>
	<v-hover>
		<template #default="{ isHovering, props }">
			<v-card
				v-bind="props"
				:elevation="isHovering ? 2 : 0"
				:flat="!isHovering"
				color="transparent" class="text-center mx-auto"
				max-width="900px"
				:to="{ name: 'products-info', params: { model: item.id } }"
			>
				<div class="pa-4 pt-0 d-flex flex-column fill-height">
					<v-row>
						<v-col cols="12">
							<v-img
								:src="product.thumbnail_front"
								:aspect-ratio="1"
								width="100%"
								@mouseover="product.thumbnail_front = product.thumbnail_back"
								@mouseleave="resetThumbnail"
							></v-img>
						</v-col>
					</v-row>
					<div class="pb-3 flex-grow fill-height">
						<div :style="$vuetify.display.mdAndUp?'font-size:30px;':'font-size:24px;'" class="white--text font-family-secondary">{{ product.title }}</div>
						<div :style="$vuetify.display.mdAndUp?'font-size:20px;':'font-size:14px;'" class="grey--text py-1">MYR {{ product.price }}</div>
						<!-- <v-card
							tile flat class="text-decoration-underline grey--text"
							:to="{ name: 'products-info', params: { model: item.model } }" color="transparent"
							:style="$vuetify.display.mdAndUp?'font-size:12px;':'font-size:10px;'"
						>
							View More
						</v-card> -->
					</div>
				</div>
			</v-card>
		</template>
	</v-hover>
</template>

<script>
// import Helper from '@shared/libs/HelperManager';
export default {
	props:{
		item: {
			type: Object,
			default: () => ({
				title: "Default Name",
				id:0,
			})
		},
	},
	data(){
		return {
			product:{}
		}
	},
	created(){
		this.product.title = this.item.title
		this.product.description = this.item.model
		this.product.price = this.item.price
		this.product.thumbnail_front = this.item.media[0].original_url
		this.product.thumbnail_back = this.item.media[1].original_url
	},
	methods:{
		// thousandFilter(price){
		// 	return Helper.formatThousandSeparator(price.toString())
		// },
		// descTruncate(str , len){
		// 	return Helper.truncate(str,len)
		// }
		resetThumbnail() {
			this.product.thumbnail_front = this.item.media[0].original_url
		}
	}
}
</script>