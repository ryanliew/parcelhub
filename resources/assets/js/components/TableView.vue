<template>
	<div>
		<loader v-if="loading"></loader>
		<vuetable-filter-bar v-if="searchables" :dateFilterable="dateFilterable"></vuetable-filter-bar>
		<vuetable ref="vuetable" 
				:api-url="url"
	    		:fields="fields"
	    		:multi-sort="true"
	    		:css="css"
	    		:append-params="params"
	    		:detail-row-component="detail"
	    		pagination-path=""
	    		@vuetable:pagination-data="onPaginationData"
	    		@vuetable:loaded="onLoaded"
	    		@vuetable-refresh="refreshTable"
	    		@vuetable:cell-clicked="onCellClicked"
	    		:no-data-template="empty">	
	    </vuetable>
	    <div class="level">
	    	<div class="level-left">
			    <vuetable-pagination-info ref="paginationInfo">
				</vuetable-pagination-info>
			</div>
			<div class="level-right">
			    <vuetable-pagination 
			    	ref="pagination"
			    	@vuetable-pagination:change-page="onChangePage">
			    </vuetable-pagination>
			</div>
		</div>
	</div>
</template>

<script>
	import Vuetable from 'vuetable-2/src/components/Vuetable';
	import VuetablePagination from './VuetablepaginationBulma';
	import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
	import VuetableFilterBar from './VuetableFilterBar';
	import moment from 'moment';
	import Loader from './Loader';

	export default {
		props: ['user', 'fields', 'url', 'searchables', 'detail', 'empty', 'dateFilterable', 'dateFilterKey'],

		components: { Vuetable, VuetablePagination, VuetablePaginationInfo, VuetableFilterBar, Loader },

		data() {
			return {
				css: {
					tableClass: 'table is-hoverable is-fullwidth responsive',
					ascendingIcon: 'fa fa-caret-up',
					descendingIcon: 'fa fa-caret-down',
				},
				params: {},
				loading: true
			};
		},

		mounted() {
			this.$events.$on('filter-set', eventData => this.onFilterSet(eventData));
			this.$events.$on('filter-reset', e => this.onFilterReset());
		},

		methods: {
			refreshTable() {
				this.loading = true;
				setTimeout(function(){
					this.$refs.vuetable.refresh();
				}.bind(this), 1000);
			},

			onPaginationData(paginationData) {
				this.$refs.pagination.setPaginationData(paginationData);
				this.$refs.paginationInfo.setPaginationData(paginationData);
			},

			onChangePage(page) {
				this.$refs.vuetable.changePage(page);
			},

			onFilterSet(filters) {
				this.loading = true;
				this.params = {
					'filter' : filters.text,
					'searchables' : this.searchables
				}

				if(filters.start && filters.end)
				{
					this.params.start = filters.start;
					this.params.end = filters.end;
					this.params.dateFilterKey = this.dateFilterKey;
				}

				Vue.nextTick( () => this.$refs.vuetable.refresh())
			},

			onFilterReset() {
				this.params = {}
				Vue.nextTick( () => this.$refs.vuetable.refresh())
			},

			onLoaded() {
				this.loading = false;
				reswitch = true;
				updateTables();
			},

			onCellClicked(data, field, event) {
				if(this.detail == 'LotDetailRow'
					|| this.detail == 'ProductDetailRow')
					this.$refs.vuetable.toggleDetailRow(data.id);
			},

			purchaseStatusLabel(value) {
				return this.$options.filters.formatPaymentStatus(value);
				/*return value == 'true' 
					? '<span class="tag is-success">Approved</span>'
					: '<span class="tag is-warning">Processing</span>'*/
			},

			incomeLabel(value) {
				return value ==='income' || value === 'profit'
					? '<span class="tag is-success">Profit</span>'
					: '<span class="tag is-danger">Expense</span>';
			},

			operatorStatusLabel(value) {
				return value === 1
					? '<span class="tag is-success">Verified</span>'
					: '<span class="tag is-danger">Pending</span>';
			},

			commissionTypeLabel(value) {
				return value === 1
					? '<span class="tag is-info">By revenue</span>'
					: '<span class="tag is-warning">By profit</span>';
			},

			inboundStatusLabel(value) {
				return this.$options.filters.formatInboundStatus(value);
			},

			outboundStatusLabel(value) {
				return this.$options.filters.formatOutboundStatus(value);
			},

			image(value) {
				return '<figure class="image is-48x48"><img src="'+ value +'"></figure>';
			},

			dangerousTag(value) {
				return value === 1 ? '<span class="tag is-danger">Dangerous</span>' : '';
			},

			fragileTag(value){
				return value === 1 ? '<span class="tag is-warning">Fragile</span>' : '';
			},

			date(value){
				return this.$options.filters.date(value);
			},

			customer(value){
				return value ? value : "N/A";
			},

			convertToM(value){
				return this.$options.filters.convertToMeterCube(value);
			}


		}
	}
</script>