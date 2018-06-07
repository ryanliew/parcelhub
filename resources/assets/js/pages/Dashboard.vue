<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div v-if="!isViewingOutbound && !isViewingInbound && !isViewingRecall && !isViewingReturn">
				<!-- Pending payment -->
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									New purchases
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table-view ref="payments" 
									:fields="paymentFields" 
									url="/internal/payments/pending"
									empty="All purchases have been approved">	
						</table-view>
					</div>
				</div>

				<modal :active="isViewingPayment" @close="isViewingPayment = false" v-if="selectedPayment">
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
							<div>
								<div v-html="$options.filters.formatPaymentStatus(selectedPayment.status)"></div>
							</div>
							
							<div class="is-divider" data-content="Lots purchased"></div>

							<table class="table is-hoverable is-fullwidth">
								<thead>
									<tr>
										<th>Name</th>
										<th>Monthly fee(RM)</th>
										<th>Volume(m³)</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="lot in selectedPayment.lots">
										<td v-text="lot.name"></td>
										<td v-text="lot.price"></td>
										<td>{{ lot.volume | convertToMeterCube }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

		          	<template slot="footer">
						<button v-if="selectedPayment.status !== 'true'" class="button is-primary" :class="approvePaymentLoadingClass" @click="approvePayment">Approve</button>
		          	</template>
				</modal>
				<!-- Pending payment -->

				
				<div class="columns mt-15">
					<!-- Inbound order pending -->
					<div class="column">
						<div class="card">
							<div class="card-header">
								<div class="card-header-title level">
									<div class="level-left">
										<div class="level-item">
											Pending inbounds
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<table-view ref="inbounds" 
											:fields="inboundFields" 
											url="/internal/inbounds/today"
											empty="No pending inbounds">	
								</table-view>
							</div>
						</div>
					</div>
					<!-- Inbound order pending -->
					
					<div class="column">
						<div class="card">
							<div class="card-header">
								<div class="card-header-title level">
									<div class="level-left">
										<div class="level-item">
											Incomplete outbounds
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<table-view ref="outbounds" 
											:fields="outboundFields" 
											url="/internal/outbounds/pending"
											empty="All outbound orders have been completed">	
								</table-view>
							</div>
						</div>
						
					</div>
				</div>

				<div class="columns mt-15">
					<!-- Inbound order pending -->
					<div class="column">
						<div class="card">
							<div class="card-header">
								<div class="card-header-title level">
									<div class="level-left">
										<div class="level-item">
											Pending returns
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<table-view ref="returns" 
											:fields="inboundFields" 
											url="/internal/returns/pending"
											empty="No pending returns">	
								</table-view>
							</div>
						</div>
					</div>
					<!-- Inbound order pending -->
					
					<div class="column">
						<div class="card">
							<div class="card-header">
								<div class="card-header-title level">
									<div class="level-left">
										<div class="level-item">
											Incomplete recalls
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<table-view ref="recalls" 
											:fields="outboundFields" 
											url="/internal/recalls/pending"
											empty="All recall orders have been completed">	
								</table-view>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<inbound :inbound="selectedInbound"
						:canManage="true" 
						@back="back()"
						v-if="isViewingInbound">
			</inbound>

			<return :returnobj="selectedReturn"
						:canManage="true" 
						@back="back()"
						v-if="isViewingReturn">
			</return>

			<outbound :outbound="selectedOutbound"
						:canManage="true" 
						@back="back()" 
						v-if="isViewingOutbound">
			</outbound>

			<recall :recall="selectedRecall"
						:canManage="true" 
						@back="back()" 
						v-if="isViewingRecall">
			</recall>
		</transition>

		<confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				message="Confirm payment approval?"
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: [''],

		components: { TableView },

		data() {
			return {
				selectedPayment: '',	
				isViewingPayment: false,
				paymentForm: new Form({
					id: ''
				}),
				selectedInbound: '',
				isViewingInbound: false,
				selectedOutbound: '',
				isViewingOutbound: false,
				selectedReturn: '',
				isViewingReturn: false,
				selectedRecall: '',
				isViewingRecall: false,
				confirmSubmit: false
			};
		},

		mounted() {
			this.$events.on('viewPayment', data => this.viewPayment(data));
			this.$events.on('viewInbound', data => this.viewInbound(data));
			this.$events.on('viewOutbound', data => this.viewOutbound(data));
			this.$events.on('viewRecall', data => this.viewRecall(data));
			this.$events.on('viewReturn', data => this.viewReturn(data));
		},

		methods: {
			viewPayment(data) {
				this.selectedPayment = data;
				this.isViewingPayment = true;
			},
			
			approvePayment() {
				this.confirmSubmit = true;
			},

			onSubmit() {
				this.confirmSubmit = false;
				this.paymentForm.id = this.selectedPayment.id;
				this.paymentForm.post('/payment/approve')
					.then(response => this.onSuccess());
			},	

			viewInbound(data) {
				this.selectedInbound = data;
				this.isViewingInbound = true;
			},

			viewReturn(data) {
				this.selectedReturn = data;
				this.isViewingReturn = true;
			},

			viewOutbound(data) {
				this.selectedOutbound = data;
				this.isViewingOutbound = true;
			},

			viewRecall(data) {
				this.selectedRecall = data;
				this.isViewingRecall = true;
			},

			back() {
				this.isViewingOutbound = false;
				this.isViewingInbound = false;
				this.isViewingRecall = false;
				this.isViewingReturn = false;
				this.isViewingPayment = false;
			},

			onSuccess() {
				this.isViewingPayment = false;
				this.$refs.payments.refreshTable();
			},
		},

		computed: {
			dialogTitlePayment() {
				return 'View payment #' + this.selectedPayment.id;
			},

			approvePaymentLoadingClass() {
				return this.paymentForm.submitting ? 'is-loading' : '';
			},

			paymentFields() {
				let displayFields = [
					{name: 'user.name', title: 'Made by'},
					{name: 'created_at', sortField: 'created_at', title: 'Purchase date', callback: 'date'},
					{name: 'price', sortField: 'price', title: 'Amount payable (RM)'},
					{name: 'status', sortField: 'status', title: 'Status', callback: 'purchaseStatusLabel'},
					{name: '__component:payments-actions', title: 'Actions'}
				];

				return displayFields;
			},

			outboundFields() {
				let displayFields = [
					{name: 'customer', sortField: 'users.name', title: 'Customer'},	
					{name: 'created_at', sortField: 'created_at', title: 'Order date'},
					{name: 'process_status', callback: 'outboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:outbounds-actions', title: 'Actions'}];

				return displayFields;
			},

			inboundFields() {
				let displayFields = [{name: 'customer', sortField: 'users.name', title: 'Customer'},
					{name: 'arrival_date', sortField: 'date', title: 'Arrival date'},
					{name: 'total_carton', sortField: 'carton', title: 'Total carton'},
					{name: 'process_status', callback: 'inboundStatusLabel', title: 'Status', sortField: 'process_status'},
					{name: '__component:inbounds-actions', title: 'Actions'}];

				if(this.can_manage)
				{
					displayFields.splice(1, 0, );
				}

				return displayFields;
			}
		}
	}
</script>