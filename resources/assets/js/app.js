
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
import VueListPicker from "vue-list-picker";

Vue.use(VueNoty);
Vue.use(VueEvents);
Vue.use(VueAnimateNumber);
Vue.use(VueListPicker);

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
Vue.component('textarea-input', require('./components/TextareaInput.vue'));
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
Vue.component('BranchesActions', require('./actions/BranchesActions.vue'));
Vue.component('ProductsActions', require('./actions/ProductsActions.vue'));
Vue.component('InboundsActions', require('./actions/InboundsActions.vue'));
Vue.component('OutboundsActions', require('./actions/OutboundsActions.vue'));
Vue.component('MinInboundsActions', require('./actions/MinInboundsActions.vue'));
Vue.component('MinOutboundsActions', require('./actions/MinOutboundsActions.vue'));
Vue.component('PaymentsActions', require('./actions/PaymentsActions.vue'));
Vue.component('UsersActions', require('./actions/UsersActions.vue'));
Vue.component('CustomersActions', require('./actions/CustomersActions.vue'));
Vue.component('ReturnsActions', require('./actions/ReturnsActions.vue'));
Vue.component('RecallsActions', require('./actions/RecallsActions.vue'));
Vue.component('SubusersActions', require('./actions/SubusersActions.vue'));
Vue.component('ProductsActionsSubuser', require('./actions/ProductsActionsSubuser.vue'));

// Fields components
Vue.component('ProductStock', require('./actions/ProductStock.vue'));

// Pages components
Vue.component('categories-page', require('./pages/Categories.vue'));
Vue.component('lots-page', require('./pages/Lots.vue'));
Vue.component('couriers-page', require('./pages/Couriers.vue'));
Vue.component('products-page', require('./pages/Products.vue'));
Vue.component('inbounds-page', require('./pages/Inbounds.vue'));
Vue.component('outbounds-page', require('./pages/Outbounds.vue'));
Vue.component('returns-page', require('./pages/Returns.vue'));
Vue.component('recalls-page', require('./pages/Recalls.vue'));
Vue.component('payments-page', require('./pages/Payments.vue'));
Vue.component('users-page', require('./pages/Users.vue'));
Vue.component('branches-page', require('./pages/Branches.vue'));
Vue.component('dashboard-page', require('./pages/Dashboard.vue'));
Vue.component('settings-page', require('./pages/Settings.vue'));
Vue.component('customers-page', require('./pages/Customers.vue'));
Vue.component('outbounds-tracking-page', require('./pages/OutboundsTracking.vue'));
Vue.component('products-excel-wizard', require('./pages/ProductsExcelWizard.vue'));
Vue.component('outbounds-excel-wizard', require('./pages/OutboundsExcelWizard.vue'));
Vue.component('inbounds-excel-wizard', require('./pages/InboundsExcelWizard.vue'));
Vue.component('subusers-page', require('./pages/Subusers.vue'));
Vue.component('reports-page', require('./pages/Reports.vue'));
Vue.component('stock-report', require('./objects/StockReport.vue'));

// Single instance view components
Vue.component('product', require('./objects/Product.vue'));
Vue.component('inbound', require('./objects/Inbound.vue'));
Vue.component('outbound', require('./objects/Outbound.vue'));
Vue.component('return', require('./objects/Return.vue'));
Vue.component('recall', require('./objects/Recall.vue'));

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};

const app = new Vue({
    el: '#app'
});