<template>
	<div>
		<div class="card">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Barcode input
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<form @submit.prevent="barcodeSubmit" 
					@keyup.enter="barcodeSubmit">

		          	<div class="field">
		          		<text-input v-model="input" :defaultValue="input" 
									label="Barcode input" 
									:required="true"
									name="barcode"
									type="text"
									:editable="true"
									:focus="true">
						</text-input>
		          	</div>
	          	</form>

			</div>
		</div>

		<div class="card mt-15">
			<div class="card-header">
				<div class="card-header-title level">
					<div class="level-left">
						<div class="level-item">
							Outbounds tracking numbers
						</div>
					</div>
					<div class="level-right">
						<div class="level-item">
							<button class="button is-primary" v-if="!isEditing" @click="editTracking()">
								<i class="fa fa-edit"></i>
								<span class="pl-5">Edit tracking numbers</span>
							</button>
							<div v-else>
								<button class="button is-danger" @click="onCancel()">
									<i class="fa fa-times"></i>
									<span class="pl-5">Cancel</span>
								</button>
							</div>
							<button class="button is-success ml-5" @click="submit()">
								<i class="fa fa-check"></i>
								<span class="pl-5">Submit</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card-content">
				<table class="table is-hoverable is-fullwidth responsive">
					<thead>
						<tr>
							<th>Outbound</th>
							<th>Tracking numbers</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(outbound, index) in outbounds">
							<td>
								<text-input v-model="outbounds[index].id" :defaultValue="outbounds[index].id" 
									label="Barcode input" 
									:required="true"
									name="id"
									type="text"
									:editable="isEditing"
									:hideLabel="true">
								</text-input>
							</td>
							<td>
								<text-input v-model="outbounds[index].tracking_numbers" :defaultValue="outbounds[index].tracking_numbers" 
									label="Barcode input" 
									:required="true"
									name="tracking_numbers"
									type="text"
									:editable="isEditing"
									:hideLabel="true">
								</text-input>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<confirmation :isConfirming="confirmTrackingNumberUpdate"
        				title="Confirmation"
        				message="Tracking numbers will be saved into all the outbounds. Proceed?"
        				@close="confirmTrackingNumberUpdate = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				outbounds: [],
				form: new Form({
					outbounds: []
				}),
				input: '',
				current_index: -1,
				isEditing: false,
				confirmTrackingNumberUpdate: false,
				action: '/admin/outbound/trackings/update'
			};
		},

		methods: {
			barcodeSubmit() {
				if(this.input !== '')
				{
					if(this.input.startsWith("POUT") || this.input.startsWith("PL"))
					{
						this.outbounds.push({'id': this.input, 'tracking_numbers': ''});
						this.form.outbounds.push({'id': this.input, 'tracking_numbers': ''});
						this.current_index++;
					}
					else
					{
						if(this.outbounds[this.current_index].tracking_numbers !== '')
							this.outbounds[this.current_index].tracking_numbers += ";";
						this.outbounds[this.current_index].tracking_numbers += this.input;

						if(this.form.outbounds[this.current_index].tracking_numbers !== '')
							this.form.outbounds[this.current_index].tracking_numbers += ";";
						this.form.outbounds[this.current_index].tracking_numbers += this.input;

					}
				}

				this.input = '';

			},

			editTracking() {
				this.isEditing = true;
			},

			onCancel() {
				this.isEditing = false;
				this.outbounds = this.form.outbounds;
			},

			submit() {
				this.confirmTrackingNumberUpdate = true;
			},

			onSubmit() {
				this.form.outbounds = this.outbounds;
				this.form.post(this.action)
					.then(response => this.onSuccess(response))
					.catch(response => this.onError(response));
			},

			onSuccess(response){
				this.outbounds = response.outbounds;
				this.form.outbounds = response.outbounds;
				this.confirmTrackingNumberUpdate = false;
			},

			onError(response){

			}
		}	
	}
</script>