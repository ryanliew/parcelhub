<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							System settings
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<form @submit.prevent="submit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)"
					@keyup.enter="submit">

		          	<div class="field">
		          		<text-input v-model="form.days_before_order" :defaultValue="form.days_before_order" 
									label="Days before inbound" 
									:required="true"
									name="days_before_order"
									type="number"
									:editable="true"
									:error="form.errors.get('days_before_order')"
									:focus="true">
						</text-input>
					</div>
					<div class="field">
						<text-input v-model="form.rental_duration" :defaultValue="form.rental_duration" 
									label="Minimum rental duration (months)" 
									:required="true"
									name="rental_duration"
									type="number"
									:editable="true"
									:error="form.errors.get('rental_duration')">
						</text-input>
		          	</div>
		          	<div class="field">
						<text-input v-model="form.cancelation_fee" :defaultValue="form.cancelation_fee" 
									label="Cancelation fee" 
									:required="true"
									name="cancelation_fee"
									type="number"
									:editable="true"
									:error="form.errors.get('cancelation_fee')">
						</text-input>
		          	</div>
		          	<div class="field">
						<text-input v-model="form.cancelation_number" :defaultValue="form.cancelation_number" 
									label="Phone number for order cancelation" 
									:required="true"
									name="cancelation_number"
									type="text"
									:editable="true"
									:error="form.errors.get('cancelation_number')">
						</text-input>
		          	</div>

		          	<button class="button is-primary">Submit</button>
	          	</form>
			</div>
		</div>

		<confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				message="Confirm changing settings?"
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';

	export default {
		props: ['setting'],

		components: { TableView },

		data() {
			return {
				form: new Form({
					days_before_order: '',
					rental_duration: '',
					cancelation_fee: '',
					cancelation_number: '',
				}),
				confirmSubmit: false
			};
		},

		created() {
			this.form.rental_duration = this.setting.rental_duration;
			this.form.days_before_order = this.setting.days_before_order;
			this.form.cancelation_fee = this.setting.cancelation_fee;
			this.form.cancelation_number = this.setting.cancelation_number;
		},

		methods: {
			submit() {
				this.confirmSubmit = true;
			},

			onSubmit() {
				this.confirmSubmit = false;
				this.form.post(this.action)
					.then(data => this.onSuccess(data))
					.catch(error => this.onFail(error));
			},

			onSuccess(data) {
				this.form.rental_duration = data.setting.rental_duration;
				this.form.days_before_order = data.setting.days_before_order;
				this.form.cancelation_fee = data.setting.cancelation_fee;
				this.form.cancelation_number = data.setting.cancelation_number;
			},

			onFail() {

			},
		},

		computed: {
			
			action() {
				return "/setting/update";
			}
		}
	}
</script>