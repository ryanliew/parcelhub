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
					<div class="level-right">
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
			</div>
		</div>

		<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="onSubmit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)">

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
									:error="form.errors.get('category_id')">
					</selector-input>
	          	</div>

	          	<div class="field">
					<checkbox-input v-model="override" :defaultChecked="override"
								label="Overwrite volume" 
								:required="false"
								name="override"
								:editable="true">
					</checkbox-input>
				</div>

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
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: [''],

		components: { TableView },

		data() {
			return {
				categories: [],
				categoriesOptions: [],
				lots: '',
				fields: [
					{name: 'name', sortField: 'name'},
					{name: 'left_volume', sortField: 'left_volume', title: 'Volume left (cm³)'},
					{name: 'category_name', sortField: 'category_name', title: 'Category'},
					{name: 'volume', sortField: 'volume', title: 'Volume (cm³)'},
					{name: 'price', sortField: 'price', title: 'Price (RM)'},
					{name: 'user_name', sortField: 'user_name', title: 'Customer'},
					{name: '__component:lots-actions', title: 'Actions'}	
				],
				detailRow: 'LotDetailRow',
				searchables: "users.name,lots.name,categories.name,lots.volume",
				selectedLot: '',
				selectedCategory: '',
				dialogActive: false,
				override: false,
				form: new Form({
					id: '',
					name: '',
					volume: '',
					category: '',
					price: ''
				}),
			};
		},

		mounted() {
			this.fetchCategories();
			this.$events.on('edit', data => this.edit(data));
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
			},

			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.dialogActive = false;
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
				this.selectedCategory = {
					label: data.category_name,
					value: data.category_id,
					volume: data.category_volume,
					price: data.category_price
				};
				this.dialogActive = true;
				this.override = data.category_volume !== data.volume || data.category_price !== data.price;
			},

			categoryUpdate(data) {
				this.selectedCategory = data;
				this.form.category = data.value;
				this.form.volume = data.volume; 
				this.form.price = data.price; 
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
			}
		}
	}
</script>