<template>
	<div class="card">
		<div class="card-header">
			<div class="card-header-title level">
				<div class="level-left">
					<div class="level-item">
						Generate stock report
					</div>
				</div>
			</div>
		</div>
		<div class="card-content">
			<form @submit.prevent="submit" 
				@keydown="form.errors.clear($event.target.name)" 
				@input="form.errors.clear($event.target.name)">
				<div class="columns">
					<div class="column">
						<text-input v-model="form.from" 
							:defaultValue="form.from"
							:required="true"
							type="date"
							label="From"
							name="from"
							:editable="true"
							:focus="true"
							:hideLabel="false"
							:error="form.errors.get('from')"
							:dateMax="form.to || today">
						</text-input>
					</div>
					<div class="column">
						<text-input v-model="form.to" 
							:defaultValue="form.to"
							:required="true"
							type="date"
							label="To"
							name="to"
							:editable="true"
							:focus="false"
							:hideLabel="false"
							:error="form.errors.get('to')"
							:dateMin="form.from"
							:dateMax="today">
						</text-input>
					</div>
				</div>

				<div class="columns">
					<div class="column">
						<label class="is-pulled-left">Select products</label>
						<button type="button" @click="selectAll" class="button is-primary is-pulled-right ml-5">Select all</button>
						<button type="button" @click="deselectAll" class="button is-primary is-pulled-right">Deselect all</button>
						<div class="select is-multiple is-fullwidth mt-5" :class="isLoadingClass">
							<select v-model="form.products" multiple size="5">
								<option v-for="product in sortedProducts" :value="product.id">{{ product.selector_name }}</option>
							</select>
							<i class="has-text-grey-dark">Hold down the Ctrl button to select multiple products</i>
						</div>
					</div>
				</div>

				

				<div class="columns">
					<div class="column">
						<label class="checkbox"> 
							<input type="checkbox" v-model="form.details">
							<span class="pl-5">With transaction details</span>
						</label>
					</div>
				</div>

				<div class="columns">
					<div class="column">
						<label>Type</label>
						<div class="select is-fullwidth">
							<select v-model="form.type" @keyup.enter="submit">
								<option value="all">All</option>
								<option value="in">Inbounds only</option>
								<option value="out">Outbounds only</option>
							</select>
						</div>
					</div>
				</div>


				<button :title="submitTooltip" type="button" class="button is-success mt-5" @click="submit" :class="isSubmittingClass" :disabled="!canSubmit">Generate</button>
			</form>
		</div>

	</div>
</template>

<script>
	import moment from 'moment';

	export default {
		props: ['active'],
		data() {
			return {
				form: new Form({
					from: moment().startOf('month').format('YYYY-MM-DD'),
					to: moment().format('YYYY-MM-DD'),
					products: [],
					type: 'all',
					details: false
				}),
				products: [],
				isLoading: true
			};
		},

		mounted() {
			this.getProducts();
		},

		methods: {
			close() {
				this.$emit('close');
			},

			getProducts(error = 'No error') {
				axios.get('/internal/products/selector')
					.then(response => this.setProducts(response))
					.catch(error => this.getProducts(e));
			},

			setProducts(response){
				this.products = response.data;
				this.isLoading = false;
			},

			submit() {
				this.form.post("/report/stock")
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				window.open("/" + response.url);
				this.close();
			},

			selectAll() {
				this.form.products = this.products.map(function(product){
					return product.id;
				});
			},

			deselectAll() {
				this.form.products = [];
			}
		},

		computed: {
			today() {
				return moment().format('YYYY-M-D');
			},

			isLoadingClass() {
				return this.isLoading ? 'is-loading' : '';
			},

			sortedProducts() {
				return _.sortBy(this.products, function(product) { return product.sku; });
			},

			isSubmittingClass() {
				return this.form.submitting ? "is-loading" : "";
			},

			canSubmit() {
				return this.form.products.length > 0;
			},

			submitTooltip() {
				return this.canSubmit ? "" : "Please select at least 1 product";
			}
		}	
	}
</script>