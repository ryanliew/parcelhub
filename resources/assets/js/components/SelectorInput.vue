<template>
	<div>
		<label class="select-label">
			<span v-text="label"></span>
			<span v-if="required" class="is-danger">*</span>
		</label>
		<div class="control" :class="canClearCss">
			<v-select :multiple="multiple" :options="potentialData" :value="this.defaultData" @input="updateValue" name="name" :placeholder="placeholder" :closeOnSelect="!multiple || true">
			</v-select>
		</div>
		<span class="help is-danger" v-if="error" v-text="error"></span>
	</div>
</template>

<script>
	import vSelect from 'vue-select';
	export default {
		props: ['potentialData', 'label', 'defaultData', 'error', 'name', 'placeholder', 'required', 'multiple', 'unclearable'],

		components: { vSelect },

		data() {
			return {
				
			};
		},

    mounted() {
      if(this.potentialData.length == 1) {
        // Auto select if there's only one data
        this.updateValue(this.potentialData[0]);
      }
    },

		methods: {
			updateValue(value) {
				this.$emit('input', value);
			}
		},	

		computed: {
			canClearCss() {
				return this.unclearable ? "unclearable" : "";
			}
		}

	}
</script>