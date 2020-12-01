
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
						<div class="level-right" v-if="!can_manage && can_edit">
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
								:searchables="searchables"
								:dateFilterable="true"
								dateFilterKey="outbounds.created_at">	
					</table-view>
				</div>
			</div>
			
			<outbound :outbound="selectedoutbound"
					:canManage="can_manage"
					:canEdit="can_edit" 
					@back="back()" 
					@canceled="canceloutbound"
					:fee="fee"
					:number="number"
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
						<p class="is-danger header" v-if="form.errors.get('overall')" v-text="form.errors.get('overall')"></p> <br>
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
												@input="isMalaysia($event)"
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

						<div v-if="!isMalaysiaData">
				          	<div class="field">
								<text-input  v-model="form.payer_gst_vat" :defaultValue="form.payer_gst_vat" 
												label="Payer of GST/VAT" 
												:required="true"
												name="payer_gst_vat"
												type="text"
												seen="false"
												:editable="true"
												:error="form.errors.get('payer_gst_vat')">
								</text-input>
							</div>
							<div class="columns">
								<div class="column">
									<text-input  v-model="form.harm_comm_code" :defaultValue="form.harm_comm_code" 
													label="HARM Comm Code" 
													:required="true"
													name="harm_comm_code"
													type="text"
													seen="false"
													:editable="true"
													:error="form.errors.get('harm_comm_code')">
									</text-input>
								</div>
								<div class="column">
									<text-input  v-model="form.trade_term" :defaultValue="form.trade_term" 
													label="Term of Trade" 
													:required="true"
													name="trade_term"
													type="text"
													seen="false"
													:editable="true"
													:error="form.errors.get('trade_term')">
									</text-input>
								</div>
							</div>
							<div class="field">
									<text-input  v-model="form.payment_term" :defaultValue="form.payment_term" 
													label="Term of Payment" 
													:required="true"
													name="payment_term"
													type="text"
													seen="false"
													:editable="true"
													:error="form.errors.get('payment_term')">
									</text-input>
								</div>
							<div class="field">
								<textarea-input  v-model="form.export_reason" :defaultValue="form.export_reason" 
												label="Reason For Export" 
												:required="true"
												name="export_reason"
												type="text"
												seen="false"
												:editable="true"
												:error="form.errors.get('export_reason')"
												rows="2"
												cols="4">
								</textarea-input>
							</div>	
							<div class="columns">
								<div class="column is-narrow">
									<checkbox-input v-model="form.business" :defaultChecked="form.business"
												label="Business?" 
												:required="false"
												name="business"
												:editable="true"
												class="mt-10">
									</checkbox-input>
								</div>
							</div>	
						</div>
						
						<table class="table is-hoverable is-fullwidth is-responsive is-multirow">
							<thead>
								<th style="width: 320px;"></th>
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
											<text-input v-if="productRows[index].product" :defaultValue="productRows[index].product.total_usable_quantity"
						          						label="Available quantity"
						          						:required="true"
						          						type="number"
						          						:editable="false"
						          						:hideLabel="false"
						          						name="quantity">
						          			</text-input>
										</td>
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].quantity" :defaultValue="productRows[index].quantity"
						          						label="Outbound quantity"
						          						:required="true"
						          						type="number"
						          						:editable="true"
						          						:hideLabel="false"
						          						@input="updateTotalValue(index)"
						          						:error="form.errors.get('outbound_products.' + index)"
						          						name="quantity">
						          			</text-input>
										</td>
										<td>
											<textarea-input v-if="productRows[index]" v-model="productRows[index].remarks" :defaultValue="productRows[index].remarks"
						          						:label="'Remarks'"
						          						:required="false"
						          						type="text"
						          						:editable="true"
						          						:hideLabel="false"
						          						name="remarks"
						          						rows="1"
						          						cols="4">
						          			</textarea-input>
										</td>
										
										<td :rowspan="!isMalaysiaData ? 2 : 1">
											<div class="button is-danger is-small" @click="removeRow(index)">
												<i class="fa fa-minus"></i>
												<span class="pl-5">Remove product</span>
											</div>
										</td>
									</tr>
									<tr v-if="!isMalaysiaData">
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].unit_value" :defaultValue="productRows[index].unit_value"
						          						label="Unit Value (RM)"
						          						:required="true"
						          						type="number"
						          						:editable="true"
						          						:hideLabel="false"
						          						name="unit_value"
						          						@input="updateTotalValue(index)">
						          			</text-input>
										</td>
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].total_value" :defaultValue="productRows[index].total_value"
						          						label="Total Value (RM)"
						          						:required="true"
						          						type="text"
						          						:editable="false"
						          						:hideLabel="false"
						          						name="total_value">
						          			</text-input>
										</td>
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].weight" :defaultValue="productRows[index].weight"
						          						label="Weight (kg)"
						          						:required="true"
						          						type="text"
						          						:editable="true"
						          						:hideLabel="false"
						          						name="weight">
						          			</text-input>
										</td>
										<td>
											<text-input v-if="productRows[index]" v-model="productRows[index].manufacture_country" :defaultValue="productRows[index].manufacture_country"
						          						label="Manufacture Country"
						          						:required="true"
						          						type="text"
						          						:editable="true"
						          						:hideLabel="false"
						          						name="manufacture_country">
						          			</text-input>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
			          	
						<p class="is-danger header" v-text="form.errors.get('outbound_products')"></p>

						<div class="field mt-10">
					    	<image-input v-model="invoiceSlip" :defaultImage="invoiceSlip"
					    				@loaded="changeInvoiceSlipImage"
					    				label="invoice slip"
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
				userField: [
					{name: 'id', title: '#'},
					{name: 'recipient_name', sortField: 'outbounds.recipient_name', title: 'Recipient'},
					{name: 'created_at', sortField: 'created_at', title: 'Order date'},
					{name: 'courier', sortField: 'couriers.name', title: 'Courier'},
					{name: 'amount_insured', sortField: 'amount_insured', title: 'Insurance'},
					{name: 'process_status', callback: 'outboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:outbounds-actions', title: 'Actions'}	
				],
				userSearchables: "process_status,outbounds.recipient_name",
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
					customer_id: '',
					insurance: '',
					amount_insured: '0',
					courier_id: '',
					outbound_products: [],
					invoice_slip: '',
					payer_gst_vat: '',
					harm_comm_code: '',
					payment_term: '',
					trade_term: '',
					export_reason: '',
					business: '',
					is_malaysia: false,
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
				errorForProducts: '',
				invoiceSlip: {name: 'No file selected'}

			};
		},

		mounted() {
			this.getProducts();
			this.$events.on('viewOutbound', data => this.view(data));
		},

		methods: {
			isMalaysia(data) {
				this.isMalaysiaData = data.toLowerCase() == "malaysia" || data == '';
			},

			getProducts() {
				axios.get('internal/products/selector')
					.then(response => this.setProducts(response));
			},

			setProducts(response) {
				this.productsOptions = response.data;
				this.getCouriers();
			},

			getCustomers() {
				axios.get('internal/customers')
					.then(response => this.setCustomers(response));
			},

			setCustomers(response) {
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
				this.productRows.push({ product: null, quantity: '', remarks: "", unit_value: "", total_value: "", weight: "", manufacture_country: ""});
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

						if(product > -1) {
							this.form.outbound_products[product].quantity = this.form.outbound_products[product].quantity + parseInt(element.quantity);
							this.form.outbound_products[product].unit_value = parseInt(element.unit_value);
							this.form.outbound_products[product].total_value = this.form.outbound_products[product].quantity * parseInt(element.unit_value);
							this.form.outbound_products[product].weight = this.form.outbound_products[product].weight + parseInt(element.weight);
							this.form.outbound_products[product].manufacture_country = element.manufacture_country;
						}
						else {
							this.form.outbound_products.push({id: element.product.id, 
															quantity: parseInt(element.quantity), 
															remarks: element.remarks, 
															unit_value: parseInt(element.unit_value), 
															total_value: parseInt(element.total_value), 
															weight: parseInt(element.weight), 
															manufacture_country: element.manufacture_country});
						}
					}
				}.bind(this));							
			},

			submit() {
				this.processProduct();

				this.form.is_malaysia = this.isMalaysiaData;

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
				this.errorForProducts = this.form.errors.get('outbound_products');
			},

			onSuccess(data) {
				this.productRows = [];
				this.invoiceSlip = {name: 'No file selected'};
				this.dialogActive = false;
				this.back();
				this.$refs.outbounds.refreshTable();
				this.getProducts();
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
					obj["remarks"] = '';
					return obj;
				});

				this.form.errors.clear('outbound_products');
				
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
					this.form.recipient_country.toLowerCase() == "malaysia"?
					this.isMalaysiaData = true : this.isMalaysiaData = false;
					this.form.is_malaysia = this.isMalaysiaData;
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
		},
	}
</script>