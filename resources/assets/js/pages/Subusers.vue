<template>
	<div>
		<transition name="slide-fade" mode="out-in">
			<div class="card" v-if="!isViewing && !isCreating">
				<div class="card-header">
					<div class="card-header-title level">
						<div class="level-left">
							<div class="level-item">
								Subusers
							</div>
						</div>
						<div class="level-right" v-if="can_manage">
							<div class="level-item">
								<button class="button is-primary" @click="create">
									<i class="fa fa-plus-circle"></i>
									<span class="pl-5">Create subuser</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-content">
					<table-view ref="subusers" 
								:fields="fields" 
								url="/subuser/index"
								:searchables="searchables">	
					</table-view>
				</div>
			</div>
			
			<subuser :subuser="selectedUser"
					:canManage="can_manage"
					:parent="parent" 
					@back="back()"
					v-else>
			</subuser>
		</transition>
	</div>
</template>

<script>
	import TableView from '../components/TableView.vue';
	import Subuser from '../objects/Subuser.vue';

	export default {
		props: ['can_manage', 'parent'],

		components: { TableView, Subuser },

		data() {
			return {
				fields: [
					{name: 'name', sortField: 'name', title: 'Name'},
					{name: 'created_at', sortField: 'created_at', title: 'Member since', callback: 'date'},
					{name: 'email', sortField: 'email'},
					{name: 'phone', sortField: 'phone'},
					{name: '__component:subusers-actions', title: 'Actions'}	
				],
				searchables: "name,email",
				selectedUser: '',
				isViewing: false,
				isCreating: false,
				deleteForm: new Form({})
			};
		},

		mounted() {
			this.$events.on('view', data => this.view(data));
			this.$events.on('delete', data => this.delete(data));
		},

		methods: {
			create() {
				this.isCreating = true;
			},

			view(data) {
				this.selectedUser = data;
				this.isViewing = true;
			},

			delete(data) {
				this.selectedUser = data;
				this.deleteForm.post('/subuser/delete/' + this.selectedUser.id)
					.then(response => this.onSuccess(response))
			},

			onSuccess() {
				this.$refs.subusers.refreshTable();
			},

			back() {
				this.isViewing = false;
				this.isCreating = false;
				this.selectedUser = '';
			}
		}
	}
</script>