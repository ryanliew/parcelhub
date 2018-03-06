<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							<span v-text="product.name"></span>
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
								<text-input :defaultValue="product.name"
											label="Name"
											:editable="false">
								</text-input>
								<text-input :defaultValue="product.volume"
											label="Volume(cm³)"
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
								<p class="title" v-text="product.total"></p>
								<p class="heading">Height(cm)</p>
								<p class="title" v-text="product.height"></p>
								<p class="heading">Width(cm)</p>
								<p class="title" v-text="product.width"></p>
								<p class="heading">Length(cm)</p>
								<p class="title" v-text="product.length"></p>
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
									<th>Export Date</th>
									<th>Amount</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="outbound in sortedOutbound">
									<td v-text="outbound.date"></td>
									<td v-text="outbound.pivot.quantity"></td>
									<td v-html="$options.filters.formatOutboundStatus(outbound.process_status)"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['product'],
		data() {
			return {

			};
		},

		methods: {
			back() {
				this.$emit('back');
			}
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