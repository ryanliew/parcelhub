<template>
	<article class="message">
		<div class="message-header">
			<slot name="title"></slot>

			<button class="button is-small is-dark fa" :class="iconClass" @click="toggle"></button>
		</div>
		<div class="message-body" v-if="isActive">
			<div class="message-content">
				<slot></slot>
			</div>
		</div>
	</article>
</template>

<script>
	export default {
		props: [],
		data() {
			return {
				isActive: false
			};
		},

		methods: {
			toggle() {
				this.isActive = !this.isActive;
			}
		},

		computed: {
			iconClass() {
				return this.isActive ? "fa-minus " : "fa-plus";
			}
		}	
	}
</script>