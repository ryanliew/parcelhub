<template>
	<div>
		<div class="file has-name is-boxed">
			<figure class="image is-128x128" v-if="defaultImage.src && (defaultImage.src.endsWith('.jpg') || defaultImage.src.endsWith('.png') || defaultImage.file.type.match('/^image.*$/'))">
				<img :src="defaultImage.src">
			</figure>
		  	<label class="file-label pl-5">
			    <input class="file-input" :accept="accept" type="file" :name="name" @change="onChange">
			    <span class="file-cta">
				     <span class="file-icon">
				        <i class="fa fa-image"></i>
				     </span>
				     <p v-html="fileLabel">
				     </p>
			    </span>
			    <span class="file-name" v-text="defaultImage.name || defaultImage.file.name"></span>
		  	</label>
		</div>
	  	<span class="help is-danger" v-if="error" v-text="error"></span>
	 </div>
</template>

<script>
	export default {
		props: ['label', 'name', 'required', 'defaultImage', 'error', 'accept'],
		data() {
			return {
				
			};
		},
		methods: {
			onChange(e) {
				if( ! e.target.files.length ) return;

				let file = e.target.files[0];

				let reader = new FileReader();

				reader.readAsDataURL(file);

				reader.onload = e => {
					let src = e.target.result;

					this.$emit('loaded', { src, file });
				};	
			}
		},

		computed: {
			fileName() {
				return this.defaultImage.name;
			},

			fileLabel() {
				let text = ( this.defaultImage.src ? 'Change ' : 'Upload ' ) + this.label;

				if(this.required) {
				    text += '<span class="is-danger">*</span>';
				}

				return text;
			}
		}	
	}
</script>