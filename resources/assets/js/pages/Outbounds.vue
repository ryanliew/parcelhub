
<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Outbound orders
							</div>
						</div>
						<div class="level-right" v-if="!can_manage">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create new outbound order</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="outbounds" 
								:fields="fields" 
								url="/internal/outbound/user"
								:searchables="searchables">	
					</table-view>
				</div>
			</div>
			
			<outbound :outbound="selectedoutbound"
					:canManage="can_manage" 
					@back="back()" 
					@canceled="canceloutbound"
					v-else>
			</outbound>
		</transition>
		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="onSubmit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)">
				<div v-show="step == 1">
		         	<div class="field">
		          		<text-input v-model="form.recipient_name" :defaultValue="form.recipient_name" 
									label="Recipient name" 
									:required="true"
									name="recipient_name"
									type="text"
									:editable="true"
									:error="form.errors.get('recipient_name')"
									:focus="true">
						</text-input>
					</div>
					<div class="field">
						<text-input v-model="form.recipient_address" :defaultValue="form.recipient_address" 
									label="Recipient address" 
									:required="true"
									name="recipient_address"
									type="text"
									:editable="true"
									:error="form.errors.get('recipient_address')">
						</text-input>
					</div>
					<div class="field mt-30">
						<selector-input v-model="selectedCourier" :defaultData="selectedCourier" 
												label="Couriers"
												name="couriers" 
												:required="true"
												:potentialData="couriersOptions"
												@input="couriersUpdate($event)"
												:editable="true"
												placeholder="Select preferred courier"
												:error="form.errors.get('courier_id')">
						</selector-input>
		          	</div>

		          	<div class="field">
						<checkbox-input v-model="form.insurance" :defaultChecked="form.insurance"
									label="Delivery insurance?" 
									:required="false"
									name="insurance"
									:editable="true">
						</checkbox-input>
					</div>
					
					<transition name="fade">
						<div class="field" v-if="form.insurance">
							<text-input v-model="form.amount_insured" :defaultValue="form.amount_insured" 
										label="Insured amount" 
										:required="true"
										name="amount_insured"
										type="text"
										:editable="true"
										:error="form.errors.get('amount_insured')">
							</text-input>
						</div>
					</transition>



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
												:error="errorForProducts">
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
	import outbound from '../objects/outbound.vue';

	export default {
		props: ['can_manage'],

		components: { TableView, outbound },

		data() {
			return {
				userField: [
					{name: 'id', title: '#'},
					{name: 'created_at', sortField: 'created_at', title: 'Order date', callback: 'date'},
					{name: 'courier', sortField: 'couriers.name', title: 'Courier'},
					{name: 'amount_insured', sortField: 'amount_insured', title: 'Insurance'},
					{name: 'process_status', callback: 'outboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:outbounds-actions', title: 'Actions'}	
				],
				userSearchables: "process_status",
				selectedoutbound: '',
				dialogActive: false,
				form: new Form({
					recipient_name: '',
					recipient_address: '',
					insurance: '',
					amount_insured: 0,
					courier_id: '',
					products: [{id: 0, quantity:0}],
				}),
				isDeleting: false,
				selectedProducts: [],
				selectedCourier: false,
				couriersOptions: [],
				productsOptions: [],
				step: 1,
				isViewing: false,
				errorForProducts: ''

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
				this.getCouriers();
			},

			getCouriers() {
				axios.get('internal/couriers')
					.then(response => this.setCouriers(response));
			},

			setCouriers(response) {
				this.couriersOptions = response.data.data.map(courier =>{
					let obj = {};
					obj['label'] = courier.name;
					obj['value'] = courier.id;
					return obj;
				});
			},

			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onError(error));
			},

			onError(error) {
				this.step = 1;
				this.errorForProducts = this.form.errors.get('products');
			},

			onSuccess(data) {
				this.selectedProducts = [];
				this.dialogActive = false;
				this.$refs.outbounds.refreshTable();
			},

			view(data) {
				this.selectedoutbound = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.selectedoutbound = '';
			},

			canceloutbound() {
				this.selectedoutbound.process_status = "canceled";
			},

			modalOpen() {
				this.form.reset();
				this.step = 1;
				this.selectedProducts = [];
				this.dialogActive = true;
			},

			couriersUpdate(data) {
				this.form.courier_id = data.value;
			},

			productsUpdate(data) {
				this.form.products = data.map(product =>{
					let obj = {};
					obj["id"] = product.id;
					obj["quantity"] = 0;
					return obj;
				});

				this.form.errors.clear('products');
				
				this.selectedProducts = data;

				this.errorForProducts = '';
							
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedoutbound
						? "Edit order #" + this.selectedoutbound.id
						: "Create new outbound order";
			},

			action() {
				let action = this.selectedoutbound ? "update" : "store";
				return "/outbound/" + action;
			},

			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			},

			fields() {
				let field = this.userField;
				if(this.can_manage)
				{
					field.splice(1, 0, {name: 'customer', sortField: 'users.name', title: 'Customer'});
				}

				return field;
			},

			searchables() {
				let searchables = this.userSearchables;

				if(this.can_manage)
				{
					searchables = searchables + ",users.name";
				}

				return searchables;
			}
		}
	}
</script>