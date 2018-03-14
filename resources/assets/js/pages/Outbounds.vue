
<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing && !isCreating">
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
					v-if="isViewing">
			</outbound>
		</transition>
		<transition name="slide-fade">
			<div v-if="isCreating" class="card" :active="dialogActive" @close="dialogActive = false">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								{{ dialogTitle }}
							</div>
						</div>
						<div class="level-right" v-if="!can_manage">
							<div class="level-item">
								<button class="button is-warning" @click="back()">
									<i class="fa fa-arrow-circle-left"></i>
									<span class="pl-5">Cancel</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="card-content">
					<form @submit.prevent="submit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)"
						@keyup.enter="submit">
						<div class="columns">
				         	<div class="column">
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
							<div class="column">
								<text-input v-model="form.recipient_phone" :defaultValue="form.recipient_phone" 
											label="Recipient phone" 
											:required="true"
											name="recipient_phone"
											type="text"
											:editable="true"
											:error="form.errors.get('recipient_phone')">
								</text-input>
							</div>
						</div>
						<div class="field">
							<text-input v-model="form.recipient_address" :defaultValue="form.recipient_address" 
											label="Recipient address line 1" 
											:required="true"
											name="recipient_address"
											type="text"
											:editable="true"
											:error="form.errors.get('recipient_address')">
							</text-input>
						</div>
						<div class="field">
							<text-input v-model="form.recipient_address_2" :defaultValue="form.recipient_address_2" 
											label="Recipient address line 2" 
											:required="false"
											name="recipient_address_2"
											type="text"
											:editable="true"
											:error="form.errors.get('recipient_address_2')">
							</text-input>
						</div>
						<div class="columns">
							<div class="column">
								<text-input v-model="form.recipient_postcode" :defaultValue="form.recipient_postcode" 
												label="Recipient postcode" 
												:required="true"
												name="recipient_postcode"
												type="text"
												:editable="true"
												:error="form.errors.get('recipient_postcode')">
								</text-input>
							</div>
							<div class="column">
								<text-input v-model="form.recipient_state" :defaultValue="form.recipient_state" 
												label="Recipient state" 
												:required="true"
												name="recipient_state"
												type="text"
												:editable="true"
												:error="form.errors.get('recipient_state')">
								</text-input>
							</div>
							<div class="column">
								<text-input v-model="form.recipient_country" :defaultValue="form.recipient_country" 
												label="Recipient country" 
												:required="true"
												name="recipient_country"
												type="text"
												:editable="true"
												:error="form.errors.get('recipient_country')">
								</text-input>
							</div>
						</div>
						<div class="field">
							<selector-input v-model="selectedCourier" :defaultData="selectedCourier" 
													label="Courier"
													name="couriers" 
													:required="true"
													:potentialData="couriersOptions"
													@input="couriersUpdate($event)"
													:editable="true"
													placeholder="Select courier"
													:error="form.errors.get('courier_id')">
							</selector-input>
			          	</div>
						
						<div class="columns">
				          	<div class="column is-narrow">
								<checkbox-input v-model="form.insurance" :defaultChecked="form.insurance"
											label="Delivery insurance?" 
											:required="false"
											name="insurance"
											:editable="true"
											class="mt-10">
								</checkbox-input>
							</div>
							
							<transition name="fade">
								<div class="column" v-if="form.insurance">
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
						</div>
						

			          	<div class="products-list">
				          	<div class="columns">
				          		<div class="column is-narrow">
				          			#
				          		</div>
								<div class="column">
									<b>Product</b>
								</div>
								<div class="column">
									<b>Quantity</b>
								</div>
								<div class="column is-2">
									<div class="button is-primary is-small" @click="addRow">
										<i class="fa fa-plus"></i>
										<span class="pl-5">Add product</span>
									</div>
								</div>
				          	</div>
			          		<div v-for="(row, index) in productRows" class="row">
			          			<div class="columns">
				          			<div class="column is-narrow">
										{{ index + 1 }}
				          			</div>
									<div class="column">
										<products-selector-input v-model="productRows[index].product" :defaultData="productRows[index].product" 
													label="Products"
													name="products" 
													:required="true"
													:potentialData="productsOptions"
													:editable="true"
													placeholder="Select product"
													:hideLabel="true"
													@input="clearProductErrors">
										</products-selector-input>
									</div>
									<div class="column">
										<text-input v-if="productRows[index]" v-model="productRows[index].quantity" :defaultValue="productRows[index].quantity"
					          						:label="'Quantity'"
					          						:required="true"
					          						type="number"
					          						:editable="true"
					          						:hideLabel="true"
					          						@input="clearProductErrors(index)"
					          						name="quantity">
					          			</text-input>
									</div>
									<div class="column is-2">
										<div class="button is-danger is-small" @click="removeRow(index)">
											<i class="fa fa-minus"></i>
											<span class="pl-5">Remove product</span>
										</div>
									</div>
								</div>
								<p class="is-danger header" v-text="form.errors.get('outbound_products.' + index)"></p>
				          	</div>
				        </div>	
						<p class="is-danger header" v-text="this.form.errors.get('outbound_products')"></p>

				        <button class="button is-primary mt-15" :disabled="form.errors.any()" :class="buttonClass">Submit</button>
		          	</form>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: ['can_manage'],

		components: { TableView },

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
					recipient_phone: '',
					recipient_address: '',
					recipient_address_2: '',
					recipient_state: '',
					recipient_postcode: '',
					recipient_country: '',
					insurance: '',
					amount_insured: '0',
					courier_id: '',
					outbound_products: [],
				}),
				isDeleting: false,
				errorForProducts: '',
				productRows: [],
				selectedCourier: false,
				couriersOptions: [],
				productsOptions: [],
				isViewing: false,
				isCreating: false,
				errorForProducts: ''

			};
		},

		mounted() {
			this.getProducts();
			this.$events.on('viewOutbound', data => this.view(data));
		},

		methods: {
			getProducts() {
				axios.get('internal/products/selector')
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

			addRow() {
				this.productRows.push({ product: null, quantity: 0});
				this.clearProductErrors();
			},

			removeRow(index) {
				this.productRows.splice(index, 1);
				this.clearProductErrors(index);
			},

			clearProductErrors(index) {
				this.form.errors.clear("outbound_products");
				this.form.errors.clear("outbound_products." + index);
				this.errorForProducts = '';
			},

			processProduct() {
				if(this.productRows.length > 0)
					this.form.outbound_products = [];
				else if(this.form.outbound_products)
					delete this.form.outbound_products;

				this.productRows.forEach(function(element) {
					if(element.product && element.quantity > 0) {
						
						let product = _.findIndex(this.form.outbound_products, function(p){ return p.id == element.product.id}.bind(element));

						if(product > -1)
							this.form.outbound_products[product].quantity = parseInt(this.form.outbound_products[product].quantity) + parseInt(element.quantity);
						else
							this.form.outbound_products.push({id: element.product.id, quantity: element.quantity});
					}
				}.bind(this));							
			},

			submit() {
				this.processProduct();

				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onError(error));
			},

			modalOpen() {
				this.form.reset();
				this.isCreating = true;
			},

			onError(error) {
				this.step = 1;
				this.errorForProducts = this.form.errors.get('outbound_products');
			},

			onSuccess(data) {
				this.productRows = [];
				this.dialogActive = false;
				this.back();
				this.$refs.outbounds.refreshTable();
			},

			view(data) {
				this.selectedoutbound = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.isCreating = false;
				this.selectedoutbound = '';
			},

			canceloutbound() {
				this.selectedoutbound.process_status = "canceled";
			},

			couriersUpdate(data) {
				this.form.courier_id = data.value;
				this.form.errors.clear('courier_id');
			},

			productsUpdate(data) {
				this.form.products = data.map(product =>{
					let obj = {};
					obj["id"] = product.id;
					obj["quantity"] = 0;
					return obj;
				});

				this.form.errors.clear('outbound_products');
				
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