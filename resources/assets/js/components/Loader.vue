<template>
	<div class="loader-container">
		<div class="custom-loader">
			<div class="bar1"></div>
			<div class="bar2"></div>
			<div class="bar3"></div>
			<div class="bar4"></div>
			<div class="bar5"></div>
			<div class="bar6"></div>
		</div>
		<div class="loading-text">Loading</div>
	</div>
</template>

<script>
	export default {
		props: [''],
		data() {
			return {

			};
		}	
	}
</script>

<style scoped>
.loader-container {
	width: 100%;
	position: absolute;
	height: 100%;
	display: flex;
	top: 0;
	left: 0;
	background: rgba(0,0,0,0.8);
	z-index: 5;
}
.custom-loader {
  	margin: 0 auto;
  	width: 60px;
  	height: 50px;
  	text-align: center;
  	font-size: 10px;
  	position: absolute;
  	top: 50%;
  	left: 50%;
  	-webkit-transform: translateY(-50%) translateX(-50%);
}

.custom-loader div {
    height: 100%;
    width: 8px;
    display: inline-block;
    float: left;
    margin-left: 2px;
    -webkit-animation: delay 0.8s infinite ease-in-out;
    animation: delay 0.8s infinite ease-in-out;
}
  
.custom-loader .bar1{
    background-color: #754fa0 ;
    -webkit-animation-delay: 0.2s;
    animation-delay: 0.2s;
}
.custom-loader .bar2{
    background-color: #09b7bf;
    -webkit-animation-delay: 0.3s;
    animation-delay: 0.3s;
}
.custom-loader .bar3{
    background-color: #90d36b;
    -webkit-animation-delay: 0.4s;
    animation-delay: 0.4s;
}
.custom-loader .bar4{
    background-color: #f2d40d;
    -webkit-animation-delay: 0.5s;
    animation-delay: 0.5s;
}
.custom-loader .bar5{
    background-color: #fcb12b;
    -webkit-animation-delay: 0.6s;
    animation-delay: 0.6s;
}
.custom-loader .bar6{
    background-color: #ed1b72;
    -webkit-animation-delay: 0.7s;
    animation-delay: 0.7s;
}

.loading-text {
	color: white;
	text-align: center;
	width: 100%;
	position: absolute;
	bottom: 40%;
}

@-webkit-keyframes delay {
  0%, 40%, 100% { -webkit-transform: scaleY(0.05) }  
  20% { -webkit-transform: scaleY(1.0) }
}

@keyframes delay {
  0%, 40%, 100% { 
    transform: scaleY(0.05);
    -webkit-transform: scaleY(0.05);
  }  20% { 
    transform: scaleY(1.0);
    -webkit-transform: scaleY(1.0);
  }
}
</style>