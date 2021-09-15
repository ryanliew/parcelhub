<template>
	<div>
		<div class="columns mt-15">
			<div class="column">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									<span>Inbound #{{ inbound.display_no }}</span>
								</div>
							</div>
							<div class="level-right">
								<div class="level-item">
									<a class="button is-primary ml-5" :href="download" target="_blank">
										<i class="fa fa-download"></i>
										<span class="pl-5">Goods received form</span>
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
										{{ inbound.created_at | date }}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Arrival date
									</p>
									<p class="title" style="max-width:153.6px;">
										{{ inbound.arrival_date}}
									</p>
								</div>
							</div>
							<div class="level-item has-text-centered">
								<div>
									<p class="heading">
										Total carton
									</p>
									<p class="title">
										{{ inbound.total_carton }}
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
				<div class="card mt-10">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Inbound products
								</div>
							</div>
							<div class="level-right" v-if="canManage && inbound.process_status !== 'completed' && inbound.process_status !== 'canceled'">
								<button class="button is-primary" :class="loadingClass" v-if="!isEditing" @click="edit()">
									<i class="fa fa-edit"></i>
									<span class="pl-5">Edit inbound details</span>
								</button>
								<div v-else>
									<button class="button is-danger" @click="onCancel()">
										<i class="fa fa-times"></i>
										<span class="pl-5">Cancel</span>
									</button>
									<button class="button is-success" @click="submitEdit()">
										<i class="fa fa-check"></i>
										<span class="pl-5">Confirm changes</span>
									</button>
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
									<th>User remark</th>
								</tr>
							</thead>
							<tbody>
								<template v-for="(product_with_lots, index) in inbound.products_with_lots">
									<tr>
										<td><figure class="image is-48x48"><img :src="inbound.products[index].picture"></figure></td>
										<td v-text="inbound.products[index].name"></td>
										<td v-text="product_with_lots.quantity"></td>
										<td>
											<div class="tags">
												<span class="tag is-danger" v-if="inbound.products[index].is_dangerous">Dangerous</span>
												<span class="tag is-warning" v-if="inbound.products[index].is_fragile">Fragile</span>
											</div>
										</td>
										
										<td>
											{{ product_with_lots.remark }}
										</td>
									</tr>
										
									<tr class="subrow">
										<th>Lots</th>
										<th>Quantity</th>
										<th>Received</th>
										<th>Expiry date</th>
										<th>Admin remark</th>
									</tr>
									<tr class="subrow" v-for="(lot, lotindex) in product_with_lots.lots">
										<td v-text="lot.name" v-if="!isEditing">
									
										</td>
										<td v-else>
											<selector-input v-model="inboundForm.products[index].lots[lotindex].lot"
												:defaultData="inboundForm.products[index].lots[lotindex].lot"
												:hideLabel="true"
												:potentialData="lotsOptions"
												placeholder="Select lot"
												name="products"
												:unclearable="true"
												v-if="inboundForm.products[index] && inboundForm.products[index].lots[lotindex]">
											</selector-input>
										</td>
										<td>
											{{ lot.pivot.quantity_original }}
										</td>
										<td>
											<text-input
												v-model="inboundForm.products[index].lots[lotindex].quantity_received"
												:defaultValue="inboundForm.products[index].lots[lotindex].quantity_received" 
												:editable="isEditing && canManage"
												name="products"
												:hideLabel="true"
												:required="true"
												type="number"
												v-if="inboundForm.products[index] && inboundForm.products[index].lots[lotindex]"
											>
											</text-input>
										</td>
										<td>
											<text-input
												v-model="inboundForm.products[index].lots[lotindex].expiry_date"
												:defaultValue="inboundForm.products[index].lots[lotindex].expiry_date" 
												:editable="isEditing"
												:hideLabel="true"
												:required="false"
												:focus="true"
												type="date"
												v-if="inboundForm.products[index] && inboundForm.products[index].lots[lotindex]"
											>
											</text-input>
										</td>
										<td>
											<textarea-input
												v-if="inboundForm.products[index] && inboundForm.products[index].lots[lotindex]"
												v-model="inboundForm.products[index].lots[lotindex].remark"
												:defaultValue="inboundForm.products[index].lots[lotindex].remark" 
												:editable="isEditing"
												label="Remark"
												name="remark"
												type="text"
												:hideLabel="true"
												:required="false"
												rows="0.5"
												cols="4">
											</textarea-input>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
						<p class="has-text-danger" v-text="inboundForm.errors.get('products')"></p>
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
								{{ inbound.process_status | unslug | capitalize }}
							</p>
							<button v-if="inbound.process_status == 'awaiting_arrival' && canEdit" @click="confirmation = true" class="button is-danger">Cancel order</button>
						</div>

					</div>
				</div>
				<div class="card mt-10">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Branch details
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<p class="heading">
							Branch Code
						</p>
						{{ inbound.code }}
						
						<hr>
						<p class="heading">
							Branch Name
						</p>
						{{ inbound.name }}
					</div>
				</div>
			</div>	
		</div>
	<div>	
	</div>

		<modal :active="confirmation" @close="confirmation = false">
			<template slot="header">Cancel order</template>
			
			Are you sure you want to cancel this inbound order?

			<template slot="footer">
				<button class="button is-danger" @click="confirmCancel">Cancel</button>
          	</template>
        </modal>

        <confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				message="Confirm changing the status of the inbound order? Editing is no longer allowed after changing the order to completed."
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>

        <confirmation :isConfirming="confirmSubmitEdit"
        				title="Confirmation"
        				message="Confirm editing the inbound order details?"
        				@close="confirmSubmitEdit = false"
        				@confirm="onSubmitEdit">
        </confirmation>
	</div>
</template>

<script>
	export default {
		props: ['inbound', 'canManage', 'canEdit'],
		data() {
			return {
				form: new Form({
					process_status: this.inbound.process_status,
					id: this.inbound.id
				}),
				inboundForm: new Form({
					products: [],
					id: this.inbound.id
				}),
				lotsOptions: [],
				selectedStatus: {
					value: this.inbound.process_status,
					label: this.$options.filters.capitalize(this.$options.filters.unslug(this.inbound.process_status))
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
			console.log(this.inbound);
			this.getLots();
		},

		methods: {
			getLots() {
				axios.get('/internal/user/' + this.inbound.products[0].user_id + '/lots')
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

				this.inboundForm.products = this.inbound.products_with_lots.map(product_with_lots => {
					let obj = {};

					obj['inbound_product_id'] = product_with_lots.id;
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
				this.form.id = this.inbound.id;
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

				this.inboundForm.post('/inbound/update/' + this.inbound.id)
					.then(response => this.onSuccess(response))
					.catch(response => this.onError(response));
			}
		},

		computed: {
			totalProducts() {
				return this.inbound.products ? this.inbound.products.length : 0;
			},

			action() {
				let action = '/admin/inbound/update';
				return action;
			},

			statusClass() {
				let color = 'is-success';
				let value = this.inbound.process_status;
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
				return "/download/inbound/report/" + this.inbound.id;
			},

			loadingClass() {
				return this.editLoading ? 'is-loading' : '';
			}
		}
	}
</script>