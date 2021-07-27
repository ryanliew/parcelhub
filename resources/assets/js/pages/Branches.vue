<template>
    <div>
        <div class="card">
            <div class="card-header">
				<div class="card-header-title level">
                    <div class="level-left">
						<div class="level-item">
							Branches
						</div>
					</div>
                </div>
                <div class="level-right">
					<div class="level-item">
						<button class="button is-primary" @click="modalOpen()">
							<i class="fa fa-plus-circle"></i>
							    <span class="pl-5">Create new Branch</span>
							</button>
					</div>
				</div>
            </div>
            <div class="card-content">
				<table-view ref="branches" 
							:fields="fields" 
							url="/internal/branches">	
				</table-view>
			</div>
        </div>

        <modal :active="dialogActive" @close="dialogActive = false">
			<template slot="header">{{ dialogTitle }}</template>

			<form @submit.prevent="submit" 
					@keydown="form.errors.clear($event.target.name)" 
					@input="form.errors.clear($event.target.name)"
					@keyup.enter="submit" v-if="type == 'Edit' || type == ''">

	          	<div class="field">
	          		<text-input v-model="form.codename" :defaultValue="form.codename" 
								label="Code Name" 
								:required="true"
								name="codename"
								type="text"
								:editable="true"
								:error="form.errors.get('codename')"
								:focus="true">
					</text-input>
	          	</div>
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
	          		<text-input v-model="form.phone" :defaultValue="form.phone" 
								label="Phone" 
								:required="true"
								name="phone"
								type="text"
								:editable="true"
								:error="form.errors.get('phone')">
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
	          		<text-input v-model="form.state" :defaultValue="form.state" 
								label="State" 
								:required="true"
								name="state"
								type="text"
								:editable="true"
								:error="form.errors.get('state')">
					</text-input>
	          	</div>
                <div class="field">
	          		<text-input v-model="form.postcode" :defaultValue="form.postcode" 
								label="Postcode" 
								:required="true"
								name="postcode"
								type="text"
								:editable="true"
								:error="form.errors.get('postcode')">
					</text-input>
	          	</div>
                <div class="field">
	          	    <text-input v-model="form.country" :defaultValue="form.country" 
								label="Country" 
								:required="true"
								name="country"
								type="text"
								:editable="true"
								:error="form.errors.get('country')">
					</text-input>
	          	</div>
          	</form>

            <template v-if="type == 'Access'">
                <vue-list-picker :left-items="leftItems" :right-items="rightItems" :title-left="leftTitle" :title-right="rightTitle"/>
            </template>

          	<template slot="footer">
				<button class="button is-primary" @click="submit">Submit</button>
          	</template>
		</modal>

        <confirmation :isConfirming="confirmSubmit"
        				title="Confirmation"
        				:message="confirmationMessage"
        				@close="confirmSubmit = false"
        				@confirm="onSubmit">
        </confirmation>
    </div>
</template>

// <script>
    import TableView from '../components/TableView.vue';

    export default {
        props: [''],

		components: { TableView },

        data() {
            return {
                fields:[
                    {name: 'codename', sortField: 'codename', title: 'Code Name'},
                    {name: 'branch_name', sortField: 'branch_name', title: 'Branch Name'},
                    {name: 'branch_address', sortField: 'branch_address', title: 'Address'},
                    {name: '__component:branches-actions', title: 'Actions'},
                ],
                selectedBranches: '',
                dialogActive: false,
                form: new Form({
					id: '',
					codename: '',
					name: '',
					phone: '',
					address: '',
                    state: '',
                    postcode: '',
                    country: ''
				}),
				forms: new Form({
					branch_id: '',
					id: []
				}),
                confirmSubmit: false,
				isDeleting: false,
                leftItems: [],
                rightItems: [],
                leftTitle: 'Access',
                rightTitle: 'No Access',
				type: '',
            }
        },

        methods: {
            submit() {
				this.confirmSubmit = true;
			},

			onSubmit() {
				this.confirmSubmit = false;
				if(this.type == 'Access') {
					var store = [];
					this.leftItems.forEach(function (data) {
						store.push(data.key);
					});

					this.forms.id = store;
					this.forms.branch_id = this.selectedBranches.id;

					this.forms.post(this.action)	
						.then(data => this.onSuccess())
						.catch(error => this.onFail(error));
				}
				else if(this.type == 'Delete') {
					axios.get('/branches/delete/' + this.selectedBranches.id)
					.then(response => this.onDeleteSuccess(response));
				}
				else {
					this.form.post(this.action)	
						.then(data => this.onSuccess())
						.catch(error => this.onFail(error));
				}
			},
			onDeleteSuccess(response) {
				this.isDeleting = false;
				this.dialogActive = false;
				flash(response.data.message);
				this.$refs.branches.refreshTable();
			},
			onSuccess() {
				this.dialogActive = false;
				this.$refs.branches.refreshTable();
			},
            edit(data) {
                this.selectedBranches = data;
				this.type = 'Edit';
                this.form.id = data.id;
				this.form.codename = data.codename;
				this.form.name = data.branch_name;
				this.form.phone = data.branch_phone;
				this.form.address = data.branch_address;
				this.form.state = data.branch_state;
				this.form.postcode = data.branch_postcode;
				this.form.country = data.branch_country;
                this.dialogActive = true;
            },
			access(data){
				this.selectedBranches = data;
				this.type = 'Access';
				this.getAccess(data.id);
				this.dialogActive = true;
			},
			delete(data) {
				this.selectedBranches = data;
				this.type = 'Delete';
				this.confirmSubmit = true;
				this.isDeleting = true;
			},
            modalOpen() {
				this.form.reset();
				this.selectedBranches = '';
				this.type = '';
				this.dialogActive = true;
			},
            getAccess(data) {
                axios.get('/branches/access/' + data)
                .then(response => this.getSuccess(response));
            },
            getSuccess(response) {
                this.rightItems = response.data[1].map(function(user) {
					let obj = {};

					obj['key'] = user.id;
					obj['content'] = user.name;

					return obj;
				});

				this.leftItems = response.data[0].map(function(user) {
					let obj = {};

					obj['key'] = user.id;
					obj['content'] = user.name;

					return obj;
				});
			},
        },
        mounted() {
			this.$events.on('edit', data => this.edit(data));
			this.$events.on('access', data => this.access(data));
			this.$events.on('delete', data => this.delete(data));
		},
        computed: {
            dialogTitle() {
				if(this.selectedBranches) {
					if(this.type == 'Edit') {
						return "Edit " + this.selectedBranches.branch_name;
					}
					else if(this.type == 'Delete') {

					}
					else if(this.type == 'Access') {
						return "Access " + this.selectedBranches.branch_name + '\'s branch';
					}
				}
				return "Create new Branch";
			},
            action() {
				var actions = ''

				if(this.type == 'Edit') {
					 actions = "update";
				}
				else if(this.type == 'Access') {
					 actions = "access/edit";
				}
				else {
					 actions = 'store';
				}
				return "/branches/" + actions;
			},
            confirmationMessage() {
				if(this.type == 'Edit') {
					return "Confirm branch update?";
				}
				else if(this.type == '') {
					return "Confirm adding new branch?";
				}
				else if(this.type == 'Access') {
					return "Confirm update access?"
				}
				else if(this.type == 'Delete') {
					return "Confirm delete this branch?";
				}
			}

        }
    }
</script>