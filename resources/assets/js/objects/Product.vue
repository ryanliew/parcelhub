<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							<span v-text="product.product_name"></span>
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
				<div class="columns product-display">
					<div class="column is-one-third">
						<figure class="image">
							<img :src="product.picture">
						</figure>
					</div>
					<div class="column">
						<div class="columns">
							<div class="column is-one-third">
								<text-input :defaultValue="product.sku"
											label="SKU"
											:editable="false">	
								</text-input>
								<text-input :defaultValue="product.product_name"
											label="Name"
											:editable="false">
								</text-input>
								<text-input :defaultValue="product.volume"
											label="Volume(cm³)"
											:editable="false">
								</text-input>
								<text-input :defaultValue="product.user_name"
											label="Owner"
											:editable="false">
								</text-input>
								<p class="heading">
									Attributes
								</p>
								<span class="tag is-danger" v-if="product.is_dangerous">Dangerous</span>
								<span class="tag is-warning" v-if="product.is_fragile">Fragile</span>
							</div>
							<div class="column is-one-third has-text-centered">
								<p class="heading">Stocks available</p>
								<p class="title" v-text="product.total_quantity"></p>
								<p class="heading">Incoming stocks</p>
								<p class="title" v-text="product.total_incoming_quantity"></p>
								<p class="heading">Outgoing stocks</p>
								<p class="title" v-text="product.total_outgoing_quantity"></p>
							</div>
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
									Recent inbound history
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table class="table is-hoverable is-fullwidth is-responsive">
							<thead>
								<tr>
									<th>Arrival Date</th>
									<th>Amount</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="inbound in sortedInbound">
									<td>{{ inbound.arrival_date | date }}</td>
									<td v-text="inbound.pivot.quantity"></td>
									<td v-html="$options.filters.formatInboundStatus(inbound.process_status)"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="column">
				<div class="card">
					<div class="card-header">
						<div class="card-header-title level">
							<div class="level-left">
								<div class="level-item">
									Recent outbound history
								</div>
							</div>
						</div>
					</div>
					<div class="card-content">
						<table class="table is-hoverable is-fullwidth is-responsive">
							<thead>
								<tr>
									<th>Order Date</th>
									<th>Quantity</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="outbound in sortedOutbound">
									<td v-text="$options.filters.date(outbound.created_at)"></td>
									<td v-text="outbound.pivot.quantity"></td>
									<td v-html="$options.filters.formatOutboundStatus(outbound.process_status)"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							<span>Stock details</span>
						</div>
					</div>
					<div class="level-right" v-if="can_manage">
						<div class="level-item">
							<button class="button is-primary" v-if="!isEditing" @click="editQuantity()">
								<i class="fa fa-edit"></i>
								<span class="pl-5">Edit stock details</span>
							</button>
							<div v-else>
								<button class="button is-danger" @click="onCancel()">
									<i class="fa fa-times"></i>
									<span class="pl-5">Cancel</span>
								</button>
								<button class="button is-success" @click="submit()">
									<i class="fa fa-check"></i>
									<span class="pl-5">Confirm changes</span>
								</button>
								<button class="button is-primary" @click="addLots()">
									<i class="fa fa-plus"></i>
									<span class="pl-5">Add lots</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table class="table is-responsive is-fullwidth is-hoverable">
					<thead>
						<th>Lot</th>
						<th>Quantity</th>
						<th>Incoming quantity</th>
						<th>Outgoing quantity</th>
					</thead>
					<tbody>
						<tr v-for="(lot, index) in product.lots">
							<td>{{ lot.name }}</td>
							<td>
								<text-input
									:defaultValue="product.lots[index].pivot.quantity" 
									:editable="isEditing"
									:name="'lot-quantity-' + product.lots[index].id"
									:hideLabel="true"
									:required="true"
									@input="updateStock($event, index)"
								>
								</text-input>
							</td>
							<td>{{ lot.pivot.incoming_quantity }}</td>
							<td>{{ lot.pivot.outgoing_product }}</td>
						</tr>
						<tr v-for="(lot, index) in newLots">
							<td>
								<selector-input v-model="newLots[index].lot"
									:hideLabel="true"
									:potentialData="lotsOptions"
									:editable="true"
									placeholder="Select lot">
								</selector-input>
							</td>
							<td>
								<text-input
									v-if="newLots[index]"
									v-model="newLots[index].quantity" 
									:editable="true"
									name="quantity"
									:hideLabel="true"
									:required="true"
									type="number"
								>
								</text-input>
							</td>
							<td>-</td>
							<td>-</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>



        <confirmation :isConfirming="confirmStockUpdate"
        				title="Confirmation"
        				message="Confirm updating stock? Rows without a lot selected will be removed."
        				@close="confirmStockUpdate = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['product', 'can_manage'],
		data() {
			return {
				isEditing: false,
				actualProduct: false,
				confirmStockUpdate: false,
				newLots: [],
				form: new Form({
					lot_products: [],
					new_lot_products: []
				}),
				action: 'lots/products/update',
				lotsOptions: ''
			};
		},

		mounted() {
			this.actualProduct = this.product.lots.map(lot => {
				let obj = {};
				obj['lot_id'] = lot.id;
				obj['quantity'] = lot.pivot.quantity;

				return obj;
			});

			this.getLots();
		},

		methods: {
			getLots() {
				axios.get('/internal/user/' + this.product.user_id + '/lots')
					.then(data => this.setLots(data));

			},

			setLots(data) {
				this.lotsOptions = data.data.map(lot => {
					let obj = {};
					obj['value'] = lot.id;
					obj['label'] = lot.name;

					return obj;
				});
			},

			back() {
				this.$emit('back');
			},

			editQuantity() {
				this.isEditing = true;
			},

			submit() {
				this.confirmStockUpdate = true;
			},

			onSubmit() {
				

				this.newLots = _.filter(this.newLots, function(lot){ return lot.lot; });

				this.form.lot_products = this.actualProduct.map(lot => {
					let obj = {};
					obj['lot_id'] = lot.lot_id;
					obj['product_id'] = this.product.id;
					obj['quantity'] = lot.quantity;
					return obj;
				});

				let newlots = this.newLots.map(lot => {
					let obj = {};
					obj['lot_id'] = lot.lot.value;
					obj['product_id'] = this.product.id;
					obj['quantity'] = lot.quantity;

					return obj;
				});

				newlots.forEach(lot => {
					this.form.lot_products.push(lot);
				});

				this.confirmStockUpdate = false;

				this.form.post(this.action)
					.then(data => this.onSuccess())
					.catch(error => this.onFail(error));
			},

			onSuccess() {
				this.isEditing = false;
				this.back();

			},

			onFail() {

			},

			onCancel() {
				this.isEditing = false;
			},

			updateStock(event, index) {
				this.actualProduct[index].quantity = event;
			},

			addLots() {
				this.newLots.push({lot: false, quantity: 0});
			},
		},

		computed: {
			backgroundImage() {
				return 'background-image:url("' + this.product.picture + '")';
			},

			sortedInbound() {
				return _.take( _.reverse(_.sortBy(this.product.inbounds, ['arrival_date'])), 10 );
			},

			sortedOutbound() {
				return _.take( _.reverse(_.sortBy(this.product.outbounds, ['export_date'])), 10 );
			}
		}
	}
</script>