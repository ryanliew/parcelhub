<template>
	<div>
		<div class="field">
		    <p class="control">
		        <div class="b-checkbox is-primary">
		            <input :id="name" 
		            		class="styled" 
		            		type="checkbox" 
		            		:checked="defaultChecked" @input="updateValue($event.target.checked)">
		            <label :for="name" v-text="label"></label>
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
				this.$emit('input', value);
			}
		},

		computed: {
			
		}	
	}
</script>