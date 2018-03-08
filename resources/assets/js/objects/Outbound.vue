<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							<span>Outbound #{{ outbound.id }}</span>
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" @click="back()">
								<i class="fa fa-arrow-alt-circle-left"></i>
								<span class="pl-5">Back to list</span>
							</button>
							<a class="button is-info ml-5" :href="download" target="_blank">
								<i class="fa fa-download"></i>
								<span class="pl-5">Download PDF</span>
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
								Order date
							</p>
							<p class="title">
								{{ outbound.arrival_date | date }}
							</p>
						</div>
					</div>
					<div class="level-item has-text-centered">
						<div>
							<p class="heading">
								Courier
							</p>
							<p class="title">
								{{ outbound.courier }}
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
		<div class="columns mt-15">
			<div class="column">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Outbound products
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table class="table is-hoverable is-fullwidth is-responsive">
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
						</div>
					</div>
					<div class="card-content">
						<form @submit.prevent="onSubmit" 
							@keydown="form.errors.clear($event.target.name)" 
							@input="form.errors.clear($event.target.name)"
							@keyup.enter="submit"
							v-if="can_manage">
							
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

							<div class="field">
								<button type="submit" class="button is-success is-pulled-right">Change status</button>

							</div>
							<div class="is-clearfix"></div>
          				</form>
						<div class="has-text-centered" v-else>
							<p class="title" :class="statusClass">
								{{ outbound.process_status | unslug | capitalize }}
							</p>
							<button class="button is-danger" v-if="outbound.process_status == 'pending'" @click="confirmation = true">Cancel order</button>
						</div>

					</div>
				</div>
				<div class="card">
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
						{{ outbound.recipient_name }}
						
						<hr>
						<p class="heading">
							Address
						</p>
						{{ outbound.recipient_address }}, <br>
						{{ outbound.recipient_address_2 }}, <br>
						{{ outbound.recipient_postcode }} {{ outbound.recipient_state }}, {{ outbound.recipient_country }}
						<hr>
						<p class="heading">
							Phone
						</p>
						{{ outbound.recipient_phone }}
							


					</div>
				</div>
			</div>
		</div>

		<modal :active="confirmation" @close="confirmation = false">
			<template slot="header">Cancel order</template>
			
			Are you sure you want to cancel this outbound order?

			<template slot="footer">
				<button class="button is-danger" @click="confirmCancel">Cancel</button>
          	</template>
        </modal>
	</div>
</template>

<script>
	export default {
		props: ['outbound', 'canManage'],
		data() {
			return {
				loading: true,
				products: [],
				form: new Form({
					process_status: '',
					id: ''
				}),
				selectedStatus: {
					value: this.outbound.process_status,
					label: this.$options.filters.capitalize(this.$options.filters.unslug(this.outbound.process_status))
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
						value: 'delivering',
						label: 'Delivering'
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
				confirmation: false
			};
		},

		mounted() {
			this.getOutbound();
		},

		methods: {
			getOutbound() {
				axios.get('/internal/outbound/' + this.outbound.id)
					.then(response => this.setOutbound(response));
			},

			setOutbound(response) {
				this.products = response.data;

				this.form.id = this.outbound.id;

				this.form.process_status = this.outbound.process_status;
			},

			back() {
				this.$emit('back');
			},

			onSubmit() {
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.catch(response => this.onError(response));
			},

			onSuccess() {

			},

			onError() {

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

			statusClass() {
				let color = 'is-success';
				let value = this.outbound.process_status;
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
				return "/download/outbound/report/" + this.outbound.id;
			},
		}
	}
</script>