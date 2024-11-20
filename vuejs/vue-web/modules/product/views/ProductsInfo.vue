<template>
	<section v-if="hasData" style="height:100%;">
		<v-container style="height:100%;">
			<template v-if="modelLoading">
				<v-row style="height:100%;" align="center" data-aos="zoom-in">
					<v-col cols="12" md="6">
						<v-skeleton-loader style="background-color:#FFE1EA !important;" type="image"></v-skeleton-loader>
					</v-col>
					<v-col cols="12" md="6">
						<v-skeleton-loader style="background-color:#FFE1EA !important;" type="article"></v-skeleton-loader>
					</v-col>
				</v-row>
			</template>
			<template v-else>
				<v-row>
					<v-col cols="12" md="6" data-aos="fade-right">
						<thumbs-gallery class="position-relative" :items="productInfo.media">
							<template #image="{ item }">
								<img
									id="product-preview" class="selectedImage"
									:src="item.original_url"
								>
								<!-- <svg-icon class="custom-icon" type="mdi" :path="pathMagnifyPlus"></svg-icon> -->
							</template>
							<template #thumbImage="{ item }">
								<img
									id="product-preview"
									:src="item.original_url"
									style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;"
									:style="$vuetify.display.smAndUp?'height:80%  !important;':'height:60%  !important;'"
									class="my-3"
								>
							</template>
						</thumbs-gallery>
					</v-col>
					<v-col
						cols="12" md="6" data-aos="fade-left"
						class="d-flex align-self-center"
					>
						<v-card color="transparent" style="height:100%; width:100%;" flat>
							<v-card-title class="headline font-family-secondary" style="font-size: 30px;">{{ productInfo.title }}</v-card-title>
							<v-card-subtitle class="grey--text" style="font-size: 20px;">MYR {{ productInfo.price }}</v-card-subtitle>
							<hr>
							<div class="px-4 font-weight-bold" style="font-size: 16px;">Description :</div>
							<v-card-text><pre class="font-family-default">{{ productInfo.description }}</pre></v-card-text>
							<!-- Size Selection -->
							<div class="px-4 mt-5 font-weight-bold" style="font-size: 16px;">Size :</div>
							<v-btn-toggle
								v-model="selectedSize" rounded mandatory
								divided class="my-5" :class="$vuetify.display.smAndUp?'':'d-flex justify-content-center'"
							>
								<v-btn
									v-for="size in productInfo.sizes" :key="size" :value="size"
									rounded class="mx-1 rounded-5" :style="$vuetify.display.smAndUp?'width:80px;':'width:30px; height:38px;'"
									@click="updateAvailableQty"
								>
									{{ size.size }}
								</v-btn>
							</v-btn-toggle>

							<!-- Available Quantity -->
							<div class="px-4 mt-n2 mb-5 grey--text" style="font-size: 14px; opacity: 0.7;">Available Quantity : {{ availableQty }}</div>

							<!-- Quantity Selection -->
							<div class="px-4 mt-5 font-weight-bold" style="font-size: 16px;">Quantity :</div>
							<v-row
								align="center" justify="space-around" class="mx-3 my-5 border border-left-0 border-right-0"
								:class="$vuetify.display.smAndUp ? '' : 'mx-auto'" style="width:150px;"
							>
								<v-col cols="auto" class="px-0 py-2">
									<v-icon @click="decrementQuantity">mdi-minus</v-icon>
								</v-col>
								<v-col cols="auto" class="d-flex align-self-center py-2">
									{{ selectedQuantity }}
								</v-col>
								<v-col cols="auto" class="px-0 py-2">
									<v-icon @click="incrementQuantity">mdi-plus</v-icon>
								</v-col>
							</v-row>

							<!-- Size Chart Expansion Panel -->
							<v-expansion-panels>
								<v-expansion-panel>
									<v-expansion-panel-title>
										Size Chart
									</v-expansion-panel-title>
									<v-expansion-panel-text class="pa-0">
										<!-- Scrollable Table for Product Measurements -->
										<v-card>
											<v-card-text class="overflow-auto">
												<v-table>
													<template #default>
														<thead>
															<tr>
																<th>Size</th>
																<th>Bust</th>
																<th>Length</th>
																<th>Shoulder</th>
																<th>Sleeve Length</th>
																<th>Waist Size</th>
																<!-- Add more measurement columns as needed -->
															</tr>
														</thead>
														<tbody>
															<!-- Add data for each size and its measurements -->
															<tr v-for="sizeMeasurement in sizeMeasurements" :key="sizeMeasurement.size" class="sizeMasurementWrapper">
																<td>{{ sizeMeasurement.size }}</td>
																<td>{{ sizeMeasurement.bust }}</td>
																<td>{{ sizeMeasurement.length }}</td>
																<td>{{ sizeMeasurement.shoulder }}</td>
																<td>{{ sizeMeasurement.sleeveLength }}</td>
																<td>{{ sizeMeasurement.waistSize }}</td>
																<!-- Add more measurement data as needed -->
															</tr>
														</tbody>
													</template>
												</v-table>
											</v-card-text>
										</v-card>
									</v-expansion-panel-text>
								</v-expansion-panel>
							</v-expansion-panels>
							<v-card-actions class="pl-0 my-2" :class="$vuetify.display.smAndUp?'':'justify-content-center'">
								<v-card-actions class="pl-0">
									<v-btn class="btn-primary" :loading="stripeLoading" @click="buyNow">Buy Now</v-btn>
								</v-card-actions>
							</v-card-actions>
						</v-card>
					</v-col>
				</v-row>
			</template>
		</v-container>
	</section>
	<section v-else style="height:100%;">
		<v-sheet color="transparent" style="height:100%;">
			<v-container style="height:100%;">
				<v-row style="height:100%;" align="center">
					<v-col cols="12">
						<div class="text-center pa-16">
							<div><v-icon class="text-h2 text-md-h1">mdi-package-variant</v-icon></div>
							<div class="pt-2 font-italic">No Data Available</div>
						</div>
					</v-col>
				</v-row>
			</v-container>
		</v-sheet>
	</section>
</template>

<script>
import ProductClient from "../client"
import ThumbsGallery from '../components/ThumbsGallery.vue';

import AOS from 'aos';
import 'aos/dist/aos.css';

export default {
	components:{
		ThumbsGallery,
	},
	data() {
		return {
			hasData:true,
			stripeLoading:false,
			productInfo: {
				thumbnail:'',
				sizes:[],
			},
			modelLoading:true,
			selectedSize: '',
			selectedQuantity: 1,
			availableQty:0,
			sizeMeasurements: [
				{ size: 'XS', bust: '...', length: '...', shoulder: '...', sleeveLength: '...', waistSize:'...' },
				{ size: 'S', bust: '...', length: '...', shoulder: '...', sleeveLength: '...', waistSize:'...' },
				{ size: 'M', bust: '...', length: '...', shoulder: '...', sleeveLength: '...', waistSize:'...' },
				{ size: 'L', bust: '...', length: '...', shoulder: '...', sleeveLength: '...', waistSize:'...' },
			], 
			user:{},
		};
	},
	mounted(){
		AOS.init();
	},
	created(){
		this.paramId = this.$route.params.model
		if(this.$auth.check()){
			this.user = this.$auth.user().user;
		}
		this.getProductInfo()
	},
	methods: {
		buyNow() {
			if (!this.$auth.check()) {
				// Redirect the user to the login page
				window.location.href = '/login';
			}
			else{
				this.stripeLoading = true;

				const selectedOptions = {
					id: this.paramId,
					size: this.selectedSize.size,
					quantity: this.selectedQuantity,
					user_id: this.user.id
				};

				let payload = selectedOptions;

				ProductClient.checkout(payload).then((res) => {
					window.location.href = res.data.data.checkout_url;
					this.stripeLoading = false;
				}).catch((err) => {
				}).finally(()=>{
					this.modelLoading = false
					this.stripeLoading = false;
				});
			}
		},
		getProductInfo()
		{
			this.modelLoading = true;
			ProductClient.getProductInfo(this.paramId).then((res) => {
				let result = res.data.data
				this.productInfo = result
				this.productInfo.thumbnail = result.media[0].original_url
				this.productInfo.sizes = result.sizes
				this.selectedSize = this.productInfo.sizes[0]
				this.availableQty = this.productInfo.sizes[0].quantity
			}).catch((err) => {
				if (err.response && err.response.status === 404) {
					this.$toast.error('Product not found');
					this.hasData = false;
				} else {
					// Handle other errors
					this.$toast.error('An error occurred');
				}
			}).finally(()=>{
				this.modelLoading = false
			});
		},
		incrementQuantity() {
			if (this.selectedQuantity < this.selectedSize.quantity) {
				this.selectedQuantity++;
			}
		},
		decrementQuantity() {
			if (this.selectedQuantity > 1) {
				this.selectedQuantity--;
			}
		},
		updateAvailableQty(){
			this.availableQty = this.selectedSize.quantity
			this.selectedQuantity = 1
		}
	},
};
</script>

<style scoped>
/* Add your custom styles here if needed */
.v-expansion-panel-text >>> .v-expansion-panel-text__wrapper{
  padding: 0 !important;
}

/* Add any additional styling or adjustments here */
.btn-primary {
    background-color: #4caf50; /* Green */
    color: white;
    padding: 0px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
  }

  .btn-primary:hover {
    background-color: #45a049; /* Slightly darker green on hover */
  }

  .sizeMasurementWrapper > td {
	text-align: center;
  }
</style>