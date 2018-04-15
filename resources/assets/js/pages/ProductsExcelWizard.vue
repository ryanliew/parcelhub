<template>
	<div>
		<div class="steps">
		 	<div class="step-item is-active">
		    	<div class="step-marker">
		      		
		    	</div>
		    	<div class="step-details">
		      		<p class="step-title">Step 1</p>
		      		<p>Download the excel file.</p>
		    	</div>
		  	</div>
		  	<div class="step-item">
		    	<div class="step-marker"></div>
		    	<div class="step-details">
		      		<p class="step-title">Step 2</p>
		      		<p>Fill in and upload the excel file.</p>
		    	</div>
		  	</div>
		  	<div class="step-item">
		    	<div class="step-marker"></div>
		    	<div class="step-details">
		      		<p class="step-title">Step 3</p>
		      		<p>Upload the photos.</p>
		    	</div>
		  	</div>
		  	<div class="step-item">
		    	<div class="step-marker"></div>
		    	<div class="step-details">
		      		<p class="step-title">Step 4</p>
		      		<p>Complete!</p>
		    	</div>
		  	</div>
		  	<div class="steps-content">
		  		<div class="step-content has-text-centered is-active">
		  			<a class="button is-large is-primary" href="/download/product/template" target="_blank">
		  				<i class="fa fa-download"></i> <span class="ml-5">Download excel template</span>
		  			</a>
		  		</div>
		  		<div class="step-content has-text-centered is-flex-center">
		  			<img class="is-responsive" src="/images/input.PNG"> 
		  			<p><i class="is-dark-grey">Example input format</i></p>
		  			<div class="field mt-5">
			  			<image-input v-model="excelTemplate" :defaultImage="excelTemplate"
				    				@loaded="changeExcelTemplate"
				    				label="excel template"
				    				name="file"
				    				:required="false"
				    				accept=".xlsx,.xls"
				    				:error="form.errors.get('file')">
				    	</image-input>
				    </div>
			    	<button class="button is-primary mt-5" @click="submit">Submit excel file</button>
		  		</div>
		  		<div class="step-content has-text-centered">
		  			<p>Make sure that the photo name is identical with the product SKU (case sensitive).</p>
		  			<vue2Dropzone ref="photosUpload" id="dropzone" :options="dropzoneOptions">
		  			</vue2Dropzone>
		  		</div>
		  		<div class="step-content has-text-centered">
		  			<h1 class="title is-4">{{ number }} of products have been processed!</h1>
		  		</div>
		  	</div>
		  	<div class="steps-actions">
			    <div class="steps-action">
			      <a href="#" data-nav="previous" class="button is-light">Previous</a>
			    </div>
			    <div class="steps-action">
			      <a href="#" data-nav="next" class="button is-light" ref="next">Next</a>
			    </div>
			</div>
		  	
		</div>
		
		<confirmation :isConfirming="confirm"
        				title="Confirmation"
        				message="Products are going to be created. Proceed?"
        				@close="confirm = false"
        				@confirm="onSubmit">
        </confirmation>
	</div>
</template>

<script>
	import bulmaSteps from 'bulma-extensions/bulma-steps/dist/bulma-steps.min.js';
	import vue2Dropzone from 'vue2-dropzone';
	import 'vue2-dropzone/dist/vue2Dropzone.css';

	export default {
		props: [''],

		components: { vue2Dropzone },

		data() {
			return {
				excelTemplate: {name: 'No file selected'},
				form: new Form({
					file: ''
				}),
				dropzoneOptions: {
					url: '/product/store/images',
			        thumbnailWidth: 150,
			        maxFilesize: 2,
			        headers: { "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]').content }

				},
				confirm: false,
				number: 0,
				action: '/excel/store'
			};
		},

		methods: {
			changeExcelTemplate(e) {
				//console.log(e);
				this.excelTemplate = { src: e.src, file: e.file };
				this.form.file = e.file;
				this.form.errors.clear('file');
			},

			submit() {
				this.confirm = true;
			},

			onSubmit() {
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.then(response => this.onError(response));
			},

			onSuccess(response) {
				this.confirm = false;
				this.$refs.next.click();
				this.number = response.number;
			},

			onError(response) {

			}
		}	
	}
</script>