<template>
	<div>
		<div class="field">
		    <p class="control">
		        <div class="b-checkbox is-primary">
		            <input :id="name" 
		            		class="styled" 
		            		type="checkbox" 
		            		:checked="defaultChecked" @click="updateValue">
		            <label :for="name" v-text="label" @click="updateValue"></label>
		        </div>
		    </p>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['defaultChecked', 'editable', 'label', 'name'],
		data() {
			return {
				
			};
		},

		methods: {
			updateValue(value) {
				console.log("Updating");
				this.$emit('input', !this.defaultChecked);
			}
		},

		computed: {
			
		}	
	}
</script>