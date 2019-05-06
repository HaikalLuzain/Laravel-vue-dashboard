
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router' // Vue Router
import moment from 'moment'; // Vue moment
import { Form, HasError, AlertError } from 'vform'; // Vform
import VueProgressBar from 'vue-progressbar' // Vue Progressbar
import swal from 'sweetalert2' // Sweet Alert 2
import Gate from "./gate"; // Define User

// Gate User
Vue.prototype.$gate = new Gate(window.user);

// Vue Pagination
Vue.component('pagination', require('laravel-vue-pagination'));


// Vform
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

// Vue router
Vue.use(VueRouter)
let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue') },
    { path: '/developer', component: require('./components/Developer.vue') },
    { path: '/profile', component: require('./components/Profile.vue') },
    { path: '/users', component: require('./components/Users.vue') },
    { path: '*', component: require('./components/Page-404.vue') }
  ]
const router = new VueRouter({
  mode: 'history',
  routes // short for `routes: routes`
})

// Vue filter
Vue.filter('upText', function(text){
  return text.charAt(0).toUpperCase() + text.slice(1);
})

Vue.filter('myDate', function(created){
  return moment(created).format('MMMM Do YYYY');
})

window.swal = swal;

window.Fire = new Vue();

// Vue Progressbar
Vue.use(VueProgressBar, {
  color: '#5e72e4',
  failedColor: 'red',
  height: '3px'
})


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue')
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component(
  'page-404',
  require('./components/Page-404.vue')
);


Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app',
    router,
});
