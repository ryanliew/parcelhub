<template>
	<div class="nav-container">
		<div class="container">
			<nav class="navbar" role="navigation" aria-label="main navigation">
			  	<div class="navbar-brand">
			    	<a class="navbar-item is-size-4" href="/">
			      		<span v-text="title"></span>
			    	</a>

				    <button class="button navbar-burger">
				      <span></span>
				      <span></span>
				      <span></span>
				    </button>
			  	</div>

			  	<div class="navbar-menu">
			  		<div class="navbar-start">
			  			<!-- Todo Search component here -->
			  		</div>
			  		<div class="navbar-end">
			  			<a class="navbar-item" href="/">Home</a>
			  			<a class="navbar-item" href="/lots" v-text="panelTitle"></a>
			  			<a class="navbar-item" @click="logout">Logout</a>
			  		</div>
			  	</div>
			</nav>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['current', 'title', 'can_manage'],
		data() {
			return {

			};
		},

		methods: {
			logout() {
				axios.post('/logout')
					.then(this.redirect);
			},

			redirect(response){
				window.location.href = '/login';
			}
		},

		computed: {
			panelTitle() {
				if(this.can_manage)
				{
					return 'Admin Panel';
				}
				return 'My Account';
			}
		}
	}
</script>