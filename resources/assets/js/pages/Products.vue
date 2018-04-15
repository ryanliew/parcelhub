<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Products
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create new product</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="products" 
								:fields="fields" 
								url="/internal/products"
								:searchables="searchables"
								:detail="detailRow">
					</table-view>
					<p class="has-text-grey is-italic">Click on the product row to view current placed lots</p>
				</div>
			</div>
			<product :product="selectedProduct" v-else
					@back="back"
					:can_manage="can_manage">
			</product>
		</transition>

		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="submit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)"
					@keyup.enter="submit">
				
				<div class="field">
	          		<text-input v-model="form.sku" :defaultValue="form.sku" 
								label="SKU" 
								:required="true"
								name="sku"
								type="text"
								:editable="true"
								:error="form.errors.get('sku')"
								:focus="true">
					</text-input>
	          	</div>

	          	<div class="field">
	          		<text-input v-model="form.name" :defaultValue="form.name" 
								label="Name" 
								:required="true"
								name="name"
								type="text"
								:editable="true"
								:error="form.errors.get('name')">
					</text-input>
	          	</div>

	          	<div class="columns">
	          		<div class="column">
			          	<div class="field">
			          		<text-input v-model="form.height" :defaultValue="form.height" 
										label="Height (cm)" 
										:required="true"
										name="height"
										type="text"
										:editable="true"
										:error="form.errors.get('height')">
							</text-input>
			          	</div>
			        </div>
			        <div class="column">
			          	<div class="field">
			          		<text-input v-model="form.width" :defaultValue="form.width" 
										label="Width (cm)" 
										:required="true"
										name="width"
										type="text"
										:editable="true"
										:error="form.errors.get('width')">
							</text-input>
			          	</div>
			        </div>
			        <div class="column">
			          	<div class="field">
			          		<text-input v-model="form.length" :defaultValue="form.length" 
										label="Length (cm)" 
										:required="true"
										name="length"
										type="text"
										:editable="true"
										:error="form.errors.get('length')">
							</text-input>
			          	</div>
			        </div>
			    </div>
				<p class="heading">Product attributes</p>		
				<div class="is-pulled-left">
			    	<checkbox-input v-model="form.is_dangerous" :defaultChecked="form.is_dangerous"
			    					label="Dangerous"
			    					name="is_dangerous"
			    					:editable="true">
			    	</checkbox-input>
			    </div>
			    <div class="is-pulled-left pl-5">
			    	<checkbox-input v-model="form.is_fragile" :defaultChecked="form.is_fragile"
			    					label="Fragile"
			    					name="is_fragile"
			    					:editable="true">
			    	</checkbox-input>
			    </div>
			    <div class="is-clearfix"></div>
				
			    <div class="field mt-10">
			    	<image-input v-model="productImage" :defaultImage="productImage"
			    				@loaded="changeProductImage"
			    				label="product image"
			    				name="picture"
			    				accept="image/*"
			    				:error="form.errors.get('picture')">
			    	</image-input>
			    </div>

			    <div class="field" v-if="can_manage && !selectedProduct">
					<selector-input v-model="selectedUser" :defaultData="selectedUser"
							label="Product owner"
							name="user_id"
							:required="true"
							:potentialData="userOptions"
							@input="userUpdate($event)"
							:editable="true"
							placeholder="Select product owner"
							:error="form.errors.get('user_id')">

					</selector-input>
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

        <confirmation :isConfirming="confirmDelete"
        				title="Confirmation"
        				message="Confirm delete product?"
        				@close="confirmDelete = false"
        				@confirm="onDelete">
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
				detailRow: 'ProductDetailRow',
				selectedProduct: '',
				isViewing: false,
				dialogActive: false,
				override: false,
				productImage: {name: 'No file selected'},
				form: new Form({
					id: '',
					name: '',
					height: '',
					width: '',
					length: '',
					sku: '',
					picture: '',
					is_dangerous: '',
					is_fragile: '',
					user_id: '',
				}),
				deleteForm: new Form({

				}),
				confirmSubmit: false,
				confirmDelete: false,
				userOptions: [],
				selectedUser: false
			};
		},

		mounted() {
			this.$events.on('edit', data => this.edit(data));
			this.$events.on('view', data => this.view(data));
			this.$events.on('delete', data => this.delete(data));
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
				this.form.user_id = data.value;
			},

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
				this.$refs.products.refreshTable();
			},

			onFail(error) {

			},

			edit(data) {
				this.selectedProduct = data;
				this.form.id = data.id;
				this.form.sku = data.sku;
				this.form.name = data.product_name;
				this.form.height = data.height;
				this.form.width = data.width;
				this.form.length = data.length;
				this.form.is_dangerous = data.is_dangerous;
				this.form.is_fragile = data.is_fragile;
				this.form.user_id = 1;

				this.productImage = {name: data.picture, src: data.picture};
				
				this.dialogActive = true;
			},

			delete(data) {
				this.selectedProduct = data;
				this.confirmDelete = true;
			},

			onDelete() {
				this.confirmDelete = false;
				this.deleteForm.delete('/internal/products/' + this.selectedProduct.id)
					.then(response => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			view(data) {
				this.selectedProduct = data;
				this.isViewing = true;
			},

			back() {
				this.selectedProduct = '';
				this.isViewing = false;
			},

			changeProductImage(e) {
				//console.log(e);
				this.productImage = { src: e.src, file: e.file };
				this.form.picture = e.file;	
			},

			modalOpen() {
				this.form.reset();
				this.getUsers();
				this.productImage = {name: 'No file selected'};
				this.selectedProduct = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedProduct
						? "Edit " + this.selectedProduct.product_name
						: "Create new product";
			},

			action() {
				let action = this.selectedProduct ? "update" : "store";
				return "/product/" + action;
			},

			confirmationMessage() {
				return this.selectedProduct
						? "Confirm editing the product information?"
						: "Confirm adding new product?";
			},

			fields() {
				let field = [
					
					{name: 'sku', sortField: 'sku', title: 'SKU'},
					{name: 'picture', callback: 'image', title: 'Image'},
					{name: 'product_name', sortField: 'product_name', title: 'Name'},
					{name: 'volume', title: 'Volume(cm³)'},
					{name: 'total_quantity', title: 'Stock'},
					{name: 'is_dangerous', title: 'Dangerous', sortField: 'is_dangerous', callback: 'dangerousTag'},
					{name: 'is_fragile', title: 'Fragile', sortField: 'is_fragile', callback: 'fragileTag'}	
				];

				if(this.can_manage)
				{
					field.push({name: 'user_name', title: 'Owner', sortField: 'user_name'});
				}

				field.push({name: '__component:products-actions', title: 'Actions'});

				return field;
			},

			searchables() {
				let searchable = "products.name,sku";

				if(this.can_manage)
				{
					searchable += ",users.name";
				}

				return searchable;
			}
		}
	}
</script>