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
						<div class="level-right" v-if="!can_manage">
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
					@back="back">
			</product>
		</transition>

		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="onSubmit" 
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
			    				:error="form.errors.get('picture')">
			    	</image-input>
			    </div>
          	</form>

          	<template slot="footer">
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>
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
				}),
			};
		},

		mounted() {
			this.$events.on('edit', data => this.edit(data));
			this.$events.on('view', data => this.view(data));
		},

		methods: {
			

			submit() {
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
				this.form.name = data.name;
				this.form.height = data.height;
				this.form.width = data.width;
				this.form.length = data.length;
				this.form.is_dangerous = data.is_dangerous;
				this.form.is_fragile = data.is_fragile;

				this.productImage = {name: data.picture, src: data.picture};
				
				this.dialogActive = true;
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
				this.productImage = {name: 'No file selected'};
				this.selectedProduct = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedProduct
						? "Edit " + this.selectedProduct.name
						: "Create new product";
			},

			action() {
				let action = this.selectedProduct ? "update" : "store";
				return "/product/" + action;
			},

			fields() {
				let field = [
					{name: 'picture', callback: 'image', title: 'Image'},
					{name: 'sku', sortField: 'sku', title: 'SKU'},
					{name: 'product_name', sortField: 'product_name', title: 'Name'},
					{name: 'volume', title: 'Volume(cm³)'},
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