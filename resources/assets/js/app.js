
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

Vue.use(VueNoty);
Vue.use(VueEvents);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// Base components
Vue.component('flash', require('./components/Flash.vue'));
Vue.component('loader', require('./components/Loader.vue'));
Vue.component('navigation', require('./components/Navigation.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('table-view', require('./components/TableView.vue'));
Vue.component('text-input', require('./components/TextInput.vue'));
Vue.component('checkbox-input', require('./components/CheckboxInput.vue'));
Vue.component('selector-input', require('./components/SelectorInput.vue'));
Vue.component('image-input', require('./components/ImageInput.vue'));
Vue.component('products-selector-input', require('./components/ProductsSelectorInput.vue'));

// Actions components
Vue.component('CategoriesActions', require('./actions/CategoriesActions.vue'));
Vue.component('LotsActions', require('./actions/LotsActions.vue'));
Vue.component('CouriersActions', require('./actions/CouriersActions.vue'))
Vue.component('ProductsActions', require('./actions/ProductsActions.vue'))

// Pages components
Vue.component('categories-page', require('./pages/Categories.vue'));
Vue.component('lots-page', require('./pages/Lots.vue'));
Vue.component('couriers-page', require('./pages/Couriers.vue'));
Vue.component('products-page', require('./pages/Products.vue'));
Vue.component('inbounds-page', require('./pages/Inbounds.vue'));

// Single instance view components
Vue.component('product', require('./objects/Product.vue'));

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};

const app = new Vue({
    el: '#app'
});
