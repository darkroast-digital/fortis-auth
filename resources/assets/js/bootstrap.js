/*
| -------------------------------------------------------------------------
| #SETUP
| -------------------------------------------------------------------------
*/



// #JQUERY
// ========================================================================

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}



// #AXIOS
// ========================================================================

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';




// #SIMPLE MDE
// =========================================================================

window.SimpleMDE = require('simplemde');




// #SLUGIFY
// =========================================================================

window.slugify = require('slugify');




// #VUE
// ========================================================================

// window.Vue = require('vue');

// Vue.config.productionTip = false

// Vue.component('column', require('./components/Column.vue'));
// Vue.component('postname', require('./components/PostName.vue'));

// Vue.filter('slug', function(value) {
//   value = value.replace(/^\s+|\s+$/g, ''); // trim
//   value = value.toLowerCase();

//   // remove accents, swap ñ for n, etc
//   var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
//   var to   = "aaaaaeeeeeiiiiooooouuuunc------";
//   for (var i=0, l=from.length ; i<l ; i++) {
//     value = value.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
//   }

//   value = value.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
//     .replace(/\s+/g, '-') // collapse whitespace and replace by -
//     .replace(/-+/g, '-'); // collapse dashes

//   return value;
// });

// var slug = function(str) {
//   str = str.replace(/^\s+|\s+$/g, ''); // trim
//   str = str.toLowerCase();

//   // remove accents, swap ñ for n, etc
//   var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
//   var to   = "aaaaaeeeeeiiiiooooouuuunc------";
//   for (var i=0, l=from.length ; i<l ; i++) {
//     str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
//   }

//   str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
//     .replace(/\s+/g, '-') // collapse whitespace and replace by -
//     .replace(/-+/g, '-'); // collapse dashes

//   return str;
// };

// const app = new Vue({
//     el: '#app'
// });
