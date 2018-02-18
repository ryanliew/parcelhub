<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Inbound orders
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create new inbound order</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="inbounds" 
								:fields="fields" 
								url="/internal/inbound/user"
								:searchables="searchables">	
					</table-view>
				</div>
			</div>
			
			<inbound :inbound="selectedInbound"
					:canManage="can_manage" 
					@back="back()" 
					@canceled="cancelInbound"
					v-else>
			</inbound>
		</transition>
		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="onSubmit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)">
				<div v-show="step == 1">
		         	<div class="field">
		          		<text-input v-model="form.arrival_date" :defaultValue="form.arrival_date" 
									label="Arrival date" 
									:required="true"
									name="arrival_date"
									type="date"
									:editable="true"
									:error="form.errors.get('arrival_date')"
									:focus="true">
						</text-input>
					</div>
					<div class="field">
						<text-input v-model="form.total_carton" :defaultValue="form.total_carton" 
									label="Total carton" 
									:required="true"
									name="total_carton"
									type="text"
									:editable="true"
									:error="form.errors.get('total_carton')">
						</text-input>
					</div>
					<div class="field mt-30">
						<products-selector-input v-model="selectedProducts" :defaultData="selectedProducts" 
												label="Products"
												name="products" 
												:required="true"
												:potentialData="productsOptions"
												@input="productsUpdate($event)"
												:editable="true"
												placeholder="Select products"
												:multiple="true"
												:error="form.errors.get('products')">
						</products-selector-input>
		          	</div>
		        </div>
		        <div v-if="step == 2">
		          	<div class="field product-list">
		          		<div class="media" v-for="(product, index) in selectedProducts">
	          				<figure class="media-left">
			          			<p class="image is-64x64">
			          				<img :src="product.picture">
			          			</p>
			          		</figure>
			          		<div class="media-content">
			          			<div class="content">
				          			<text-input v-model="form.products[index].quantity" :defaultValue="form.products[index].quantity"
				          						:label="'Quantity for ' + product.name"
				          						:required="true"
				          						type="text"
				          						:editable="true">
				          			</text-input>
				          		</div>
			          		</div>
		          		</div>
		          	</div>
		        </div>
          	</form>
			
			<progress class="progress is-primary mt-30" :value="step" max="2"></progress>

          	<template slot="footer">
          		<button class="button is-primary" :class="buttonClass" @click="step++" v-if="step == 1" :disabled=" selectedProducts.length == 0 || form.errors.any()">Next step</button>
          		<div v-if="step == 2">
	          		<button class="button is-info" :class="buttonClass" @click="step--">Back</button>
					<button class="button is-primary" :class="buttonClass" @click="submit">Submit</button>
				</div>
          	</template>
		</modal>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Inbound from '../objects/Inbound.vue';

	export default {
		props: ['can_manage'],

		components: { TableView, Inbound },

		data() {
			return {
				fields: [
					{name: 'id', title: '#'},
					{name: 'arrival_date', sortField: 'date', title: 'Arrival date', callback: 'date'},
					{name: 'total_carton', sortField: 'carton', title: 'Total carton'},
					{name: 'process_status', callback: 'inboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:inbounds-actions', title: 'Actions'}	
				],
				searchables: "process_status",
				selectedInbound: '',
				dialogActive: false,
				override: false,
				form: new Form({
					arrival_date: '',
					total_carton: '',
					products: [{id: 0, quantity:0}],
				}),
				isDeleting: false,
				selectedProducts: [],
				productsOptions: [],
				step: 1,
				isViewing: false

			};
		},

		mounted() {
			this.getProducts();
			this.$events.on('view', data => this.view(data));
		},

		methods: {
			getProducts() {
				axios.get('internal/products')
					.then(response => this.setProducts(response));
			},

			setProducts(response) {
				this.productsOptions = response.data.data;
			},

			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onError(error));
			},

			onError(error) {
				this.step = 1;
			},

			onSuccess(data) {
				this.selectedProducts = [];
				this.dialogActive = false;
				this.$refs.inbounds.refreshTable();
			},

			view(data) {
				this.selectedInbound = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.selectedInbound = '';
			},

			cancelInbound() {
				this.selectedInbound.process_status = "canceled";
			},

			modalOpen() {
				this.form.reset();
				this.step = 1;
				this.selectedProducts = [];
				this.dialogActive = true;
			},

			productsUpdate(data) {
				this.form.products = data.map(product =>{
					let obj = {};
					obj["id"] = product.id;
					obj["quantity"] = 0;
					return obj;
				});

				this.form.errors.clear('products');
				
				Vue.nextTick().then( () => this.selectedProducts = data);
							
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedInbound
						? "Edit order #" + this.selectedInbound.id
						: "Create new inbound order";
			},

			action() {
				let action = this.selectedInbound ? "update" : "store";
				return "/inbound/" + action;
			},

			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			}
		}
	}
</script>