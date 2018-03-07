<template>
	<div>
		<transition name="slide-fade">
			<div class="card" v-if="!isPurchasing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								{{ mainTitle }}
							</div>
						</div>
						<div class="level-right" v-if="!can_manage">
							<div class="level-item">
								<button class="button is-primary is-tooltip-danger is-tooltip-left" :class="tooltipClass" :disabled="!canPurchase" @click="modalOpen" data-tooltip="Sold out temporarily">
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
		</transition>
		<transition name="slide-fade">
			<div class="card" v-if="isPurchasing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Purchase lots
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
						@keyup.enter="submit"
						v-if="this.selectableLots.length > 0">

						<div class="level">
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Lots count
									</p>
									<p class="title">
										{{ totalLots }}
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
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Price (RM)
									</p>
									<p class="title">
										{{ subPrice }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total Price (RM)
									</p>
									<p class="title">
										{{ totalPrice }}
									</p>
								</div>
							</div>
						</div>

						<div class="is-divider" data-content="SELECT LOTS"></div>
						
						<table class="table is-hoverable is-fullwidth">
							<thead>
								<th>Lot type</th>
								<th>Price</th>
								<th>Volume</th>
								<th>Quantity</th>
							</thead>
							<tbody>
								<tr v-for="(category, index) in categories">
									<td>{{ category.name }}</td>
									<td>{{ category.price }}</td>
									<td>{{ category.volume }}</td>
									<td v-if="availableLots(category).length > 0">
										<text-input v-model="categories[index].quantity" :defaultValue="categories[index].quantity"
				          						:label="'Quantity'"
				          						:required="true"
				          						type="number"
				          						:editable="true"
				          						:hideLabel="true"
				          						name="quantity"
				          						@input="updateTotals(category)"
				          						:error="categories[index].error"
				          						:ref="'category-'+category.id">
				          				</text-input>
									</td>
									<td v-else>Sold out</td>
								</tr>
							</tbody>
						</table>
						<!-- <div class="accordions">
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
											<th>Price(RM) / Month</th>
											<th>Purchase</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="lot in availableLots(category)" v-if="lot.user_id == null">
											<td>
												{{ lot.lot.name }}
											</td>
											<td>
												{{ lot.lot.volume }}
											</td>
											<td>
												{{ lot.lot.price }}
											</td>
											<td>
												<checkbox-input
													v-model="selectableLots[lot.index].selected"
													:defaultChecked="selectableLots[lot.index].selected"
													:required="false"
													:name="'lot-' + lot.lot.id"
													:editable="true"
													:index="index"
													@input="toggleCheck(lot.lot)">
												</checkbox-input>
											</td>
										</tr>
									</tbody>
								</table>
							</Accordion>
						</div> -->
						
						<div class="is-divider" data-content="FILL IN DETAILS"></div>
						
						<div class="field">
							<text-input
								v-model="rental_duration"
								:defaultValue="rental_duration"
								:required="true"
								label="Rental duration (months)"
								name="lot_purchases.0.rental_duration"
								type="number"
								:editable="true"
								:error="form.errors.get('lot_purchases.0.rental_duration') ? 'Rental duration is required' : ''">
							</text-input>
						</div>

						<div class="field mt-10">
					    	<image-input v-model="paymentSlip" :defaultImage="paymentSlip"
					    				@loaded="changePaymentSlipImage"
					    				label="payment slip"
					    				name="payment_slip"
					    				:required="true"
					    				:error="form.errors.get('payment_slip')">
					    	</image-input>
					    </div>	
						
						<button type="submit" :disabled="!canSubmit" class="button is-primary is-tooltip-danger is-tooltip-right" :class="submitTooltipClass" :data-tooltip="submitTooltipText" >Submit</button>
					</form>
					<article class="message" v-else>
						<div class="message-body">
							We are currently fully occupied. Please try again later or contact our support team.
						</div>
					</article>
				</div>

			</div>

		</transition>
		
		<modal :active="isViewing" @close="isViewing = false" v-if="selectedPayment">
			<template slot="header">{{ dialogTitle }}</template>

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
				<button v-if="selectedPayment.status !== 'true'" class="button is-primary" :class="approveLoadingClass" @click="approve">Approve</button>
          	</template>
		</modal>
		
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Accordion from '../components/Accordion.vue';
	export default {
		props: ['can_manage'],

		components: { TableView, Accordion },

		data() {
			return {
				selectedPayment: '',
				isPurchasing: false,
				dialogActive: false,
				override: false,
				form: new Form({
					payment_slip: '',
					lot_purchases: '',
					price: ''
				}),
				approveForm: new Form({
					id: ''
				}),
				categories: '',
				selectableLots: [],
				rental_duration: '',
				totalLots: 0,
				totalVolume: 0,
				subPrice: 0,
				paymentSlip: {name: 'No file selected'},
				selectedPayment: '',
				isViewing: false,
				submitting: false,
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));

			this.getLotCategories();
		},

		methods: {
			getLotCategories() {
				axios.get('/internal/categories')
					.then(response => this.setLotCategories(response.data));
			},

			setLotCategories(data) {
				//this.categories = data.data;
				this.selectableLots = [];
				data.data.forEach(function(category){
					category.quantity = 0;
					category.error = '';
					category.lots.forEach(function(lot){
						if(lot.user_id == null)
						{
							lot.selected = false;
							this.selectableLots.push(lot);
						}
					}.bind(this))
				}.bind(this));
				this.categories = data.data;

				this.isPurchasing = this.getParameterByName('new') == 'true';
			},

			getParameterByName(name, url) {
			    if (!url) url = window.location.href;
			    name = name.replace(/[\[\]]/g, "\\$&");
			    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			        results = regex.exec(url);
			    if (!results) return null;
			    if (!results[2]) return '';
			    return decodeURIComponent(results[2].replace(/\+/g, " "));
			},

			view(data) {
				this.selectedPayment = data;
				this.isViewing = true;
			},

			approve() {
				this.submitting = true;
				this.approveForm.id = this.selectedPayment.id;
				this.approveForm.post('/payment/approve')
					.then(response => this.onSuccess());
			},

			back(){
				this.isPurchasing = false;
			},

			reset() {
				this.selectableLots.forEach(function(lot){
					lot.selected = false;
				});
				this.totalLots = 0;
				this.totalVolume = 0;
				this.subPrice = 0;
				this.rental_duration = '';
			},

			submit() {
				let selectedLots = [];
				this.categories.forEach(function(category){
					let lots = [];
					lots = _.take(category.lots, category.quantity);
					lots.forEach(function(lot){
						selectedLots.push(lot);
					});
				});

				//let selectedLots = _.filter(this.selectableLots, function(lot){ return lot.selected; });
				this.form.lot_purchases = selectedLots.map(function(lot) {
					let obj = {};
					obj['id'] = lot.id;
					obj['rental_duration'] = this.rental_duration;
					return obj;
				}.bind(this));

				this.form.price = this.totalPrice;
				
				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.isPurchasing = false;
				this.isViewing = false;
				this.submitting = false;
				this.$refs.payments.refreshTable();
			},

			onFail() {

			},

			changePaymentSlipImage(e) {
				//console.log(e);
				this.paymentSlip = { src: e.src, file: e.file };
				this.form.payment_slip = e.file;	
			},

			toggleCheck(lot){
				if(lot.selected){
					this.totalVolume = this.totalVolume + parseInt(lot.volume);
					this.subPrice += lot.price;
					this.totalLots++;
				}
				else{
					this.totalVolume = this.totalVolume - parseInt(lot.volume);
					this.subPrice -= lot.price;
					this.totalLots--;
				}

			},

			updateTotals(category) {
				category.error = "";
				let quantity = parseInt(category.quantity);
				let availableLotCount = this.availableLots(category).length;

				if( availableLotCount < quantity) {
					category.quantity = 0;
					category.error = "We only have " + availableLotCount + " of " + category.name + " lots left";
				}

				this.subPrice =_.sumBy(this.categories, function(category){ return  parseInt(category.quantity)* category.price; });
				this.totalVolume =_.sumBy(this.categories, function(category){ return parseInt(category.quantity) * category.volume; });
				this.totalLots =_.sumBy(this.categories, function(category){ return parseInt(category.quantity); });
			},

			availableLots(category) {
				let availableLots = [];
				this.selectableLots.forEach(function(lot, key){
					if(lot.category_id == category.id && lot.user_id == null)
					{
						availableLots.push({'lot': lot, 'index': key});
					}
				}.bind(category));
				return availableLots;
			},

			modalOpen() {
				this.form.reset();
				this.reset();
				this.isPurchasing = true;
			}
		},

		computed: {
			dialogTitle() {
				return "Purchase lot";
			},

			action() {
				return "/payment/purchase";
			},

			totalPrice() {
				return this.subPrice * this.rental_duration;
			},

			canSubmit() {
				return this.totalLots > 0 && !this.form.errors.any();
			},

			canPurchase() {
				return !this.selectableLots.length == 0;
			},

			tooltipClass() {
				return this.canPurchase ? '' : 'tooltip';
			},

			submitTooltipClass() {
				return this.canSubmit ? '' : 'tooltip';
			},

			submitTooltipText() {
				let text = '';

				if(!this.totalLots > 0){
					text = 'Please select at least one lot';
				}

				if(this.form.errors.any())
					text = 'There are errors in the form';

				return text;
			},

			dialogTitle() {
				return 'View payment #' + this.selectedPayment.id;
			},

			approveLoadingClass() {
				return this.submitting ? 'is-loading' : '';
			},

			mainTitle() {
				return this.can_manage ? 'Purchases' : 'Purchase history';
			},

			fields() {
				let displayFields = [
					{name: 'user.name', title: 'Made by'},
					{name: 'created_at', sortField: 'created_at', title: 'Purchase date', callback: 'date'},
					{name: 'price', sortField: 'price'},
					{name: 'status', sortField: 'status', title: 'Status', callback: 'purchaseStatusLabel'},
					{name: '__component:payments-actions', title: 'Actions'}
				];

				if(!this.can_manage)
				{
					displayFields = _.drop(displayFields);
				}

				return displayFields;
			}
		}
	}
</script>