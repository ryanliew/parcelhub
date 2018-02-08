<template>
	<div>
		<div class="field">
		    <p class="control">
		        <div class="b-checkbox is-primary mt-30">
		            <input :id="name" 
		            		class="styled" 
		            		type="checkbox" 
		            		:checked="value" @input="updateValue($event.target.checked)">
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
				finalValue: ''
			};
		},

		methods: {
			updateValue(value) {
				//console.log(value);
				this.finalValue = value;
				this.$emit('input', value);
			}
		},

		computed: {
			value() {
				return this.finalValue == '' 
						? this.defaultValue 
						: this.finalValue;
			}
		}	
	}
</script>