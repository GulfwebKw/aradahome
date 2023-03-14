
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.use(require('vue-resource'));

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

import '@../../../public/admin_assets/assets/css/style.bundle.css';

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.component('categories-component', require('./components/Categories.vue'));
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('products-component', require('./components/PosProducts.vue'));
Vue.component('attribute-component', require('./components/Attributes.vue'));
Vue.component('shoppingcart-component', require('./components/PosCart.vue'));

Vue.component('product-pagination', require('laravel-vue-pagination'));

const app = new Vue({
    el: '#app'
});
