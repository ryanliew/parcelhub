<template>
	<div class="card">
		<div class="card-header">
			<div class="card-header-title">
				Lot categories
			</div>
		</div>
		<div class="card-content">
			<table-view :fields="fields" url="/internal/categories"></table-view>
		</div>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: [''],

		components: { TableView },

		data() {
			return {
				categories: '',
				fields: [
					{name: 'name', sortField: 'name'},
					{name: 'volume', sortFiled: 'volume', title: 'Volume (cm³)'}	
				]
			};
		},

		mounted() {
			this.fetchCategories();
		},

		methods: {
			fetchCategories() {
				axios.get('/internal/categories')
					.then(response => this.setCategories);
			},

			setCategories(response) {
				this.categories = response.data;
			}
		}	
	}
</script>