export default {
  name: 'circular-countdown',

  props: {
    rotate: {
      type: Number,
      default: 0
    },

    size: {
      type: [Number, String],
      default: 32
    },

    width: {
      type: Number,
      default: 4
    },

    totalValue: {
      type: Number,
      defaut: 100
    },

    value: {
      type: Number,
      default: 0
    }
  },

  computed: {
    calculatedSize () {
      return Number(this.size);
    },

    circumference () {
      return 2 * Math.PI * this.radius
    },

    classes () {
      return {
        'progress-circular': true,
        'text-red': true
      };
    },

    cxy () {
      return this.calculatedSize / 2;
    },

    normalizedValue () {
      if (this.value < 0) {
        return 0;
      }

      if (this.value > this.totalValue) {
        return this.totalValue;
      }

      return this.value;
    },

    radius () {
      return (this.calculatedSize - this.width) / 2;
    },

    strokeDashArray () {
      return Math.round(this.circumference * 1000) / 1000;
    },

    strokeDashOffset () {
      return ((this.totalValue - this.normalizedValue) / this.totalValue) * this.circumference + 'px';
    },

    styles () {
      return {
        height: `${this.calculatedSize}px`,
        width: `${this.calculatedSize}px`
      };
    },

    svgSize () {
      return this.calculatedSize;
    },

    svgStyles () {
      return {
        transform: `rotate(${this.rotate}deg)`
      };
    },

    viewBox () {
      return false;
    }
  },

  methods: {
    genCircle (h, name, offset) {
      return h('circle', {
        class: `progress-circular__${name}`,
        attrs: {
          fill: 'transparent',
          cx: this.cxy,
          cy: this.cxy,
          r: this.radius,
          'stroke-width': this.width,
          'stroke-dasharray': this.strokeDashArray,
          'stroke-dashoffset': offset
        }
      });
    },
    genSvg (h) {
      const children = [
        this.genCircle(h, 'underlay', 0),
        this.genCircle(h, 'overlay', this.strokeDashOffset)
      ];

      return h('svg', {
        style: this.svgStyles,
        attrs: {
          xmlns: 'http://www.w3.org/2000/svg',
          height: this.svgSize,
          width: this.svgSize,
          viewBox: this.viewBox
        }
      }, children);
    }
  },

  render (h) {
    const info = h('div', { class: 'progress-circular__info' }, [this.$slots.default]);
    const svg = this.genSvg(h);

    return h('div', {
      class: this.classes,
      style: this.styles,
      on: this.$listeners
    }, [svg, info]);
  }
}
