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
									<i class="fa fa-arrow-circle-left"></i>
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
	
						<div class="accordions">
							<Accordion 
								v-for="category in categories" 
								:key="category.id"
								v-if="availableLots(category).length > 0">
								<template slot="title">
									{{ category.name }}
								</template>
								<table class="table is-hoverable is-fullwidth">
									<thead>
										<tr>
											<th>Name</th>
											<th>Volume(cm³)</th>
											<th>Price(RM)</th>
											<th>Purchase</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(lot, index) in availableLots(category)" v-if="lot.user_id == null">
											<td>
												{{ lot.name }}
											</td>
											<td>
												{{ lot.volume }}
											</td>
											<td>
												{{ lot.price }}
											</td>
											<td>
												<checkbox-input
													v-model="selectableLots[index].selected"
													:defaultChecked="selectableLots[index].selected"
													:required="false"
													:name="'lot-' + lot.id"
													:editable="true"
													@input="toggleCheck(lot)">
												</checkbox-input>
											</td>
										</tr>
									</tbody>
								</table>
							</Accordion>
						</div>
						
						<hr>

						<div class="level">
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Lots purchased
									</p>
									<p class="title">
										{{ totalLots }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total price (RM)
									</p>
									<p class="title">
										{{ totalPrice }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total volume (cm³)
									</p>
									<p class="title">
										{{ totalVolume }}
									</p>
								</div>
							</div>
						</div>
					</form>
				</div>

			</div>

		</transition>
		

	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Accordion from '../components/Accordion.vue';
	export default {
		props: [''],

		components: { TableView, Accordion },

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
				form: new Form({
					id: '',
					name: '',
				}),
				categories: '',
				selectableLots: [],
				totalLots: 0,
				totalVolume: 0,
				totalPrice: 0,
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
				this.categories = data.data;
				this.categories.forEach(function(category){
					category.lots.forEach(function(lot){
						if(lot.user_id == null)
						{
							lot.selected = false;
							this.selectableLots.push(lot);
						}
					}.bind(this))
				}.bind(this));
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

			toggleCheck(lot){
				if(lot.selected){
					this.totalVolume = parseInt(this.totalVolume + lot.volume);
					this.totalPrice += lot.price;
					this.totalLots++;
				}
				else{
					this.totalVolume = parseInt(this.totalVolume - lot.volume);
					this.totalPrice -= lot.price;
					this.totalLots--;
				}

			},

			availableLots(category) {
				return _.filter(category.lots, {'user_id' : null});
			},

			modalOpen() {
				this.form.reset();
				this.selectedPayment = '';
				this.dialogActive = true;
			}
		},

		computed: {
			dialogTitle() {
				return "Purchase lot";
			},

			action() {
				return "/payment/store" + action;
			},
		}
	}
</script>