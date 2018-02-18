<template>
	<div>
		<label class="select-label">
			<span v-text="label"></span>
			<span v-if="required" class="is-danger">*</span>
		</label>
		<div class="control">
			<v-select :multiple="multiple" :options="potentialData" :value="this.defaultData" @input="updateValue" name="name" :placeholder="placeholder" label="name" :closeOnSelect="false">
				<template slot="option" slot-scope="option">
					<div class="level">
						<figure class="image is-48x48">
							<img :src="option.picture">
						</figure>
						<span class="level-item" v-text="option.name"></span>
					</div>
				</template>
			</v-select>
		</div>
		<span class="help is-danger" v-if="error" v-text="error"></span>
	</div>
</template>

<script>
	import vSelect from 'vue-select';
	export default {
		props: ['potentialData', 'label', 'defaultData', 'error', 'name', 'placeholder', 'required', 'multiple'],

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