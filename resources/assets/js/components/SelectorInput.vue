<template>
	<div>
		<label class="select-label">
			<span v-text="label"></span>
			<span v-if="required" class="is-danger">*</span>
		</label>
		<div class="control">
			<v-select :options="potentialData" :value="this.defaultData" @input="updateValue" name="name" :placeholder="placeholder">
			</v-select>
		</div>
		<span class="help is-danger" v-if="error" v-text="error"></span>
	</div>
</template>

<script>
	import vSelect from 'vue-select';
	export default {
		props: ['potentialData', 'label', 'defaultData', 'error', 'name', 'placeholder', 'required'],

		components: { vSelect },

		data() {
			return {
				
			};
		},

		methods: {
			updateValue(value) {
				this.$emit('input', value);
			}
		}

	}
</script>