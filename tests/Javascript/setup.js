let Vue = require('vue');

Vue.config.productionTip = false;

require('jsdom-global')('', {
  url: 'http://example.com',
  runScripts: "dangerously",
  resources: "usable",
  beforeParse (window) {
    window.HTMLMediaElement.prototype.load = () => {};
    window.HTMLMediaElement.prototype.play = () => {};
    window.HTMLMediaElement.prototype.pause = () => {};
    window.scrollTo = () => {};
  }
});