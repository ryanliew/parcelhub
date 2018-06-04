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
				isViewing: false
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));

			if(!this.can_manage)
			{
				this.view(null);
			}
		},

		methods: {

			view(data) {
				this.selectedUser = data;
				this.isViewing = true;
			},

			back() {
				this.isViewing = false;
				this.selectedUser = '';
			}
		}
	}
</script>