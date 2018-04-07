import { mount } from '@vue/test-utils';
import moxios from 'moxios'
import expect from 'expect';
import swal from 'sweetalert2';
import sinon from 'sinon';

import Game from '../../resources/assets/js/components/GameComponent';

describe ('Game', () => {
  let wrapper, swalStub, assignStub;

  beforeEach (() => {
    wrapper = mount(Game, {

    });

    swalStub = sinon.stub(swal, 'default').resolves(true);
    // assignStub = sinon.stub(window.location, 'assign');

    moxios.install();
  });

  afterEach(function () {
    moxios.uninstall();

    swalStub.restore(swal);
    // assignStub.restore(window.location.assign);
  });

  it ('shows 0 as default total score', () => {
    see('Total Score: 0')
  });

  it ('shows the start button ', () => {
    expect(wrapper.find('#game-start').exists()).toBe(true);

    see('Start', '#game-start');
  });

  it ('starts the game when start button is clicked', (done) => {
    click('#game-start');

    moxios.wait(() => {
      let request = moxios.requests.mostRecent();

      request.respondWith({
        status: 200,
        response: {
          tracks: [],
          'current_song_url': 'http://example.com'
        }
      }).then(() => {
        expect(wrapper.vm.gameInProgress).toBe(true);
        expect(wrapper.vm.currentTracks).toEqual([]);
        expect(wrapper.vm.currentSongUrl).toBe('http://example.com');

        done()
      })
    })
  });

  it ('will not start the game if server responds with error', (done) => {
    click('#game-start');

    moxios.wait(() => {
      let request = moxios.requests.mostRecent();

      request.respondWith({
        status: 500,
        response: {
        }
      }).then(() => {
        expect(wrapper.vm.gameInProgress).toBe(false);
        expect(wrapper.vm.currentTracks).toBeFalsy();
        expect(wrapper.vm.currentSongUrl).toBeFalsy();

        done()
      })
    })
  });

  it ('hides the start button when game is started', () => {
    wrapper.setData({
      gameInProgress: true,
      currentTracks: [
        { name: 'TestTrack', artists: [{ name: 'TestArtist' }] },
        { name: 'TestTrack2', artists: [{ name: 'TestArtist2' }] },
      ]
    });

    expect(wrapper.find('#game-start').exists()).toBe(false);
  });

  it ('shows track name and artists name on answer button', () => {
    wrapper.setData({
      gameInProgress: true,
      currentTracks: [
        { name: 'TestTrack', artists: 'TestArtist' },
        { name: 'TestTrack2', artists: 'TestArtist2, TestArtist3' },
      ]
    });

    see('TestTrack - TestArtist', '#answers div:first-of-type');
    see('TestTrack2 - TestArtist2, TestArtist3', '#answers div:last-of-type');
  });

  it ('updates score on correct answer ', (done) => {
    wrapper.setData({
      audio: document.createElement('audio'),
      gameInProgress: true,
      currentTracks: [
        { name: 'TestTrack', artists: [{ name: 'TestArtist' }] },
        { name: 'TestTrack2', artists: [{ name: 'TestArtist2' }] },
      ]
    });

    click('#answers button:first-of-type');

    moxios.wait(() => {
      let request = moxios.requests.mostRecent();

      request.respondWith({
        status: 200,
        response: {
          score: 10,
          message: 'Correct'
        }
      }).then(() => {
        see('Total Score: 10');
        see('Correct');

        done()
      })
    })
  });

  it ('automatically submits an empty answer when timer is finished', done => {
    wrapper.setData({
      audio: document.createElement('audio'),
      gameInProgress: true,
      currentTracks: [
        {name: 'Track', artists: [{name: 'Artist'}]},
        {name: 'Track2', artists: [{name: 'Artist2'}]},
      ]
    });

    see('Total Score: 0');

    wrapper.update();

    // Manually trigger a timeout
    // TODO: Using fake clock caused some issues. Investigate more.
    wrapper.vm.timeout();

    moxios.wait(() => {
      let request = moxios.requests.mostRecent();

      request.respondWith({
        status: 200,
        response: { }
      }).then(() => {
        see('Total Score: 0');

        done()
      })
    })
  });

  it ('redirects when game is finished', function (done) {
    wrapper.setData({
      audio: document.createElement('audio'),
      gameInProgress: true,
      currentTracks: [
        {name: 'Track', artists: [{name: 'Artist'}]},
        {name: 'Track2', artists: [{name: 'Artist2'}]},
      ]
    });

    click('#answers button:first-of-type');

    moxios.wait(() => {
      let request = moxios.requests.mostRecent();

      request.respondWith({
        status: 200,
        response: {
          message: 'finished'
        }
      }).then(() => {
        swalStub.clickConfirm();

        // expect(assignStub.called).toBe(true);

        expect(swalStub.isVisible()).toBe(false);

        done()
      })
    })
  });

  // Helper Functions

  let see = (text, selector) => {
    let wrap = selector ? wrapper.find(selector) : wrapper;

    expect(wrap.html()).toContain(text);
  };

  let type = (text, selector) => {
    let node = wrapper.find(selector);

    node.element.value = text;
    node.trigger('input');
  };

  let click = selector => {
    wrapper.find(selector).trigger('click');
  };
});
