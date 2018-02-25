<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							<span>Inbound #{{ inbound.id }}</span>
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" @click="back()">
								<i class="fa fa-arrow-alt-circle-left"></i>
								<span class="pl-5">Back to list</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<div class="level">
					<div class="level-item has-text-centered">
						<div>
							<p class="heading">
								Arrival date
							</p>
							<p class="title">
								{{ inbound.arrival_date | date }}
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
		<div class="columns mt-15">
			<div class="column">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Inbound products
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
									<th>Lots</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(product, index) in inbound.products">
									<td><figure class="image is-48x48"><img :src="product.picture"></figure></td>
									<td v-text="product.name"></td>
									<td v-text="product.pivot.quantity"></td>
									<td>
										<div class="tags">
											<span class="tag is-danger" v-if="product.is_dangerous">Dangerous</span>
											<span class="tag is-warning" v-if="product.is_fragile">Fragile</span>
										</div>
									</td>
									<td v-text="inbound.products_with_lots[index].lots_name"></td>
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
							<button v-if="inbound.process_status == 'awaiting_arrival'" @click="confirmation = true" class="button is-danger">Cancel order</button>
						</div>

					</div>
				</div>
			</div>
		</div>

		<modal :active="confirmation" @close="confirmation = false">
			<template slot="header">Cancel order</template>
			
			Are you sure you want to cancel this inbound order?

			<template slot="footer">
				<button class="button is-danger" @click="confirmCancel">Cancel</button>
          	</template>
        </modal>
	</div>
</template>

<script>
	export default {
		props: ['inbound', 'canManage'],
		data() {
			return {
				form: new Form({
					process_status: this.inbound.process_status,
					id: this.inbound.id
				}),
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
				confirmation: false
			};
		},

		methods: {
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
			}
		}
	}
</script>