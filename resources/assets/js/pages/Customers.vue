<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Customers
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Add customer</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="customers" 
								:fields="fields" 
								url="/internal/customers"
								:searchables="searchables">
					</table-view>
				</div>
			</div>
		</transition>

		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="submit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)"
					@keyup.enter="submit">
				
				<div class="columns">
		         	<div class="column">
		          		<text-input v-model="form.customer_name" :defaultValue="form.customer_name" 
									label="Customer name" 
									:required="true"
									name="customer_name"
									type="text"
									:editable="true"
									:error="form.errors.get('customer_name')"
									:focus="true">
						</text-input>
					</div>
					<div class="column">
						<text-input v-model="form.customer_phone" :defaultValue="form.customer_phone" 
									label="Customer phone" 
									:required="true"
									name="customer_phone"
									type="text"
									:editable="true"
									:error="form.errors.get('customer_phone')">
						</text-input>
					</div>
				</div>
				<div class="field">
					<text-input v-model="form.customer_address" :defaultValue="form.customer_address" 
									label="Customer address line 1" 
									:required="true"
									name="customer_address"
									type="text"
									:editable="true"
									:error="form.errors.get('customer_address')">
					</text-input>
				</div>
				<div class="field">
					<text-input v-model="form.customer_address_2" :defaultValue="form.customer_address_2" 
									label="Customer address line 2" 
									:required="false"
									name="customer_address_2"
									type="text"
									:editable="true"
									:error="form.errors.get('customer_address_2')">
					</text-input>
				</div>
				<div class="columns">
					<div class="column">
						<text-input v-model="form.customer_postcode" :defaultValue="form.customer_postcode" 
										label="Customer postcode" 
										:required="true"
										name="customer_postcode"
										type="text"
										:editable="true"
										:error="form.errors.get('customer_postcode')">
						</text-input>
					</div>
					<div class="column">
						<text-input v-model="form.customer_state" :defaultValue="form.customer_state" 
										label="Customer state" 
										:required="true"
										name="customer_state"
										type="text"
										:editable="true"
										:error="form.errors.get('customer_state')">
						</text-input>
					</div>
					<div class="column">
						<text-input v-model="form.customer_country" :defaultValue="form.customer_country" 
										label="Customer country" 
										:required="true"
										name="customer_country"
										type="text"
										:editable="true"
										:error="form.errors.get('customer_country')">
						</text-input>
					</div>
				</div>	
          	</form>

          	<template slot="footer">
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>

		<confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				:message="confirmationMessage"
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Product from '../objects/Product.vue';

	export default {
		props: ['can_manage'],

		components: { TableView, Product },

		data() {
			return {
				selectedCustomer: '',
				isViewing: false,
				dialogActive: false,
				form: new Form({
					id: '',
					customer_name: '',
					customer_phone: '',
					customer_address: '',
					customer_address_2: '',
					customer_state: '',
					customer_postcode: '',
					customer_country: '',
				}),
				deleteForm: new Form({

				}),
				confirmSubmit: false,
				confirmDelete: false,
			};
		},

		mounted() {
			this.$events.on('edit', data => this.edit(data));
			// this.$events.on('view', data => this.view(data));
			// this.$events.on('delete', data => this.delete(data));
		},

		methods: {

			submit() {
				this.confirmSubmit = true;
			},

			onSubmit() {
				this.confirmSubmit = false;
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.dialogActive = false;
				this.$refs.customers.refreshTable();
			},

			onFail(error) {

			},

			edit(data) {
				this.selectedCustomer = data;
				this.form.id = data.id;
				this.form.customer_name = data.customer_name;
				this.form.customer_phone = data.customer_phone;
				this.form.customer_address = data.customer_address;
				this.form.customer_address_2 = data.customer_address_2;
				this.form.customer_state = data.customer_state;
				this.form.customer_postcode = data.customer_postcode;
				this.form.customer_country = data.customer_country;
				
				this.dialogActive = true;
			},

			back() {
				this.selectedCustomer = '';
				this.isViewing = false;
			},

			modalOpen() {
				this.form.reset();
				this.selectedCustomer = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedCustomer
						? "Edit " + this.selectedCustomer.customer_name
						: "Add customer";
			},

			action() {
				let action = this.selectedCustomer ? "update" : "store";
				return "/customer/" + action;
			},

			confirmationMessage() {
				return this.selectedCustomer
						? "Confirm editing the customer information?"
						: "Confirm adding new customer?";
			},

			fields() {
				let field = [
					{name: 'customer_name', title: 'Customer name'},
					{name: '__component:customers-actions', title: 'Actions'}
				];

				return field;
			},

			searchables() {
				let searchable = "customer_name";

				return searchable;
			}
		}
	}
</script>