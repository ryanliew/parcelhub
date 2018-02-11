<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Couriers
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" @click="modalOpen()">
								<i class="fa fa-plus-circle"></i>
								<span class="pl-5">Create new courier</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table-view ref="couriers" 
							:fields="fields" 
							url="/internal/couriers">	
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
								:error="form.errors.get('name')">
					</text-input>
	          	</div>
          	</form>

          	<template slot="footer">
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>

		<modal :active="isDeleting" @close="isDeleting = false">
			<template slot="header">Delete courier</template>
			
			Are you sure you want to delete <span v-text="selectedCourier.name"></span>?

			<template slot="footer">
				<button class="button is-primary" @click="confirmDeletion">Confirm</button>
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
				couriers: '',
				fields: [
					{name: 'name', sortField: 'name'},
					{name: '__component:couriers-actions', title: 'Actions'}	
				],
				selectedCourier: '',
				dialogActive: false,
				override: false,
				form: new Form({
					id: '',
					name: '',
				}),
				isDeleting: false
			};
		},

		mounted() {
			this.$events.on('edit', data => this.edit(data));
			this.$events.on('delete', data => this.delete(data));
		},

		methods: {
			submit() {
				this.form.post(this.action)
					.then(this.onSuccess());
			},

			onSuccess() {
				this.dialogActive = false;
				this.$refs.couriers.refreshTable();
			},

			edit(data) {
				this.selectedCourier = data;
				this.form.id = data.id;
				this.form.name = data.name;
				
				this.dialogActive = true;
			},

			delete(data) {
				this.selectedCourier = data;
				this.isDeleting = true;
			},

			confirmDeletion() {
				axios.get('/courier/delete/' + this.selectedCourier.id)
					.then(response => this.deleteSuccess(response));
			},

			deleteSuccess(response) {
				this.isDeleting = false;
				flash(response.message);
				this.$refs.couriers.refreshTable();
			},

			modalOpen() {
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedCourier
						? "Edit " + this.selectedCourier.name
						: "Create new courier";
			},

			action() {
				let action = this.selectedCourier ? "update" : "store";
				return "/courier/" + action;
			}
		}
	}
</script>