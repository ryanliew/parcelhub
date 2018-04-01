
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

require('./filters');

window.events = new Vue();

import VueNoty from 'vuejs-noty';
import VueEvents from 'vue-events';
import VueAnimateNumber from 'vue-animate-number';

Vue.use(VueNoty);
Vue.use(VueEvents);
Vue.use(VueAnimateNumber);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// Base components
Vue.component('flash', require('./components/Flash.vue'));
Vue.component('confirmation', require('./components/Confirmation.vue'));
Vue.component('loader', require('./components/Loader.vue'));
Vue.component('navigation', require('./components/Navigation.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('table-view', require('./components/TableView.vue'));
Vue.component('text-input', require('./components/TextInput.vue'));
Vue.component('checkbox-input', require('./components/CheckboxInput.vue'));
Vue.component('selector-input', require('./components/SelectorInput.vue'));
Vue.component('image-input', require('./components/ImageInput.vue'));
Vue.component('products-selector-input', require('./components/ProductsSelectorInput.vue'));

// Detail row components
Vue.component('LotDetailRow', require('./details/LotDetailRow.vue'));
Vue.component('ProductDetailRow', require('./details/ProductDetailRow.vue'));

// Actions components
Vue.component('CategoriesActions', require('./actions/CategoriesActions.vue'));
Vue.component('LotsActions', require('./actions/LotsActions.vue'));
Vue.component('CouriersActions', require('./actions/CouriersActions.vue'));
Vue.component('ProductsActions', require('./actions/ProductsActions.vue'));
Vue.component('InboundsActions', require('./actions/InboundsActions.vue'));
Vue.component('OutboundsActions', require('./actions/OutboundsActions.vue'));
Vue.component('PaymentsActions', require('./actions/PaymentsActions.vue'));
Vue.component('UsersActions', require('./actions/UsersActions.vue'));
Vue.component('CustomersActions', require('./actions/CustomersActions.vue'));

// Pages components
Vue.component('categories-page', require('./pages/Categories.vue'));
Vue.component('lots-page', require('./pages/Lots.vue'));
Vue.component('couriers-page', require('./pages/Couriers.vue'));
Vue.component('products-page', require('./pages/Products.vue'));
Vue.component('inbounds-page', require('./pages/Inbounds.vue'));
Vue.component('outbounds-page', require('./pages/Outbounds.vue'));
Vue.component('payments-page', require('./pages/Payments.vue'));
Vue.component('users-page', require('./pages/Users.vue'));
Vue.component('dashboard-page', require('./pages/Dashboard.vue'));
Vue.component('settings-page', require('./pages/Settings.vue'));
Vue.component('customers-page', require('./pages/Customers.vue'));

// Single instance view components
Vue.component('product', require('./objects/Product.vue'));
Vue.component('inbound', require('./objects/Inbound.vue'));
Vue.component('outbound', require('./objects/Outbound.vue'));

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};

const app = new Vue({
    el: '#app'
});