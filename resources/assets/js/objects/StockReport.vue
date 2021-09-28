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
							<!-- <select v-model="form.products" multiple size="5">
								<option v-for="product in sortedProducts" :value="product.id">{{ product.selector_name }}</option>
							</select> -->
							<multiselect v-model="selectedProducts" 
								:options="sortedProducts" 
								:close-on-select="false" 
								:multiple="true" 
								:clear-on-select="false"
								:custom-label="customLabel"
								track-by="value">
								<template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span></template>
							</multiselect>
							<!-- <div class="columns">
								<div class="column">
									<i class="has-text-grey-dark">Hold down the Ctrl button to select multiple products</i>
								</div>
								<div class="column is-narrow">
									<i class="has-text-grey-dark">{{ this.form.products.length }} selected</i>
								</div>
							</div> -->
						</div>
					</div>
				</div>
				<div class= "columns">
					<div class="column">
						<selector-input
								v-model="selected_branch"
								label="Branch" 
								:potentialData="branchesOptions"
								name="branch"
								:editable="true"
								placeholder="Select a branch"
								:error="form.errors.get('selectedBranch')">></selector-input>
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
						<label>Report type</label>
						<div class="select is-fullwidth">
							<select v-model="form.report_type" @keyup.enter="submit">
								<option value="pdf">PDF</option>
								<option value="excel">Excel</option>
							</select>
						</div>
					</div>
				</div>
				<!-- <div class="columns">
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
				</div> -->


				<button :title="submitTooltip" type="button" class="button is-success mt-5" @click="submit" :class="isSubmittingClass" :disabled="!canSubmit">Generate</button>
			</form>
		</div>

	</div>
</template>

<script>
	import moment from 'moment';
	import Multiselect from 'vue-multiselect'

	export default {
		props: ['active'],

		components: { Multiselect },

		data() {
			return {
				form: new Form({
					from: moment().startOf('month').format('YYYY-MM-DD'),
					to: moment().format('YYYY-MM-DD'),
					products: [],
					type: 'all',
					report_type: 'pdf',
					selectedBranch: '',
					details: false
				}),
				selected_branch: '',
				products: [],
				selectedProducts: [],
				branchesOptions: [],
				isLoading: true
			};
		},

		mounted() {
			this.getProducts();
			this.getBranches();
		},

		methods: {
			close() {
				this.$emit('close');
			},

			getProducts(error = 'No error') {
				axios.get('/internal/products/selector/true')
					.then(response => this.setProducts(response))
					.catch(error => this.getProducts(error));
			},

			getBranches() {
				axios.get('/internal/branches/selector')
					.then(response => this.setBranches(response));
			},

			setProducts(response){
				this.products = response.data.map(product => {
					let obj = {};

					obj['label'] = product.selector_name;
					obj['value'] = product.id;

					return obj;	
				});

				this.isLoading = false;
			},

			setBranches(response) {
				this.branchesOptions = response.data.map(branches =>{
					let obj = {};
					obj['label'] = branches.name;
					obj['value'] = branches.code;
					return obj;
				});
			},

			submit() {
				if(this.selected_branch != null) {
					this.form.selectedBranch = this.selected_branch.value;
				}
				this.form.post("/report/stock")
					.then(response => this.onSuccess(response))
					.catch(error => this.onError(error));
			},

			onSuccess(response) {
				window.open("/" + response.url);
				this.selectedBranch = '';
				this.selectedProducts = [];
				this.close();
			},

			onError(error) {
				flash("Memory exhausted, please select lesser products and try again.", 'danger');
			},

			selectAll() {
				this.selectedProducts = this.products;
			},

			deselectAll() {
				this.selectedProducts = [];
			},

			customLabel({label, value}) {
				return label;
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
		},

		watch: {
			selectedProducts(newVal) {
				this.form.products = newVal.map(product => {
					return product.value;
				});
			}
		}	
	}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>