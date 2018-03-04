<template>
	<div>
		<transition name="slide-fade">
			<div class="card" v-if="!isPurchasing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Purchase history
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-primary" @click="isPurchasing = true">
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
			<div class="card" v-if="isPurchasing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								{{ dialogTitle }}
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-warning" @click="back()">
									<i class="fa fa-arrow-alt-circle-left"></i>
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
						
					</form>
				</div>

			</div>

		</transition>
		

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
					{name: 'created_at', sortField: 'created_at'},
					{name: 'price', sortField: 'price'}	
				],
				selectedPayment: '',
				isPurchasing: false,
				dialogActive: false,
				override: false,
				selectedLots: '',
				form: new Form({
					id: '',
					name: '',
				}),
				categories: ''
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));
			this.$events.on('delete', data => this.delete(data));
			this.getLotCategories();
		},

		methods: {
			getLotCategories() {
				axios.get('/internal/categories')
					.then(response => this.setLotCategories(response.data));
			},

			setLotCategories(data) {
				console.log(data);
				this.categories = data.data;
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