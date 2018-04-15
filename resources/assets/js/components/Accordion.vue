<template>
	<article class="message">
		<div class="message-header" @click="toggle">
			<slot name="title"></slot>

			<a class="button is-small is-dark fa" :class="iconClass"></a>
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