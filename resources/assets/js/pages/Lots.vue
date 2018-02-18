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
							:searchables="searchables">	
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
		          	<div class="field" v-if="override || this.form.volume">
		          		<text-input v-model="form.volume" :defaultValue="form.volume" 
									label="Volume(cm³)" 
									:required="true"
									name="volume"
									type="text"
									:editable="override"
									:error="form.errors.get('volume')">
						</text-input>
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
					{name: 'category_name', sortField: 'category_name', title: 'Category'},
					{name: 'volume', sortField: 'Volume', title: 'Volume (cm³)'},
					{name: 'user_name', sortField: 'user_name', title: 'Customer'},
					{name: '__component:lots-actions', title: 'Actions'}	
				],
				searchables: "users.name,lots.name,categories.name,lots.volume",
				selectedLot: '',
				selectedCategory: '',
				dialogActive: false,
				override: false,
				form: new Form({
					id: '',
					name: '',
					volume: '',
					category: ''
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
					volume: data.category_volume
				};
				this.dialogActive = true;
				this.override = data.category_volume !== data.volume;
			},

			categoryUpdate(data) {
				this.selectedCategory = data;
				this.form.category = data.value;
				this.form.volume = data.volume; 
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