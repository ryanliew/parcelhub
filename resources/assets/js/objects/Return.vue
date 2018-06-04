<template>
	<div>
		<div class="columns mt-15">
			<div class="column">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									<span>Return #{{ returnobj.id }}</span>
								</div>
							</div>
							<div class="level-right">
								<div class="level-item">
									<a class="button is-primary ml-5" :href="download" target="_blank">
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
										Return date
									</p>
									<p class="title">
										{{ returnobj.arrival_date | date }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total carton
									</p>
									<p class="title">
										{{ returnobj.total_carton }}
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
							v-if="canManage">
							
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
								{{ returnobj.process_status | unslug | capitalize }}
							</p>
							<button v-if="returnobj.process_status == 'awaiting_arrival' && canEdit" @click="confirmation = true" class="button is-danger">Cancel order</button>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Return products
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
							<th>Remark</th>
						</tr>
					</thead>
					<tbody>
						<template v-for="(product_with_lots, index) in returnobj.products_with_lots">
							<tr>
								<td><figure class="image is-48x48"><img :src="returnobj.products[index].picture"></figure></td>
								<td v-text="returnobj.products[index].name"></td>
								<td v-text="product_with_lots.quantity"></td>
								<td>
									<div class="tags">
										<span class="tag is-danger" v-if="returnobj.products[index].is_dangerous">Dangerous</span>
										<span class="tag is-warning" v-if="returnobj.products[index].is_fragile">Fragile</span>
									</div>
								</td>
								
								<td>
									{{ product_with_lots.remark }}
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
		</div>

		<modal :active="confirmation" @close="confirmation = false">
			<template slot="header">Cancel order</template>
			
			Are you sure you want to cancel this return order?

			<template slot="footer">
				<button class="button is-danger" @click="confirmCancel">Cancel</button>
          	</template>
        </modal>

        <confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				message="Confirm changing the status of the return order? Editing is no longer allowed after changing the order to completed."
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	export default {
		props: ['returnobj', 'canManage', 'canEdit'],
		data() {
			return {
				form: new Form({
					process_status: this.returnobj.process_status,
					id: this.returnobj.id
				}),
				returnobjForm: new Form({
					products: [],
					id: this.returnobj.id
				}),
				lotsOptions: [],
				selectedStatus: {
					value: this.returnobj.process_status,
					label: this.$options.filters.capitalize(this.$options.filters.unslug(this.returnobj.process_status))
				},
				processStatusOptions: [
					{
						value: 'awaiting_arrival',
						label: 'Awaiting arrival'
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
				confirmSubmitEdit: false,
				isEditing: false,
				editLoading: true
			};
		},

		mounted() {
			this.getLots();
		},

		methods: {
			getLots() {
				axios.get('/internal/user/' + this.returnobj.products[0].user_id + '/lots')
					.then(data => this.setLots(data));

			},

			setLots(data) {
				this.lotsOptions = data.data.map(lot => {
					let obj = {};
					obj['value'] = lot.id;
					obj['label'] = lot.name;

					return obj;
				});

				this.setFormData();
			},

			setFormData() {
				this.editLoading = false;

				this.returnobjForm.products = this.returnobj.products_with_lots.map(product_with_lots => {
					let obj = {};

					obj['returnobj_product_id'] = product_with_lots.id;
					obj['product_id'] = product_with_lots.product_id;
					obj['lots'] = product_with_lots.lots.map(lot => {
						let lotobj = {};
						lotobj['original_lot'] = lot.id;
						lotobj['lot'] = { value: lot.id, label: lot.name };
						lotobj['quantity_original'] = lot.pivot.quantity_original;
						lotobj['original_quantity'] = lot.pivot.quantity_original == lot.pivot.quantity_received || lot.pivot.quantity_received == 0 ? lot.pivot.quantity_original : lot.pivot.quantity_received;
						lotobj['quantity_received'] = lot.pivot.quantity_received;
						lotobj['expiry_date'] = lot.pivot.expiry_date;
						lotobj['remark'] = lot.pivot.remark;

						return lotobj;
					});

					return obj;
				})
			},

			back() {
				this.$emit('back');
			},

			submit() {
				this.confirmSubmit = true;
			},

			onSubmit() {	
				this.confirmSubmit = false;
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.catch(response => this.onError(response));
			},

			onSuccess() {
				this.confirmSubmit = false;
				this.isEditing = false;
				this.form.id = this.returnobj.id;
				this.back();
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
			},

			edit() {
				this.isEditing = true;
			},

			onCancel() {
				this.isEditing = false;
				this.setFormData();
			},

			submitEdit() {
				this.confirmSubmitEdit = true;
			},

			onSubmitEdit() {
				this.confirmSubmitEdit = false;

				this.returnobjForm.post('/inbound/update/' + this.returnobj.id)
					.then(response => this.onSuccess(response))
					.catch(response => this.onError(response));
			}
		},

		computed: {
			totalProducts() {
				return this.returnobj.products ? this.returnobj.products.length : 0;
			},

			action() {
				let action = '/admin/inbound/update';
				return action;
			},

			statusClass() {
				let color = 'is-success';
				let value = this.returnobj.process_status;
				switch(value) {
					case 'awaiting_arrival':
						color = 'is-warning';
						break;
					case 'delivering':
						color = 'is-info';
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
				return "/download/inbound/report/" + this.returnobj.id;
			},

			loadingClass() {
				return this.editLoading ? 'is-loading' : '';
			}
		}
	}
</script>