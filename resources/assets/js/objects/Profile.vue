<template>
	<div>
		<loader v-if="loading"></loader>
		<div class="card">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Profile
							</div>
						</div>
						<div class="level-right" v-if="canManage">
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
					<form @submit.prevent="onSubmit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)"
						@keyup.enter="onSubmit">
						<div class="columns">
				         	<div class="column">
				          		<text-input v-model="form.name" :defaultValue="form.name" 
											label="Name" 
											:required="true"
											name="name"
											type="text"
											:editable="true"
											:error="form.errors.get('name')"
											:focus="true">
								</text-input>
							</div>
							<div class="column">
								<text-input v-model="form.phone" :defaultValue="form.phone" 
											label="Phone" 
											:required="true"
											name="phone"
											type="text"
											:editable="true"
											:error="form.errors.get('phone')">
								</text-input>
							</div>
						</div>
						<div class="field">
							<text-input v-model="form.address" :defaultValue="form.address" 
											label="Address line 1" 
											:required="true"
											name="address"
											type="text"
											:editable="true"
											:error="form.errors.get('address')">
							</text-input>
						</div>
						<div class="field">
							<text-input v-model="form.address_2" :defaultValue="form.address_2" 
											label="Address line 2" 
											:required="false"
											name="address_2"
											type="text"
											:editable="true"
											:error="form.errors.get('address_2')">
							</text-input>
						</div>
						<div class="columns">
							<div class="column">
								<text-input v-model="form.postcode" :defaultValue="form.postcode" 
												label="Postcode" 
												:required="true"
												name="postcode"
												type="text"
												:editable="true"
												:error="form.errors.get('postcode')">
								</text-input>
							</div>
							<div class="column">
								<text-input v-model="form.state" :defaultValue="form.state" 
												label="State" 
												:required="true"
												name="state"
												type="text"
												:editable="true"
												:error="form.errors.get('state')">
								</text-input>
							</div>
							<div class="column">
								<text-input v-model="form.country" :defaultValue="form.country" 
												label="Country" 
												:required="true"
												name="country"
												type="text"
												:editable="true"
												:error="form.errors.get('country')">
								</text-input>
							</div>
						</div>

				        <button class="button is-primary mt-15" :disabled="this.form.errors.any()" :class="buttonClass">Update</button>
		          	</form>
				</div>
			</div>
	</div>
</template>

<script>
	export default {
		props: ['canManage', 'user'],
		data() {
			return {
				loading: true,
				form: new Form({
					name: '',
					phone: '',
					address: '',
					address_2: '',
					state: '',
					postcode: '',
					country: '',
					id: ''
				}),
				profile: ''
			};
		},

		mounted() {
			this.getUser();
		},

		methods: {
			getUser() {
				if(!this.user)
				{
					this.loading = true;
					axios.get('/internal/user')
						.then(response => this.setUser(response.data));
				}
				else
				{
					this.setUser(this.user);
				}
			},

			setUser(user) {
				this.profile = user;

				this.form.id = this.profile.id;

				this.form.name = this.profile.name;
				this.form.phone = this.profile.phone;
				this.form.address = this.profile.address;
				this.form.address_2 = this.profile.address_2;
				this.form.state = this.profile.state;
				this.form.postcode = this.profile.postcode;
				this.form.country = this.profile.country;

				this.loading = false;

			},

			back() {
				this.$emit('back');
			},

			onSubmit() {
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.catch(response => this.onError(response));
			},

			onSuccess() {
				this.form.submitting = false;
				if(this.user){
					this.back();
				}
				else
				{
					this.getUser();
				}
			},

			onError() {
				this.form.submitting = false;
			},
		},

		computed: {

			action() {
				let action = '/user/update';
				return action;
			},

			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			}
		}
	}
</script>