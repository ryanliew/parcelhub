<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing && !isCreating">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Inbound orders
							</div>
						</div>
						<div class="level-right" v-if="!can_manage">
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
					v-if="isViewing">
			</inbound>
		</transition>
		<transition name="slide-fade">
			<div class="card" v-if="isCreating" @back="isCreating = false">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								New inbound order
							</div>
						</div>
						<div class="level-right">
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
						<p class="is-danger header" v-if="form.errors.get('overall')" v-text="form.errors.get('overall')"></p> <br>
						<div class="columns">
				         	<div class="column">
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
							<div class="column">
								<text-input v-model="form.total_carton" :defaultValue="form.total_carton" 
											label="Total carton" 
											:required="true"
											name="total_carton"
											type="text"
											:editable="true"
											:error="form.errors.get('total_carton')">
								</text-input>
							</div>
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
								<div class="column">
									<b>Consuming volume(m³)</b>
								</div>
								<div class="column is-2">
									<div class="button is-primary is-small" @click="addRow">
										<i class="fa fa-plus"></i>
										<span class="pl-5">Add product</span>
									</div>
								</div>
				          	</div>
			          		<div v-for="(row, index) in productRows" class="columns">
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
				          						@input="clearProductErrors"
				          						name="quantity">
				          			</text-input>
								</div>
								<div class="column">
									<span v-if="productRows[index].product">
										{{ productRows[index].product.volume * productRows[index].quantity / 100 }}
									</span>
								</div>
								<div class="column is-2">
									<div class="button is-danger is-small" @click="removeRow(index)">
										<i class="fa fa-minus"></i>
										<span class="pl-5">Remove product</span>
									</div>
								</div>
				          	</div>
			          		<p class="is-danger header" v-text="errorForProducts"></p>
				        </div>
			    
				        <button class="button is-primary mt-15" :disabled="this.form.errors.any()" :class="buttonClass" type="submit">Submit</button>
		          	</form>
		        </div>
			</div>
		</transition>
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
				searchables: "process_status",
				selectedInbound: '',
				isCreating: false,
				override: false,
				form: new Form({
					arrival_date: '',
					total_carton: '',
					products: []
				}),
				isDeleting: false,
				productsOptions: [],
				isViewing: false,
				errorForProducts: '',
				productRows: []

			};
		},

		mounted() {
			this.getProducts();
			this.$events.on('viewInbound', data => this.view(data));
		},

		methods: {
			getProducts() {
				axios.get('internal/products/selector')
					.then(response => this.setProducts(response));
			},

			setProducts(response) {
				this.productsOptions = response.data.data;
			},

			addRow() {
				this.productRows.push({ product: null, quantity: 0});
				this.clearProductErrors();
			},

			removeRow(index) {
				this.productRows.splice(index, 1);
				this.clearProductErrors();
			},

			submit() {
				
				this.processProduct();

				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onError(error));
			},

			onError(error) {
				this.step = 1;
				this.errorForProducts = this.form.errors.get('products');
			},

			onSuccess(data) {
				this.productRows = [];
				this.dialogActive = false;
				this.back();
				this.$refs.inbounds.refreshTable();
			},

			view(data) {
				this.selectedInbound = data;
				this.isViewing = true;
			},

			back() {
				this.isCreating = false;
				this.isViewing = false;
				this.selectedInbound = '';
			},

			cancelInbound() {
				this.selectedInbound.process_status = "canceled";
			},

			modalOpen() {
				this.form.reset();
				this.isCreating = true;
			},

			clearProductErrors() {
				this.form.errors.clear("products");
				this.errorForProducts = '';
			},

			processProduct() {
				if(this.productRows.length > 0)
					this.form.products = [];
				else if(this.form.products)
					delete this.form.products;

				this.productRows.forEach(function(element) {
					if(element.product && element.quantity > 0) {
						
						let product = _.findIndex(this.form.products, function(p){ return p.id == element.product.id}.bind(element));

						if(product > -1)
							this.form.products[product].quantity = parseInt(this.form.products[product].quantity) + parseInt(element.quantity);
						else
							this.form.products.push({id: element.product.id, quantity: element.quantity});
					}
				}.bind(this));							
			}
		},

		computed: {
			action() {
				let action = this.selectedInbound ? "update" : "store";
				return "/inbound/" + action;
			},

			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			},

			fields() {
				let displayFields = [{name: 'id', title: '#'},
					{name: 'arrival_date', sortField: 'date', title: 'Arrival date', callback: 'date'},
					{name: 'total_carton', sortField: 'carton', title: 'Total carton'},
					{name: 'process_status', callback: 'inboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:inbounds-actions', title: 'Actions'}];

				if(this.can_manage)
				{
					displayFields.splice(1, 0, {name: 'customer', sortField: 'users.name', title: 'Customer'});
				}

				return displayFields;
			}
		}
	}
</script>