import { mount } from '@vue/test-utils';
import expect from 'expect';
import GameTimer from '../../resources/assets/js/components/GameTimer';
import sinon from 'sinon';

describe ('GameTimer', () => {
  let wrapper, clock;

  beforeEach (() => {
    clock = sinon.useFakeTimers();

    wrapper = mount(GameTimer, {
      propsData: {
        dataTotalPercentage: 30,
        start: false,
        reset: false
      }
    });
  });

  afterEach(() => clock.restore());

  it ('renders a countdown timer', () => {
    see('30s');
  });

  it ('reduces the countdown every second when started', async () => {
    see('30s');

    wrapper.setProps({ start: true });

    clock.tick(1000);

    await wrapper.vm.$nextTick();

    see('29s');
  });

  // it ('shows an expired message when the countdown has completed', async () => {
  //   clock.tick(10000);
  //
  //   await wrapper.vm.$nextTick();
  //
  //   see('Now Expired');
  // });
  //
  // it ('shows a custom expired message when the countdown has completed', async () => {
  //   wrapper.setProps({ expiredText: 'Contest is over.' });
  //
  //   clock.tick(10000);
  //
  //   await wrapper.vm.$nextTick();
  //
  //   see('Contest is over.');
  // });

  it ('broadcasts when the countdown is finished', async () => {
    wrapper.setProps({ start: true });

    clock.tick(30000);

    await wrapper.vm.$nextTick();

    expect(wrapper.emitted().finished).toBeTruthy();
  });

  it ('clears the interval once completed', async () => {
    wrapper.setProps({ start: true });

    clock.tick(30000);

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.percentage).toBe(30);

    await wrapper.vm.$nextTick();

    clock.tick(5000);

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.percentage).toBe(30);
  });

  it ('can reset the timer',async () => {
    wrapper.setProps({ start: true });

    clock.tick(10000);

    await wrapper.vm.$nextTick();

    see('20s');

    wrapper.setProps({ reset: true });

    see('30s');
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
