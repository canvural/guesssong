require('./bootstrap');

window.Vue = require('vue');

Vue.component('countdown-timer', require('./components/GameTimer'));

import Flash from './components/Flash';
import Game from './components/GameComponent';

Vue.component('flash', Flash);

const app = new Vue({
    el: '#app',
    components: {
      Game,
    }
});
