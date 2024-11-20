<template>
	<v-container>
		<!-- My Account title and Logout button -->
		<v-row class="mb-4">
			<v-col class="text-h6" cols="6">
				My Account
				<v-row>
					<!-- Account Details -->
					<v-col cols="6">
						<div>
							<v-divider></v-divider>
							<v-row>
								<v-col>
									<v-row class="text-subtitle-1">
										<v-col cols="12" md="auto"><strong>Email :</strong></v-col>
										<v-col cols="12" md="auto">{{ user.email }}</v-col>
									</v-row>
									<!-- Add more details like phone number, address, etc. -->
								</v-col>
							</v-row>
						</div>
					</v-col>
				</v-row>
			</v-col>
			<v-col class="d-flex justify-end" cols="6">
				<v-btn :loading="is_loading" color="error" @click="logout">Logout</v-btn>
			</v-col>
		</v-row>

		<!-- Order History and Account Details sections -->
		<v-row>
			<!-- Order History -->
			<v-col cols="12">
				<div>
					<v-divider></v-divider>
					<v-row class="mt-4">
						<v-col class="text-h6">Order History</v-col>
					</v-row>
					<!-- Add your order history content here -->
					<template v-if="modelLoading">
						<v-row
							style="height:100%;" class="align-center" justify="center"
							data-aos="zoom-in"
						>
							<template v-for="i in 1" :key="i">
								<v-col cols="12">
									<v-skeleton-loader style="background-color:#FFE1EA !important;" type="image , text"></v-skeleton-loader>
								</v-col>
							</template>
						</v-row>
					</template>
					<template v-for="(order,i) in orders" v-else :key="i">
						<v-row
							no-gutters class="pa-3 mb-3 flex-column" outlined
							style="box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;"
						>
							<!-- Top Bar: Order Number and Status -->
							<v-col>
								<v-row class="top-bar">
									<v-col class="" cols="2" md="6">NMS#{{ order.id }}</v-col>
									<v-col class="text-right text-uppercase" cols="10" md="6">
										<template v-if="order.order_status == 'paid'"><v-icon icon="mdi-package"></v-icon> preparing to ship</template>
										<template v-else-if="order.order_status == 'shipping'"><v-icon icon="mdi-truck"></v-icon> parcel has been shipped</template>
										<template v-else-if="order.order_status == 'completed'"><v-icon icon="mdi-package-check"></v-icon> parcel has been delivered</template>
										<span style="opacity:0.3;" class="px-2">|</span>
										<span style="color:orange;">{{ order.order_status }}</span>
									</v-col>
								</v-row>
							</v-col>

							<v-divider></v-divider>

							<!-- Main Content: Image, Title, Quantity -->
							<v-col>
								<v-row class="main-content">
									<v-col class="" cols="auto">
										<v-img
											:aspect-ratio="1"
											width="100px" :src="order.products[0].media[0].original_url" alt="Item Image"
										/>
									</v-col>
									<v-col class="my-auto" cols="6">
										<div class="">{{ order.products[0].title }}</div>
										<div class="" style="color:grey;">Variation : {{ order.products[0].pivot.size }}</div>
										<div class="">x{{ order.products[0].pivot.quantity }}</div>
									</v-col>
									<v-col class="text-end">
										<span style="color:orange;">RM {{ order.final_price }}</span>
									</v-col>
								</v-row>
							</v-col>

							<v-divider></v-divider>

							<!-- Bottom: Total Items -->
							<v-col>
								<v-row class="bottom-bar">
									<!-- <v-col class="">{{ order.order_quantity }} items</v-col> -->
									<v-col class="text-end" style="color:grey; font-size:12px;">Ordered At : {{ formattedDate(order.created_at) }}</v-col>
								</v-row>
							</v-col>
						</v-row>
					</template>
				</div>
			</v-col>
		</v-row>

		<v-container v-if="paginationLength > 1" class="pt-10 py-md-8">
			<v-row>
				<!-- <v-col cols="0" md="3"></v-col> -->
				<v-col
					cols="12" align-self="center"
					class="py-0"
				>
					<v-pagination
						v-model="page"
						rounded="circle"
						:length="paginationLength"
						total-visible="5"
						color="grey"
						@click="getOrderHistory"
					>
					</v-pagination>
				</v-col>
				<!-- <v-col
					cols="12" align-self="center"
					class="pr-0 py-0"
				>
					<div class="text-center">Showing {{ paginator.from }} - {{ paginator.to }} of {{ paginator.total }} results</div>
				</v-col> -->
				<!-- <v-col cols="0" md="3"></v-col> -->
			</v-row>
		</v-container>
	</v-container>
</template>

<script>
import GeneralClient from '../client';

export default {
	data() {
		return {
			user: {
				// Assuming you have user details here
				email: 'example@email.com',
				// Add more user details
			},
			orders:[],
			is_loading : false,
			modelLoading: true,
			page: 1,
			paginator:{},
			paginationLength: 0,
		};
	},
	created(){
		this.user = this.$auth.user().user;
		this.getOrderHistory();
	},
	methods: {
		async logout(){
			this.is_loading = true;
			this.$auth.logout({
				makeRequest: true,
				data: {},
				redirect: false,
			}).finally((res)=>{
				this.is_loading = false
				this.$toast.success("Logged out successfully")

				setTimeout(() => {
					this.$router.push({ name: 'login-page' });
				}, 500);
			});
		},

		getOrderHistory(){

			if(!(this.page == this.paginator.current_page)){
				let payload = {
					id:this.user.id,
					page:this.page,
					itemsPerPage: 5,
				}

				this.modelLoading = true;
				GeneralClient.getOrderHistory(payload).then((res)=>{
					let {data,...pagination} = res.data.data
					this.orders = data
					this.paginationLength = Math.ceil(pagination.total/pagination.per_page);
					this.paginator = pagination
				}).catch((err)=>{
				}).finally(()=>{
					this.modelLoading = false;
				})
			}
		},

		formattedDate(orderDate) {
			const date = new Date(orderDate);
			const day = date.getDate().toString().padStart(2, '0');
			const month = (date.getMonth() + 1).toString().padStart(2, '0');
			const year = date.getFullYear();
			return `${day}/${month}/${year}`;
		},
	},
};
</script>

