<template>
	<div>
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Users
							</div>
						</div>
						<div class="level-right">
							<div class="level-item">
								<button class="button is-primary" @click="modalOpen()" >
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create new User</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="users" 
								:fields="fields" 
								url="/internal/users/list"
								:searchables="searchables">	
					</table-view>
				</div>
			</div>

			<modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ 'Create User' }}</template>
				<form @submit.prevent="submit" 
						@keydown="form.errors.clear($event.target.name)" 
						@input="form.errors.clear($event.target.name)"
						@keyup.enter="submit">

	          		
				<div class="field">
						<text-input v-model="form.name" :defaultValue="form.name" 
									label="Name" 
									:required="true"
									name="name"
									type="text"
									:editable="true"
									:error="form.errors.get('name')"
									>
						</text-input>
				</div>		
				<div class="field">
					<text-input v-model="form.email" :defaultValue="form.email" 
								label="E-mail" 
								:required="true"
								name="email"
								type="text"
								:editable="true"
								:error="form.errors.get('email')">
					</text-input>
				</div>
	          	<div class="field">
	          		<text-input v-model="form.address" :defaultValue="form.address" 
								label="Address" 
								:required="true"
								name="address"
								type="text"
								:editable="true"
								:error="form.errors.get('address')">
					</text-input>
	          	</div>
				  <div class="field">
	          		<text-input v-model="form.address_2" :defaultValue="form.address_2" 
								label="Address 2" 
								:required="false"
								name="address_2"
								type="text"
								:editable="true"
								:error="form.errors.get('address_2')">
					</text-input>
	          	</div>
                  <div class="field">
	          		<text-input v-model="form.phone" :defaultValue="form.phone" 
								label="Phone" 
								:required="true"
								name="phone"
								type="text"
								:editable="true"
								:error="form.errors.get('phone')">
					</text-input>
	          	</div>
				<div class="columns">
					<div class="column">
					  	<div class="field">
							<text-input v-model="form.state" :defaultValue="form.state" 
											label="State" 
											:required="false"
											name="state"
											type="text"
											:editable="true"
											:error="form.errors.get('state')">
								</text-input>
							</div>
					  	</div>
					<div class="column">
						<div class="field">
							<text-input v-model="form.postcode" :defaultValue="form.postcode" 
										label="Postcode" 
										:required="false"
										name="postcode"
										type="text"
										:editable="true"
										:error="form.errors.get('postcode')">
							</text-input>
						</div>
					</div>
					<div class="column">
						<div class="field">
							<text-input v-model="form.country" :defaultValue="form.country" 
										label="Country" 
										:required="false"
										name="country"
										type="text"
										:editable="true"
										:error="form.errors.get('country')">
							</text-input>
						</div>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<label>Select branches</label>
						<multiselect v-model="selectedBranches" :options="branchesOptions" 
									:close-on-select="false" 
									:multiple="true" 
									:clear-on-select="false"
									:custom-label="customLabel"
									track-by="value">
							<template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} branches selected</span></template>
						</multiselect>
					</div>
				</div>
				
          	</form>

          	<template slot="footer">
				<button :title="submitTooltip" :class="isSubmittingClass" type="button" class="button is-primary" @click="createSubmit" :disabled="!canSubmit">Submit</button>
          	</template>
			</modal>
			
			<profile :user="selectedUser"
					:canManage="can_manage" 
					@back="back()"
					v-if="isViewing">
			</profile>

        <confirmation :isConfirming="confirmApproval"
        				title="Confirmatzion"
        				:message="message"
        				@close="confirmApproval = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Profile from '../objects/Profile.vue';
	import Multiselect from 'vue-multiselect'

	Vue.component('multiselect', Multiselect)
	export default {
		props: ['can_manage'],

		components: { TableView, Profile },

		data() {
			return {
				fields: [
					{name: 'name', sortField: 'name', title: 'Name'},
					{name: 'created_at', sortField: 'created_at', title: 'Member since', callback: 'date'},
					{name: 'email', sortField: 'email'},
					{name: 'phone', sortField: 'phone'},
					{name: 'role_name', title: 'Type', callback: 'userTypeFormat'},
					{name: '__component:users-actions', title: 'Actions'}	
				],
				dialogActive: false,
				form: new Form({
					email: '',
					name: '',
					phone: '',
					address: '',
					address_2: '',
                    state: '',
                    postcode: '',
                    country: '',
					branches: []
				}),
				searchables: "name,email",
				selectedInbound: '',
				isViewing: false,
				message: '',
				confirmApproval: false,
				confirmSubmit: false,
				selectedUser: '',
				selectedBranches: [],
				branchesOptions: [],
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));
			this.$events.on('approve', data => this.approve(data));
			this.getBranches();
			if(!this.can_manage)
			{
				this.view(null);
			}

			if(this.getParameterByName('name')) {
				this.searchName(this.getParameterByName("name"));
			}
		},
		methods: {
			getBranches() {
				axios.get('/internal/branches/selector')
					.then(response => this.setBranches(response));
			},

			setBranches(response) {
				this.branchesOptions = response.data.map(branches =>{
					let obj = {};
					obj['label'] = branches.branch_name;
					obj['value'] = branches.id;
					return obj;
				});
			},
			getParameterByName(name, url) {
			    if (!url) url = window.location.href;
			    name = name.replace(/[\[\]]/g, "\\$&");
			    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			        results = regex.exec(url);
			    if (!results) return null;
			    if (!results[2]) return '';
			    return decodeURIComponent(results[2].replace(/\+/g, " "));
			},

			view(data) {
				this.selectedUser = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.selectedUser = '';
			},

			approve(data) {
				this.selectedUser = data;
				this.message = data.is_approved == false ? "Approving user " + data.name + " to access the system. Continue?" : "Banning user " + data.name + " to access the system. Continue?";
				this.confirmApproval = true;
			},

			createSubmit() {
				if(this.selectedBranches != []) {
					this.form.branches = this.selectedBranches.map(branch => {
						return branch.value;
					});
				}
				this.form.post("/internal/users/store")	
					.then(data => this.onCreateSuccess(data));
			},

			onSubmit() {
				axios.post("/internal/user/" + this.selectedUser.id + "/approval")
					.then(response => this.onSuccess(response));
			},

			onCreateSuccess(response) {
				flash(response.message);
				this.dialogActive = false;
				this.$refs.users.refreshTable();
			},

			onSuccess(response) {
				flash(response.data.message);
				this.confirmApproval = false;
				this.dialogActive = false;
			},

			searchName(name) {
				let filters = {};
				filters.text = name;
				filters.fromParam = true;
				this.$events.fire("filter-param", filters);
			},

			modalOpen() {
				this.form.reset();
				this.selectedBranches = [];
				this.dialogActive = true;
			},

			submit() {
				this.confirmSubmit = true;
			},

			onFail() {

			},

			customLabel({label, value}) {
				return label;
			}
		},
		computed: {
			isSubmittingClass() {
				return this.form.submitting ? "is-loading" : "";
			},
			canSubmit() {
				return this.selectedBranches.length > 0;
			},

			submitTooltip() {
				return this.canSubmit ? "" : "Please select at least 1 branch";
			},
		}
	}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>