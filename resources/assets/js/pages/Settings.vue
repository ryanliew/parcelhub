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
									:error="form.errors.get('rental_duration')"
									:focus="true">
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
				}),
				confirmSubmit: false
			};
		},

		created() {
			this.form.rental_duration = this.setting[0].value;
			this.form.days_before_order = this.setting[1].value;
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
				console.log(data);
				this.form.rental_duration = data.setting[0].value;
				this.form.days_before_order = data.setting[1].value;
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