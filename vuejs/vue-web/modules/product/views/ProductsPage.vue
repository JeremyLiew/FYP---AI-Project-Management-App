<template>
	<v-container style="height:100%;">
		<template v-if="modelLoading">
			<v-row
				style="height:100%;" class="align-center" justify="center"
				data-aos="zoom-in"
			>
				<template v-for="i in 1" :key="i">
					<v-col cols="8" md="6">
						<v-skeleton-loader style="background-color:#FFE1EA !important;" type="image , text"></v-skeleton-loader>
					</v-col>
				</template>
			</v-row>
		</template>
		<template v-else>
			<v-row
				style="height:100%;" class="align-center" justify="center"
				data-aos="flip-left"
			>
				<template v-for="(product, i) in productList" :key="i">
					<v-col cols="8" md="6">
						<product-card
							:item="product"
							class="fill-height"
						></product-card>
					</v-col>
				</template>
				<v-col v-if="productList == []" cols="12">
					<div class="text-center pa-16">
						<!-- <div><v-icon class="text-h2 text-md-h1">mdi-package-variant</v-icon></div> -->
						<v-img
							src="/images/No-Product-available.png" aspect-ratio="1" height="150"
							width="150" class="mx-auto"
						/>
						<div class="pt-2 font-italic">No Data Available</div>
					</div>
				</v-col>
			</v-row>
		</template>
	</v-container>
</template>

<script>
import ProductCard from '../../../components/ProductCard.vue';
import ProductClient from "../client.js"

import AOS from 'aos';
import 'aos/dist/aos.css';

export default {
	components:{
		ProductCard,
	},
	data() {
		return {
			productList: [],
			modelLoading: true,
		}
	},
	mounted(){
		AOS.init();
	},
	created(){
		this.getProductList()
	},
	methods: {
		getProductList()
		{
			this.modelLoading = true;
			this.productList = []

			ProductClient.getList().then((res) => {
				let result = res.data.data
				this.productList = result
			}).catch((err) => {
			}).finally(()=>{
				this.modelLoading = false;
			});
		},
	},
}
</script>