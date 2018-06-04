<template>
	<div>
		<div class="columns">
			<div class="column">
				<!-- Recall details -->
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									<span>Recall #{{ recall.id }}</span>
								</div>
							</div>
							<div class="level-right">
								<div class="level-item">
									<a class="button is-info ml-5" :href="download" target="_blank">
										<i class="fa fa-download"></i>
										<span class="pl-5">Download invoice</span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<div class="level">
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Recall date
									</p>
									<p class="title">
										{{ recall.created_at | date }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total products
									</p>
									<p class="title">
										{{ totalProducts }}
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Recall products -->
				<div class="card mt-15">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Recall products
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table class="table is-hoverable is-fullwidth responsive">
							<thead>
								<tr>
									<th>Image</th>
									<th>Name</th>
									<th>Quantity</th>
									<th>Attributes</th>
									<th>Lot</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(product, index) in products">
									<td><figure class="image is-48x48"><img :src="product.picture"></figure></td>
									<td v-text="product.name"></td>
									<td v-text="product.pivot.quantity"></td>
									<td>
										<div class="tags">
											<span class="tag is-danger" v-if="product.is_dangerous">Dangerous</span>
											<span class="tag is-warning" v-if="product.is_fragile">Fragile</span>
										</div>
									</td>
									<td v-text="product.lot_name"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
					
				<!-- Tracking numbers -->
				<div class="card mt-15" v-if="recall.tracking_numbers.length > 0">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Tracking numbers
								</div>
							</div>
							<div class="level-right">
								<div class="level-item" v-if="can_manage">
									<button class="button is-primary" v-if="!isEditTracking" @click="editTracking()">
										<i class="fa fa-edit"></i>
										<span class="pl-5">Edit tracking numbers</span>
									</button>
									<div v-else>
										<button class="button is-danger" @click="onCancel()">
											<i class="fa fa-times"></i>
											<span class="pl-5">Cancel</span>
										</button>
										<button class="button is-success" @click="submitTracking()">
											<i class="fa fa-check"></i>
											<span class="pl-5">Confirm changes</span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table class="table is-hoverable is-fullwidth responsive" v-if="!isEditTracking">
							<thead>
								<tr>
									<th>#</th>
									<th>Number</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(number, index) in recall.tracking_numbers">
									<td>{{ index + 1 }}</td>
									<td v-text="number.number"></td>
								</tr>
							</tbody>
						</table>
						<div class="field" v-else>
							<form @submit.prevent="submitTracking" 
								@keydown="trackingForm.errors.clear($event.target.name)" 
								@input="trackingForm.errors.clear($event.target.name)"
								@keyup.enter="submitTracking">

								<textarea-input
									v-model="trackingForm.tracking_numbers" 
									:defaultValue="trackingForm.tracking_numbers"
									:editable="true"
									label="Tracking numbers"
									name="tracking_numbers"
									type="text"
									:hideLabel="false"
									:required="true"
									:error="trackingForm.errors.get('tracking_numbers')"
									rows="2"
									cols="4">
								</textarea-input>
								<i class="has-text-grey">Please separate the tracking numbers with the ; character</i>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="column is-one-third">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Order status
								</div>
							</div>
							<div class="level-right">
								<div class="level-item">
									<button class="button is-primary" @click="back()">
										<i class="fa fa-arrow-circle-left"></i>
										<span class="pl-5">Back to list</span>
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
							v-if="canManage && recall.process_status !== 'completed'">
							
							<div class="field">
								<selector-input v-model="selectedStatus" :defaultData="selectedStatus"
												label="Status"
												:required="true"
												name="process_status"
												:potentialData="processStatusOptions"
												@input="statusUpdate($event)"
												:editable="true"
												placeholder="Select a status"
												:unclearable="true"
												:error="form.errors.get('process_status')">
								</selector-input>
							</div>

							<div class="field" v-if="selectedStatus.value == 'completed'">
								<textarea-input
									v-model="form.tracking_numbers" 
									:defaultValue="form.tracking_numbers"
									:editable="true"
									label="Tracking numbers"
									name="tracking_numbers"
									type="text"
									:hideLabel="false"
									:required="false"
									:error="form.errors.get('tracking_numbers')"
									rows="2"
									cols="4">
								</textarea-input>
								<i class="has-text-grey">Please separate the tracking numbers with the ; character</i>
							</div>

							<div class="field">
								<button type="submit" class="button is-success is-pulled-right">Change status</button>
							</div>
							<div class="is-clearfix"></div>
          				</form>
						<div class="has-text-centered" v-else>
							<p class="title" :class="statusClass">
								{{ recall.process_status | unslug | capitalize }}
							</p>
							<button class="button is-danger" v-if="recall.process_status !== 'completed' && recall.process_status !== 'canceled' && canEdit" @click="confirmation = true">Cancel order</button>
						</div>

					</div>
				</div>
				<div class="card mt-10">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Recipient details
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<p class="heading">
							Name
						</p>
						{{ recall.recipient_name }}
						
						<hr>
						<p class="heading">
							Address
						</p>
						{{ recall.recipient_address }}, <br>
						<div v-if="recall.recipient_address_2">{{ recall.recipient_address_2 }}, <br></div> 
						{{ recall.recipient_postcode }} {{ recall.recipient_state }}, {{ recall.recipient_country }}
						<hr>
						<p class="heading">
							Phone
						</p>
						{{ recall.recipient_phone }}
							


					</div>
				</div>
			</div>
		</div>

		<modal :active="confirmation" @close="confirmation = false">
			<template slot="header">Cancel order</template>
			<p v-if="recall.process_status == 'processing'">
				Unfortunately, we are already processing your order. <br>
				Please call <b>{{ number }}</b> for assistance. A cancelation fee of <b>RM{{ fee }}</b> might be charged.
			</p>
			<p v-else>Are you sure you want to cancel this recall order?</p>

			<template slot="footer" v-if="recall.process_status !== 'processing'">
				<button class="button is-danger" @click="confirmCancel">Cancel</button>
          	</template>
        </modal>

        <confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				message="Confirm changing the status of the recall order?"
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>

        <confirmation :isConfirming="confirmSubmitTracking"
        				title="Confirmation"
        				message="Confirm editing the tracking numbers?"
        				@close="confirmSubmitTracking = false"
        				@confirm="onSubmitTracking">
        </confirmation>
	</div>
</template>

<script>
	export default {
		props: ['recall', 'canManage', 'fee', 'number', 'canEdit'],
		data() {
			return {
				loading: true,
				products: [],
				form: new Form({
					process_status: '',
					tracking_numbers: '',
					id: ''
				}),
				trackingForm: new Form({
					tracking_numbers: '',
					id: ''
				}),
				selectedStatus: {
					value: this.recall.process_status,
					label: this.$options.filters.capitalize(this.$options.filters.unslug(this.recall.process_status))
				},
				processStatusOptions: [
					{
						value: 'pending',
						label: 'Pending'
					},
					{
						value: 'processing',
						label: 'Processing'
					},
					{
						value: 'completed',
						label: 'Completed'
					},
					{
						value: 'canceled',
						label: 'Canceled'
					}
				],
				confirmation: false,
				confirmSubmit: false,
				confirmSubmitTracking: false,
				isEditTracking: false

			};
		},

		mounted() {
			this.getRecall();
		},

		methods: {
			getRecall() {
				axios.get('/internal/outbound/products/' + this.recall.id)
					.then(response => this.setRecall(response));
			},

			setRecall(response) {
				this.products = response.data;

				this.form.id = this.recall.id;

				this.form.process_status = this.recall.process_status;
			},

			back() {
				this.$emit('back');
			},

			submit() {
				this.confirmSubmit = true;
			},

			editTracking() {
				this.isEditTracking = true;

				this.trackingForm.tracking_numbers = _.map(this.recall.tracking_numbers, function(value) {
					return value.number;
				}).join(";");
			},

			submitTracking() {
				this.confirmSubmitTracking = true;
			},

			onSubmit() {
				this.confirmSubmit = false;
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.catch(response => this.onError(response));
			},

			onSubmitTracking() {
				this.confirmSubmitTracking = false;
				this.trackingForm.id = this.recall.id;

				// Trim out the last semicolon if it exists
				let numbers = this.trackingForm.tracking_numbers;
				if(numbers.substring(numbers.length - 1) == ";")
				{
					this.trackingForm.tracking_numbers = numbers.substring(0, numbers.length - 1);
				}

				this.trackingForm.post(this.trackingAction)
								.then(response => this.onSuccess(response))
								.catch(response => this.onError(response));
			},

			onSuccess(response) {
				this.form.id = this.recall.id;
				this.back();
			},

			onError(response) {
				this.form.id = this.recall.id
			},

			onCancel() {
				this.isEditTracking = false;
			},

			statusUpdate(data) {
				this.selectedStatus = data;
				this.form.process_status = data.value;
			},

			confirmCancel() {
				this.form.process_status = "canceled";
				this.onSubmit();
				this.confirmation = false;
				this.$emit('canceled');
			}
		},

		computed: {
			totalProducts() {
				return this.products ? this.products.length : 0;
			},

			action() {
				let action = '/admin/outbound/update';
				return action;
			},

			trackingAction() {
				let action = '/admin/outbound/tracking/update';
				return action;
			},

			statusClass() {
				let color = 'is-success';
				let value = this.recall.process_status;
				switch(value) {
					case 'pending':
						color = 'is-warning';
						break;
					case 'processing':
						color = 'is-info';
						break;
					case 'delivering':
						color = 'is-primary';
						break;
					case 'completed':
						color = 'is-success';
						break;
					case 'canceled':
						color = 'is-danger';
						break;
				}

				return color;
			},

			download() {
				return "/download/outbound/report/" + this.recall.id;
			},

			downloadPackinglist() {
				return "/download/outbound/packingList/" + this.recall.id;
			},
		}
	}
</script>