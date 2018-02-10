<template>
	<transition name="fade">
		<div class="modal is-active" v-if="active">
		  	<div class="modal-background" @click="close"></div>
		  	<div class="modal-card">
		    	<header class="modal-card-head">
		      		<p class="modal-card-title"><slot name="header"></slot></p>
		      		<button class="delete" aria-label="close" @click="close"></button>
		    	</header>
		    	<section class="modal-card-body">
		    		<slot></slot>
		    	</section>
		    	<footer class="modal-card-foot">
		      		<slot name="footer"></slot>
		    	</footer>
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