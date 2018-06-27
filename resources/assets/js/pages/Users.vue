<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Users
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
			
			<profile :user="selectedUser"
					:canManage="can_manage" 
					@back="back()"
					v-if="isViewing">
			</profile>
		</transition>


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
				searchables: "name,email",
				selectedInbound: '',
				isViewing: false,
				message: '',
				confirmApproval: false,
				selectedUser: ''
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));
			this.$events.on('approve', data => this.approve(data));
			if(!this.can_manage)
			{
				this.view(null);
			}

			if(this.getParameterByName('name')) {
				this.searchName(this.getParameterByName("name"));
			}
		},

		methods: {
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

			onSubmit() {
				axios.post("/internal/user/" + this.selectedUser.id + "/approval")
					.then(response => this.onSuccess(response));
			},

			onSuccess(response) {
				flash(response.data.message);
				this.confirmApproval = false;
				this.$refs.users.refreshTable();
			},

			searchName(name) {
				let filters = {};
				filters.text = name;
				filters.fromParam = true;
				this.$events.fire("filter-param", filters);
			}
		}
	}
</script>