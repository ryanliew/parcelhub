<template>
	<transition name="fade">
		<div class="modal is-active" v-if="active">
		  	<div class="modal-background" @click="close"></div>
		  	<div class="modal-content">
		  		<div class="box">
		  			<div class="level">
		  				<div class="level-left">
		  					<p class="modal-card-title"><slot name="header"></slot></p>
		  				</div>
		  				<div class="level-right">
		  					<button class="delete" aria-label="close" @click="close"></button>
		  				</div>
		  			</div>
				
					<hr>

		    		<slot></slot>

		    		<hr>

		      		<slot name="footer"></slot>
		      	</div>
		  	</div>
		</div>
	</transition>
</template>

<script>
	export default {
		props: ['active'],
		data() {
			return {

			};
		},

		methods: {
			close() {
				this.$emit('close');
			}
		}	
	}
</script>