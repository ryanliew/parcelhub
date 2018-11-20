<template>
	<div class="card">
		<div class="card-header">
			<div class="card-header-title level">
				<div class="level-left">
					<div class="level-item">
						Reports
					</div>
				</div>
			</div>
		</div>
		<div class="card-content">
			<table class="table is-fullwidth responsive">
				<thead>
					<th>Report type</th> 
					<th>Action</th>
				</thead> 
				<tbody>
					<tr>
						<td>Stock report</td> 
						<td><a href="/report/stock" class="button is-primary">Generate</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
	import StockReport from "../objects/StockReport.vue";

	export default {
		props: [''],

		components: { StockReport },

		data() {
			return {
				showStockReport: false
			};
		},

		methods: {
			generateSalesReport() {

			}
		}	
	}
</script>