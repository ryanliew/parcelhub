<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Purchase history
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" @click="modalOpen()">
								<i class="fa fa-plus-circle"></i>
								<span class="pl-5">Purchase lots</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table-view ref="couriers" 
							:fields="fields" 
							url="/internal/payments">	
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
          	</form>

          	<template slot="footer">
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>

		<modal :active="isDeleting" @close="isDeleting = false">
			<template slot="header">Delete payment</template>
			
			Are you sure you want to delete <span v-text="selectedPayment.name"></span>?

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
				fields: [
					{name: 'name', sortField: 'name'},	
				],
				selectedPayment: '',
				dialogActive: false,
				override: false,
				lots: '',
				form: new Form({
					id: '',
					name: '',
				}),
				isDeleting: false
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));
			this.$events.on('delete', data => this.delete(data));
			this.getLots();
		},

		methods: {
			getLots() {
				axios.get('/internal/lots')
					.then(response => this.setLots(response.data));
			},

			setLots(data) {
				console.log(data.data);
				this.lots = _.filter(data.data, function(lot){ return !lot.user_name });
			},

			submit() {
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.dialogActive = false;
				this.$refs.couriers.refreshTable();
			},

			onFail() {

			},

			edit(data) {
				this.selectedPayment = data;
				this.form.id = data.id;
				this.form.name = data.name;
				
				this.dialogActive = true;
			},

			delete(data) {
				this.selectedPayment = data;
				this.isDeleting = true;
			},

			confirmDeletion() {
				axios.get('/courier/delete/' + this.selectedPayment.id)
					.then(response => this.deleteSuccess(response));
			},

			deleteSuccess(response) {
				this.isDeleting = false;
				flash(response.message);
				this.$refs.couriers.refreshTable();
			},

			modalOpen() {
				this.form.reset();
				this.selectedPayment = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return this.selectedPayment
						? "Edit " + this.selectedPayment.name
						: "Create new courier";
			},

			action() {
				let action = this.selectedPayment ? "update" : "store";
				return "/courier/" + action;
			}
		}
	}
</script>