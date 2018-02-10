
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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
Vue.component('select-input', require('./components/SelectInput.vue'));

// Actions components
Vue.component('CategoriesActions', require('./actions/CategoriesActions.vue'));

// Pages components
Vue.component('categories-page', require('./pages/Categories.vue'));

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};

const app = new Vue({
    el: '#app'
});
