<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Lots
						</div>
					</div>
					<div class="level-right" v-if="can_manage">
						<div class="level-item">
							<button class="button is-primary" @click="modalOpen()">
								<i class="fa fa-plus-circle"></i>
								<span class="pl-5">Create new lot</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table-view ref="lots" 
							:fields="fields" 
							url="/internal/lots"
							:searchables="searchables"
							:detail="detailRow">	
				</table-view>
				<p class="has-text-grey is-italic">Click on the lot to view products in the lot currently</p>
			</div>
		</div>

		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="submit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)"
					@keyup.enter="submit">

	          	<div class="field">
	          		<text-input v-model="form.name" :defaultValue="form.name" 
								label="Name" 
								:required="true"
								name="name"
								type="text"
								:editable="true"
								:error="form.errors.get('name')"
								:focus="true">
					</text-input>
	          	</div>

	          	<div class="field mt-30">
	          		<selector-input v-model="selectedCategory" :defaultData="selectedCategory" 
									label="Category" 
									:required="true"
									name="category"
									:potentialData="categoriesOptions"
									@input="categoryUpdate($event)"
									:editable="true"
									placeholder="Select a category"
									:error="form.errors.get('category')">
					</selector-input>
	          	</div>

	          	<!-- <div class="field">
					<checkbox-input v-model="override" :defaultChecked="override"
								label="Overwrite volume" 
								:required="false"
								name="override"
								:editable="true">
					</checkbox-input>
				</div> -->

				<transition name="fade">
					<div v-if="override || this.form.volume || this.form.price">
			          	<div class="field">
			          		<text-input v-model="form.volume" :defaultValue="form.volume" 
										label="Volume (cm³)" 
										:required="true"
										name="volume"
										type="text"
										:editable="override"
										:error="form.errors.get('volume')">
							</text-input>
			          	</div>
			          	<div class="field">
			          		<text-input v-model="form.price" :defaultValue="form.price" 
										label="Price (RM)" 
										:required="true"
										name="price"
										type="text"
										:editable="override"
										:error="form.errors.get('price')">
							</text-input>
			          	</div>
			         </div>
	          	</transition>
          	</form>

          	<template slot="footer">
				<button class="button is-primary" :disabled="form.errors.any()" @click="submit">Submit</button>
          	</template>
		</modal>

		<modal :active="dialogUserActive" @close="dialogUserActive = false">
			<template slot="header">Manage owner</template>

			<form @submit.prevent="submitOwner" 
				@keydown="ownerForm.errors.clear($event.target.name)" 
				@input="ownerForm.errors.clear($event.target.name)"
				@keyup.enter="submitOwner">

	          	<div class="field">
					<selector-input v-model="selectedUser" :defaultData="selectedUser" 
									label="Owner" 
									:required="true"
									name="user_id"
									:potentialData="userOptions"
									@input="userUpdate($event)"
									:editable="true"
									placeholder="Select an owner"
									:error="ownerForm.errors.get('user_id')">
					</selector-input>
	          	</div>
          	</form>

          	<template slot="footer">
				<button class="button is-primary" @click="submitOwner">Reassign</button>
				<button class="button is-danger" @click="unassignOwner" v-if="selectedLot.user_id">Unassign</button>
          	</template>
		</modal>

		<modal :active="dialogPaymentActive" @close="dialogPaymentActive = false" v-if="selectedPayment">
			<template slot="header">Payment details</template>
			
			<loader v-if="isPaymentLoading"></loader>
			<div class="columns">
				<div class="column">
					<figure class="image">
						<img :src="selectedPayment.picture">
					</figure>
				</div>
				<div class="column">
					<text-input :defaultValue="selectedPayment.user.name"
							:editable="false"
							:required="false"
							label="Paid by"
							type="text">
					</text-input>
					<text-input :defaultValue="'RM' + selectedPayment.price"
							:editable="false"
							:required="false"
							label="Amount payable"
							type="text">
					</text-input>
					<text-input :defaultValue="selectedPayment.lots[0].rental_duration + ' months'"
							:editable="false"
							:required="false"
							label="Rental duration"
							type="text">
					</text-input>
					<div v-if="selectedPayment.lots[0].expired_at">
						<text-input :defaultValue="selectedPayment.lots[0].expired_at | date"
							:editable="false"
							:required="false"
							label="Expire date"
							type="text">
						</text-input>
					</div>
					
					<div class="is-divider" data-content="Lots purchased"></div>

					<table class="table is-hoverable is-fullwidth">
						<thead>
							<tr>
								<th>Name</th>
								<th>Price</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="lot in selectedPayment.lots">
								<td v-text="lot.name"></td>
								<td v-text="lot.price"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

          	<template slot="footer" v-if="can_manage">
				<button v-if="selectedPayment.status !== 'true'" class="button is-primary" :class="approveLoadingClass" @click="approvePayment">Approve</button>
          	</template>
		</modal>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: ['can_manage'],

		components: { TableView },

		data() {
			return {
				categories: [],
				categoriesOptions: [],
				lots: '',
				detailRow: 'LotDetailRow',
				searchables: "users.name,lots.name,categories.name,lots.volume",
				selectedLot: '',
				selectedCategory: '',
				dialogActive: false,
				dialogUserActive: false,
				dialogPaymentActive: false,
				override: false,
				form: new Form({
					id: '',
					name: '',
					volume: '',
					category: '',
					price: ''
				}),
				ownerForm: new Form({
					user_id: '',
					id: ''
				}),
				approveForm: new Form({
					id: ''
				}),
				userOptions: [],
				selectedUser: '',
				selectedPayment: '',
				isPaymentLoading: false,
				paymentApproving: false
			};
		},

		mounted() {
			this.fetchCategories();

			this.$events.on('edit', data => this.edit(data));
			this.$events.on('assign', data => this.editOwner(data));
			this.$events.on('approve', data => this.approveOwner(data));
		},

		methods: {
			fetchCategories() {
				axios.get('/internal/categories')
					.then(response => this.setCategories(response));
			},

			setCategories(response) {
				this.categories = response.data.data;
				this.categoriesOptions = this.categories.map(category =>{
					let obj = {};
					obj['label'] = category.name;
					obj['value'] = category.id;
					obj['volume'] = category.volume;
					obj['price'] = category.price;
					return obj;
				});

				this.fetchUsers();
			},

			fetchUsers() {
				axios.get('/internal/users')
					.then(response => this.setUsers(response));
			},

			approveOwner(data) {
				this.dialogPaymentActive = true;
				this.isPaymentLoading = true;
				axios.get('/internal/payment/' + data.id)
					.then(response => this.setPayment(response));
			},

			setPayment(response) {
				this.selectedPayment = response.data;
				this.isPaymentLoading = false;
			},

			approvePayment() {
				this.submitting = true;
				this.approveForm.id = this.selectedPayment.id;
				this.approveForm.post('/payment/approve')
					.then(response => this.onSuccess());
			},

			setUsers(response) {
				this.userOptions = response.data.map(user => {
					let obj = {};
					obj['label'] = user.name;
					obj['value'] = user.id;
					return obj;
				});
			},

			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			submitOwner() {
				this.ownerForm.post('/lot/assign/' + this.selectedLot.id)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			unassignOwner() {
				this.ownerForm.post('/lot/unassign/' + this.selectedLot.id)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.dialogActive = false;
				this.dialogUserActive = false;
				this.dialogPaymentActive = false;
				this.$refs.lots.refreshTable();
			},

			onFail(error) {

			},

			edit(data) {
				this.selectedLot = data;
				this.form.id = data.id;
				this.form.name = data.name;
				this.form.volume = data.volume;
				this.form.category = data.category_id;
				this.form.price = data.price;
				this.selectedCategory = {
					label: data.category_name,
					value: data.category_id,
					volume: data.category_volume,
					price: data.category_price
				};
				this.dialogActive = true;
				//this.override = data.category_volume !== data.volume || data.category_price !== data.price;

			},

			editOwner(data) {
				this.ownerForm.reset();
				this.selectedLot = data;
				if(data.user_name)
					this.selectedUser = {
						label: data.user_name,
						value: data.user_id
					};
				else
					this.selectedUser = '';
				this.dialogUserActive = true;
			},

			categoryUpdate(data) {
				this.selectedCategory = data;
				this.form.category = data.value;
				if(!this.override) {
					this.form.volume = data.volume; 
					this.form.price = data.price; 
				}
				this.form.errors.clear('price');
				this.form.errors.clear('volume');
				this.form.errors.clear('category');
			},

			userUpdate(data) {
				this.selectedUser = data;
				this.ownerForm.user_id = data.value;
			},

			modalOpen() {
				this.form.reset();
				this.selectedCategory = '';
				this.selectedLot = '';
				this.dialogActive = true;
				this.override = false;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedLot
						? "Edit " + this.selectedLot.name
						: "Create new lot";
			},

			action() {
				let action = this.selectedLot ? "update" : "store";
				return "/lot/" + action;
			},

			fields() {
				let displayFields = [
					{name: 'name', sortField: 'name'},
					
					{name: 'category_name', sortField: 'category_name', title: 'Category'},
					{name: 'usage', title: 'Usage (cm³)'},
					// {name: 'price', sortField: 'price', title: 'Price (RM)'},
					{name: 'products.length', title: 'No. item'},
					{name: 'expired_at', sortField: 'expired_at', title: 'Rental expire', callback: 'date'},
					
				];

				if(this.can_manage)
				{
					displayFields.push({name: 'user_name', sortField: 'user_name', title: 'Customer', callback: 'customer'});
					displayFields.push({name: '__component:lots-actions', title: 'Actions'});
				}
				else
				{
					
					displayFields.push({name: 'lot_status', sortField: 'lot_status', title: 'Rental status', callback: 'purchaseStatusLabel'});
				}
				return displayFields;
			},

			approveLoadingClass() {
				return this.paymentApproving ? 'is-loading' : '';
			},
		}
	}
</script>