<template>
	<div class="nav-container">
		<div class="container">
			<nav class="navbar" role="navigation" aria-label="main navigation">
			  	<div class="navbar-brand">
			    	<a class="navbar-item is-size-4" href="/">
			      		KANGAROO
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
			  			<a class="navbar-item" href="/">Dashboard</a>
			  			<a class="navbar-item" href="/customers">Customers</a>
			  			<a class="navbar-item" href="/operators">Operators</a>
			  			<div class="navbar-item has-dropdown is-hoverable">
			  				<a class="navbar-link" href="/units">Units</a>
			  				<div class="navbar-dropdown is-boxed">
			  					<a class="navbar-item" href="/units/create">Create unit</a>
			  					<a class="navbar-item" href="/transactions/create">Create transactions</a>
			  				</div>
			  			</div>
			  			<a class="navbar-item" @click="logout">Logout</a>
			  		</div>
			  	</div>
			</nav>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['current'],
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
		}	
	}
</script>