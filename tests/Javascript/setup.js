require('jsdom-global')('', {
  url: 'http://example.com',
  beforeParse (window) {
    window.HTMLMediaElement.prototype.load = () => {};
    window.HTMLMediaElement.prototype.play = () => {};
    window.HTMLMediaElement.prototype.pause = () => {};
    window.scrollTo = () => {};
  }
});