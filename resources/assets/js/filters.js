import moment from "moment";

// Vue filters
Vue.filter('unslug', function(value) {
	return _.replace(value, '_', ' ');
});

Vue.filter('capitalize', function(value) {
	return _.capitalize(value);
});

Vue.filter('formatInboundStatus', function(value) {
	let color = 'is-success';
	let text = _.capitalize(_.replace(value, '_', ' '));
	switch(value) {
		case 'awaiting_arrival':
			color = 'is-warning';
			break;
		case 'delivering':
			color = 'is-info';
			break;
		case 'completed':
			color = 'is-success';
			break;
		case 'canceled':
			color = 'is-danger';
			break;
	}

	return '<span class="tag '+ color +'">'+ text +'</span>';
});

Vue.filter('formatOutboundStatus', function(value) {
	let color = 'is-success';
	let text = _.capitalize(_.replace(value, '_', ' '));
	switch(value) {
		case 'processing':
			color = 'is-info';
			break;
		case 'completed':
			color = 'is-success';
			break;
		case 'canceled':
			color = 'is-danger';
			break;
	}
	return '<span class="tag '+ color +'">'+ text +'</span>';
});

Vue.filter('formatOutboundStatus', function(value) {
	let color = 'is-success';
	let text = _.capitalize(_.replace(value, '_', ' '));
	switch(value) {
		case 'pending':
			color = 'is-warning';
			break;
		case 'processing':
			color = 'is-info';
			break;
		case 'completed':
			color = 'is-success';
			break;
		case 'canceled':
			color = 'is-danger';
			break;
	}
	return '<span class="tag '+ color +'">'+ text +'</span>';
});

Vue.filter('formatPaymentStatus', function(value){
	let color = 'is-warning';
	let text = 'Processing';
	switch(value){
		case 'true':
			color = 'is-success';
			text = 'Approved';
			break;
	}
	return '<span class="tag ' + color + '">' + text + '</span>';
});

Vue.filter('date', function(value){
	if(moment(value).isValid())
		return moment(value).format('L');
	return 'N/A';
});

Vue.filter('convertToMeterCube', function(value){
	return value / 1000000;
});

Vue.filter('convertToCentimeterCube', function(value){
	return value * 1000000;
});