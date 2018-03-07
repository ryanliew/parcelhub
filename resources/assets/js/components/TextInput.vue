<template>
	<div>
		<span class="text-input input--hoshi" :class="inputClass">
			<input :name="name" 
					:id="name" 
					class="input__field input__field--hoshi" 
					:type="type || 'text'" 
					:value="value" 
					@input="updateValue($event.target.value)"
					v-if="editable"
					ref="input" />
			<span class="input__field input__field--hoshi"
					v-html="value"
					v-else>	
			</span>
			<label class="input__label input__label--hoshi"
					:class="className"  
					:for="name"
					>
				<span class="input__label-content input__label-content--hoshi" v-if="!hideLabel">
					<span v-text="label"></span>
					<span v-if="required && editable" class="is-danger">*</span>
				</span>
			</label>
		</span>
		<span class="help is-danger" v-if="error" v-text="error"></span>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['defaultValue', 'label', 'required', 'error', 'name', 'type', 'editable', 'focus', 'hideLabel'],
		data() {
			return {
				
			};
		},

		mounted() {
			if(this.focus)
			{
				this.$refs.input.focus();
			}
		},

		methods: {
			updateValue(value) {
				this.finalValue = value;
				this.$emit('input', value);
			}
		},

		computed: {
			className() {
				if(this.error)
				{
					return 'input__label--hoshi-color-3';
				}
				else if(!this.editable)
				{
					return 'input__label--hoshi-color-transparent';
				}

				return 'input__label--hoshi-color-1';
			},

			inputClass(){
				let theClass = [];
				if( this.value !== '' || this.type == 'date') {
					theClass.push('input--filled');
				}

				if(this.hideLabel) {
					theClass.push('input--nolabel');
				}

				return theClass;
			},

			value() {
				return !this.editable && this.type == 'date' ? moment(this.defaultValue).fromNow() : this.defaultValue;
			}
		},

		filters: {
			ago: function(value) {
				if(!this.editable && this.type == 'date')
					return moment(value).fromNow();
				return value;
			}
		}
	}
</script>

<style scoped>
.text-input {
	position: relative;
	z-index: 1;
	display: inline-block;
	width: 100%;
	vertical-align: top;
	font-size: 1em;
	min-width: 80px;
}

.input__field, .input__disabled {
	position: relative;
	display: block;
	float: right;
	padding: 0.8em;
	width: 100%;
	border: none;
	border-radius: 0;
	background: #f0f0f0;
	-webkit-appearance: none; /* for box shadows to show on iOS */
	font-size: 1em;
}

.input__field:focus {
	outline: none;
}

.input__label {
	display: inline-block;
	float: right;
	padding: 0 1em;
	width: 40%;
	font-weight: bold;
	font-size: 90.25%;
	-webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

.input__label-content {
	position: relative;
	display: block;
	padding: 0.4em 0;
	width: 100%;
}

.input--hoshi {
	overflow: hidden;
}

.input__field--hoshi, .input__disabled {
	padding: 1em 0.15em;
	width: 100%;
	background: transparent;
}

.input--nolabel .input__field--hoshi {
	padding: 0 1em 1em 0.15em;
}

.input__label--hoshi {
	position: absolute;
	bottom: 0;
	left: 0;
	padding: 0 0.25em;
	width: 100%;
	height: calc(100% - 1em);
	text-align: left;
	pointer-events: none;
}

.input__label-content--hoshi {
	position: absolute;
	left: 2px;
}

.input__label--hoshi::before,
.input__label--hoshi::after {
	content: '';
	position: absolute;
	top: 2px;
	left: 0;
	width: 100%;
	height: calc(100% - 10px);
	border-bottom: 1px solid #B9C1CA;
}

span.input__label--hoshi::after {
	border-bottom: none;
}

.input__label--hoshi::after {
	margin-top: 2px;
	border-bottom: 4px solid red;
	-webkit-transform: translate3d(-100%, 0, 0);
	transform: translate3d(-100%, 0, 0);
	-webkit-transition: -webkit-transform 0.3s;
	transition: transform 0.3s;
}

.input__label--hoshi-color-1::after {
	border-color: hsl(200, 100%, 50%);
}

.input__label--hoshi-color-2::after {
	border-color: hsl(160, 100%, 50%);
}

.input__label--hoshi-color-3::after {
	border-color: hsl(20, 100%, 50%);
}

.input__label--hoshi-color-transparent::before, .input__label--hoshi-color-transparent::after {
	border-color: transparent;
	border-bottom: 0px solid transparent;
}

.input__field--hoshi:focus + .input__label--hoshi::after,
.input--filled .input__label--hoshi::after {
	-webkit-transform: translate3d(0, 0, 0);
	transform: translate3d(0, 0, 0);
}

.input__field--hoshi:focus + .input__label--hoshi .input__label-content--hoshi,
.input--filled .input__label-content--hoshi {
	-webkit-animation: anim-1 0.3s forwards;
	animation: anim-1 0.3s forwards;
}

@-webkit-keyframes anim-1 {
	50% {
		opacity: 0;
		-webkit-transform: translate3d(1em, 0, 0);
		transform: translate3d(1em, 0, 0);
		font-size: 100%;
	}
	51% {
		opacity: 0;
		-webkit-transform: translate3d(-1em, -40%, 0);
		transform: translate3d(-1em, -40%, 0);
		font-size: 90%;
	}
	100% {
		opacity: 1;
		-webkit-transform: translate3d(0, -70%, 0);
		transform: translate3d(0, -70%, 0);
		font-size: 85%;
	}
}

@keyframes anim-1 {
	50% {
		opacity: 0;
		-webkit-transform: translate3d(1em, 0, 0);
		transform: translate3d(1em, 0, 0);
		font-size: 100%;
	}
	51% {
		opacity: 0;
		-webkit-transform: translate3d(-1em, -40%, 0);
		transform: translate3d(-1em, -40%, 0);
		font-size: 90%;
	}
	100% {
		opacity: 1;
		-webkit-transform: translate3d(0, -70%, 0);
		transform: translate3d(0, -70%, 0);
		font-size: 85%;
	}
}
</style>