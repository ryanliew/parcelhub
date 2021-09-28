
<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing && !isCreating">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Return Orders
							</div>
						</div>
						<div class="level-right" v-if="can_edit && !can_manage">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create new return order</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="returns" 
								:fields="fields" 
								url="/internal/return/user"
								:searchables="searchables"
								:dateFilterable="true"
								dateFilterKey="arrival_date">	
					</table-view>
				</div>
			</div>
			
			<return :returnobj="selectedreturn"
					:canManage="can_manage" 
					@back="back()" 
					@canceled="cancelreturn"
					:fee="fee"
					:number="number"
					v-if="isViewing"
					:canEdit="can_edit">
			</return>
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
						<div class="field" v-if="can_manage && !selectedProduct">
							<selector-input v-model="selectedUser" :defaultData="selectedUser"
									label="Return order owner"
									name="user_id"
									:required="true"
									:potentialData="userOptions"
									@input="userUpdate($event)"
									:editable="true"
									placeholder="Select return order owner"
									:error="form.errors.get('user_id')">
							</selector-input>
					    </div>
						<div class="field">
							<selector-input v-model="selectedCustomer" :defaultData="selectedCustomer"
									label="Customer"
									name="customer_id"
									:required="false"
									:potentialData="customersOptions"
									@input="customerUpdate($event)"
									:editable="true"
									placeholder="Select customer"
									:error="form.errors.get('customer_id')">

							</selector-input>
						</div>
						<div v-if="showCustomer">
							<div class="columns">
					         	<div class="column">
					          		<text-input v-model="form.recipient_name" :defaultValue="form.recipient_name" 
												label="Recipient name" 
												:required="true"
												name="recipient_name"
												type="text"
												:editable="false"
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
												:editable="false"
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
												:editable="false"
												:error="form.errors.get('recipient_address')">
								</text-input>
							</div>
							<div class="field">
								<text-input v-model="form.recipient_address_2" :defaultValue="form.recipient_address_2" 
												label="Recipient address line 2" 
												:required="false"
												name="recipient_address_2"
												type="text"
												:editable="false"
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
													:editable="false"
													:error="form.errors.get('recipient_postcode')">
									</text-input>
								</div>
								<div class="column">
									<text-input v-model="form.recipient_state" :defaultValue="form.recipient_state" 
													label="Recipient state" 
													:required="true"
													name="recipient_state"
													type="text"
													:editable="false"
													:error="form.errors.get('recipient_state')">
									</text-input>
								</div>
								<div class="column">
									<text-input v-model="form.recipient_country" :defaultValue="form.recipient_country" 
													label="Recipient country" 
													:required="true"
													name="recipient_country"
													type="text"
													:editable="false"
													:error="form.errors.get('recipient_country')">
									</text-input>
								</div>
							</div>
						</div>	
						<div class="columns">
				         	<div class="column">
				          		<text-input v-model="form.arrival_date" :defaultValue="form.arrival_date" 
											label="Return date" 
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
						
						<table class="table is-hoverable is-fullwidth is-responsive is-multirow">
							<thead>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>
									<div class="button is-primary is-small" @click="addRow">
										<i class="fa fa-plus"></i>
										<span class="pl-5">Add product</span>
									</div>
								</th>
							</thead>
							<tbody>
								<template v-for="(row, index) in productRows">
									<tr>
										<td>
											<products-selector-input v-model="productRows[index].product" :defaultData="productRows[index].product" 
														label="Products"
														name="products" 
														:required="true"
														:potentialData="productsOptions"
														:editable="true"
														placeholder="Select product"
														:hideLabel="false"
														@input="clearProductErrors">
											</products-selector-input>
										</td>
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].quantity" :defaultValue="productRows[index].quantity"
						          						label="Return quantity"
						          						:required="true"
						          						type="number"
						          						:editable="true"
						          						:hideLabel="false"
						          						@input="updateTotalValue(index)"
						          						:error="form.errors.get('return_products.' + index)"
						          						name="quantity">
						          			</text-input>
										</td>
										<td>
											<textarea-input v-if="productRows[index]" v-model="productRows[index].remarks" :defaultValue="productRows[index].remarks"
						          						:label="'Remarks'"
						          						:required="true"
						          						type="text"
						          						:editable="true"
						          						:hideLabel="false"
						          						name="remarks">
						          			</textarea-input>
										</td>
										<td></td>
										<td>
											<div class="button is-danger is-small" @click="removeRow(index)">
												<i class="fa fa-minus"></i>
												<span class="pl-5">Remove product</span>
											</div>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
			          	
						<p class="is-danger header" v-text="form.errors.get('products')"></p>

						<div class="field mt-10">
					    	<image-input v-model="invoiceSlip" :defaultImage="invoiceSlip"
					    				@loaded="changeInvoiceSlipImage"
					    				label="delivery note"
					    				name="invoice_slip"
					    				:required="false"
					    				accept="image/*,.pdf"
					    				:error="form.errors.get('invoice_slip')">
					    	</image-input>
					    </div>

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
		props: ['can_manage', 'fee', 'number', 'can_edit'],

		components: { TableView },

		data() {
			return {
				selectedreturn: '',
				dialogActive: false,
				selectedProduct: '',
				form: new Form({
					recipient_name: '',
					recipient_phone: '',
					recipient_address: '',
					recipient_address_2: '',
					recipient_state: '',
					recipient_postcode: '',
					recipient_country: '',
					customer_id: '',
					total_carton: '',
					arrival_date: '',
					insurance: '',
					amount_insured: '0',
					courier_id: '',
					return_products: [],
					invoice_slip: '',
					user_id: ''
				}),
				isDeleting: false,
				errorForProducts: '',
				productRows: [],
				selectedCourier: false,
				selectedCustomer: false,
				couriersOptions: [],
				productsOptions: [],
				customersOptions: [],
				isViewing: false,
				isCreating: false,
				isMalaysiaData: true,
				showCustomer: false,
				errorForProducts: '',
				selectedUser: false,
				getForAdmin: false,
				invoiceSlip: {name: 'No file selected'}

			};
		},

		mounted() {
			this.getProducts();
			this.getUsers();
			this.$events.on('viewReturn', data => this.view(data));
		},

		methods: {
			getUsers() {
				if(this.can_manage)
					axios.get('internal/users/selector')
						.then(response => this.setUsers(response));
				else
					axios.get('internal/user')
						.then(response => this.setUsers(response));
			},

			setUsers(data) {
				if(this.can_manage) {
					this.userOptions = data.data.map(user => {
						let obj = {};
						obj['label'] = user.name;
						obj['value'] = user.id;
						return obj;
					});
				}
				else {
					this.form.user_id = data.data.id;
				}
			},

			userUpdate(data) {
				if(this.form.user_id){
					this.selectedCustomer = null;
					this.form.customer_id = '';
					this.form.recipient_name = '';
					this.form.recipient_phone = '';
					this.form.recipient_address = '';
					this.form.recipient_address_2 = '';
					this.form.recipient_state = '';
					this.form.recipient_postcode = '';
					this.form.recipient_country = '';
					this.showCustomer = false;
				}
				this.form.user_id = data.value;
				if(this.getForAdmin){
					this.getCustomerForAdmin(data.value);
					this.getProductForAdmin(data.value);
				}	
				this.getForAdmin = true;			
			},
			isMalaysia(data) {
				this.isMalaysiaData = data.toLowerCase() == "malaysia" || data == '';
			},

			getProductForAdmin(id){
				axios.get('internal/products/admin/selector/' + id)
					.then(response => this.setProducts(response));
			},

			getProducts() {
				axios.get('internal/products/selector/false')
					.then(response => this.setProducts(response));
			},

			setProducts(response) {
				this.productsOptions = response.data.data;
				this.getCouriers();
			},

			getCustomerForAdmin(id){
				axios.get('internal/admin/customers/' + id)
					.then(response => this.setCustomers(response));
			},

			getCustomers() {
					axios.get('internal/customers')
					.then(response => this.setCustomers(response));	
			},

			setCustomers(response) {
				console.log('setCustomers');
				this.customersOptions = response.data.data.map(customer => {
					let obj = {};
					obj['value'] = customer.id;
					obj['label'] = customer.customer_name;
					obj['recipient_name'] = customer.customer_name;
					obj['recipient_phone'] = customer.customer_phone;
					obj['recipient_address'] = customer.customer_address;
					obj['recipient_address_2'] = customer.customer_address_2;
					obj['recipient_state'] = customer.customer_state;
					obj['recipient_postcode'] = customer.customer_postcode;
					obj['recipient_country'] = customer.customer_country;

					return obj;
				});
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

			updateTotalValue(index) {
				this.clearProductErrors(index);
				let product = this.productRows[index];
				this.productRows[index].total_value = product.unit_value * product.quantity;
			},

			addRow() {
				this.productRows.push({ product: null, quantity: '', remarks: ""});
				this.clearProductErrors();
			},

			removeRow(index) {
				this.productRows.splice(index, 1);
				this.clearProductErrors(index);
			},

			clearProductErrors(index) {
				this.form.errors.clear("products");
				this.form.errors.clear("products." + index);
				this.errorForProducts = '';
			},

			processProduct() {
				if(this.productRows.length > 0)
					this.form.return_products = [];
				else if(this.form.return_products)
					delete this.form.return_products;

				this.productRows.forEach(function(element) {
					if(element.product && element.quantity > 0) {
						
						let product = _.findIndex(this.form.return_products, function(p){ return p.id == element.product.id}.bind(element));

						if(product > -1) {
							this.form.return_products[product].quantity = this.form.return_products[product].quantity + parseInt(element.quantity);
						}
						else {
							this.form.return_products.push({id: element.product.id, 
															quantity: parseInt(element.quantity), 
															remarks: element.remarks});
						}
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
				this.selectedCustomer = '';
				this.selectedCourier = '';
				this.getProducts();
				this.getCustomers();
				this.isCreating = true;
			},

			onError(error) {
				this.step = 1;
				this.errorForProducts = this.form.errors.get('return_products');
			},

			onSuccess(data) {
				this.productRows = [];
				this.dialogActive = false;
				this.back();
				this.$refs.returns.refreshTable();
				this.getProducts();
			},

			view(data) {
				this.selectedreturn = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.isCreating = false;
				this.selectedreturn = '';
			},

			cancelreturn() {
				this.selectedreturn.process_status = "canceled";
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
					obj["remarks"] = '';
					return obj;
				});

				this.form.errors.clear('return_products');
				
				this.selectedProducts = data;

				this.errorForProducts = '';
							
			},

			customerUpdate(data) {
				this.form.customer_id = '';
				if(data){
					this.form.customer_id = data.value;
					this.form.errors.clear('customer_id');
					this.form.recipient_name = data.recipient_name;
					this.form.recipient_phone = data.recipient_phone;
					this.form.recipient_address = data.recipient_address;
					this.form.recipient_address_2 = data.recipient_address_2;
					this.form.recipient_state = data.recipient_state;
					this.form.recipient_postcode = data.recipient_postcode;
					this.form.recipient_country = data.recipient_country;
					this.showCustomer = true;
				}
			},

			changeInvoiceSlipImage(e) {
				//console.log(e);
				this.invoiceSlip = { src: e.src, file: e.file };
				this.form.invoice_slip = e.file;
				this.form.errors.clear('invoice_slip');
			},
		},

		computed: {
			dialogTitle() {
				return this.selectedreturn
						? "Edit order #" + this.selectedreturn.id
						: "Create new return order";
			},

			action() {
				let action = this.selectedreturn ? "update" : "store";
				return "/return/" + action;
			},

			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			},

			fields() {
				let displayFields = [{name: 'id', title: '#'},
					{name: 'arrival_date', sortField: 'arrival_date', title: 'Return date'},
					{name: 'total_carton', sortField: 'total_carton', title: 'Total carton'},
					{name: 'process_status', callback: 'inboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:returns-actions', title: 'Actions'}];

				if(this.can_manage)
				{
					displayFields.splice(1, 0, {name: 'customer', sortField: 'users.name', title: 'Customer'});
				}

				return displayFields;
			},

			searchables() {
				let searchables = "process_status";

				if(this.can_manage)
				{
					searchables = searchables + ",users.name";
				}

				return searchables;
			}
		}
	}
</script>