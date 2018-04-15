<template>
	<div class="nav-container">
		<div class="container">
			<nav class="navbar" role="navigation" aria-label="main navigation">
			  	<div class="navbar-brand">
			    	<a class="navbar-item is-size-4" href="/">
			      		<span v-text="title"></span>
			    	</a>

				    <button class="button navbar-burger" :class="burgerClass" @click="burgerActive = !burgerActive">
				      <span></span>
				      <span></span>
				      <span></span>
				    </button>
			  	</div>

			  	<div class="navbar-menu" :class="burgerClass">
			  		<div class="navbar-start">
			  			<!-- Todo Search component here -->
			  		</div>
			  		<div class="navbar-end">
			  			<div class="navbar-item has-dropdown" :class="dropdownClass" @mouseover="dropdownActive = true" @mouseleave="dropdownActive = false">
			  				<a class="navbar-link">
			  					Welcome, {{ user }}
			  				</a>
				  			<div class="navbar-dropdown">
					  			<a class="navbar-item" href="/">Home</a>
					  			<a class="navbar-item" :href="panelLink" v-text="panelTitle"></a>
					  			<a class="navbar-item" @click="logout">Logout</a>
					  		</div>
					  	</div>
			  		</div>
			  	</div>
			</nav>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['current', 'title', 'can_manage', 'user'],
		data() {
			return {
				burgerActive: false,
				dropdownActive: false
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
			},

			burgerClass() {
				return this.burgerActive ? 'is-active' : '';
			},

			dropdownClass() {
				return this.dropdownActive ? 'is-active' : '';
			},

			panelLink() {
				if(this.can_manage)
				{
					return '/dashboard';
				}
				return '/lots';
			}
		}
	}
</script>