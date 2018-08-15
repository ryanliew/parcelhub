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
		      		<p>Complete!</p>
		    	</div>
		  	</div>
		  	<div class="steps-content">
		  		<div class="step-content has-text-centered is-active">
		  			<a class="button is-large is-primary" href="/download/inbound/template" target="_blank">
		  				<i class="fa fa-download"></i> <span class="ml-5">Download excel template</span>
		  			</a>
		  		</div>
		  		<div class="step-content has-text-centered is-flex-center">
		  			<img class="is-responsive" src="/images/inbound-excel.PNG"> 
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
				    <span class="help is-danger">{{ form.errors.get('overall') }}</span>
			    	<button class="button is-primary mt-5" @click="submit" :class="buttonClass">Submit excel file</button>
			    	<p><i>Make sure to follow the formats and fill all the columns</i></p>
		  		</div>
		  		<div class="step-content has-text-centered">
		  			<h1 class="title is-4">{{ message }}</h1>
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
        				message="Inbounds are going to be created. Proceed?"
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
				confirm: false,
				number: 0,
				action: '/excel/inbound/store',
				message: ''
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
				this.confirm = false;
				this.form.post(this.action)
						.then(response => this.onSuccess(response))
						.then(response => this.onError(response));
			},

			onSuccess(response) {
				this.excelTemplate = {name: 'No file selected'};
				this.confirm = false;
				this.$refs.next.click();
				this.message = response.message;
			},

			onError(response) {
				this.excelTemplate = {name: 'No file selected'};
				this.confirm = false;
			}
		},

		computed: {
			buttonClass() {
				return this.form.submitting ? 'is-loading' : '';
			}
		}	
	}
</script>