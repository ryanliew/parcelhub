<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Lots categories
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" @click="modalOpen()">
								<i class="fa fa-plus-circle"></i>
								<span class="pl-5">Create new category</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table-view ref="categories" 
							:fields="fields" url="/internal/categories">
					
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
	          	<div class="field">
	          		<text-input v-model="form.volume" :defaultValue="form.volume" 
								label="Volume(cm³)" 
								:required="true"
								name="volume"
								type="text"
								:editable="true"
								:error="form.errors.get('volume')">
					</text-input>
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

	export default {
		props: [''],

		components: { TableView },

		data() {
			return {
				fields: [
					{name: 'name', sortField: 'name'},
					{name: 'volume', sortFiled: 'volume', title: 'Volume (cm³)'},
					{name: '__component:categories-actions', title: 'Actions'}	
				],
				selectedCategory: '',
				dialogActive: false,
				form: new Form({
					id: '',
					name: '',
					volume: ''
				}),
			};
		},

		mounted() {
			this.$events.on('edit', data => this.edit(data));
		},

		methods: {
			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));;
			},

			onSuccess() {
				this.dialogActive = false;
				this.$refs.categories.refreshTable();
			},

			onFail() {

			},

			edit(data){
				this.selectedCategory = data;
				this.form.id = data.id;
				this.form.name = data.name;
				this.form.volume = data.volume;
				this.dialogActive = true;
			},

			modalOpen() {
				this.form.reset();
				this.selectedCategory = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedCategory 
						? "Edit " + this.selectedCategory.name
						: "Create new category";
			},

			action() {
				let action = this.selectedCategory ? "update" : "store";
				return "/category/" + action;
			}
		}
	}
</script>