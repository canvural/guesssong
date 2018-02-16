<template>
    <div class="relative leading-none"
         :class="[
          status ? 'is-' + status : ''
        ]">
        <div class="inline-block" :style="{height: width + 'px', width: width + 'px'}">
            <svg viewBox="0 0 100 100">
                <path :d="trackPath" :stroke="trackColor" :stroke-width="relativeStrokeWidth"
                      fill="none"></path>
                <path :d="trackPath" :stroke-linecap="strokeLinecap" :stroke="stroke"
                      :stroke-width="relativeStrokeWidth" fill="none" :style="circlePathStyle"></path>
            </svg>
        </div>
        <div class="absolute inline-block align-middle m-0 leading-none text-lg pin-l w-full text-center"
             style="top: 50%; transform: translate(0, -50%);"
             v-if="showText && !textInside"
             ref="progressText">
            <template v-if="!st || strokeColor || $slots.default">
                <slot>{{value}}%</slot>
            </template>
            <i v-else :class="iconClass"></i>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      value: {
        type: [Number, String],
        default: 0,
        required: true
      },
      totalPercentage: {
        type: [Number, String],
        default: 100,
        required: true
      },
      strokeWidth: {
        type: Number,
        default: 4
      },
      strokeLinecap: {
        type: String,
        default: 'round',
        validator: val => {
          return ['butt', 'square', 'round'].indexOf(val) > -1
        }
      },
      strokeColor: {
        type: String
      },
      trackColor: {
        type: String,
        default () {
          return 'black'
        }
      },
      textInside: {
        type: Boolean,
        default: false
      },
      showText: {
        type: Boolean,
        default: true
      },
      status: {
        type: String,
        validator: val => {
          return ['success', 'exception', 'warning', 'info'].indexOf(val) > -1
        }
      },
      width: {
        type: Number,
        default: 126
      },
      reverse: {
        type: Boolean,
        default: false
      },
      striped: {
        type: Boolean,
        default: false
      },
      linearClassName: String
    },

    data () {
      return {
        st: this.status
      }
    },

    watch: {
      value (newVal) {
        if (this.$slots.default) return;
        this.st = newVal === this.totalPercentage ? 'success' : this.status
      }
    },

    computed: {
      relativeStrokeWidth () {
        return (this.strokeWidth / this.width * this.totalPercentage).toFixed(1)
      },
      trackPath () {
        let radius = parseInt(50 - parseFloat(this.relativeStrokeWidth) / 2, 10)
        let reverse = this.reverse ? 0 : 1
        return `M 50 50 m 0 -${radius} a ${radius} ${radius} 0 1 ${reverse} 0 ${radius * 2} a ${radius} ${radius} 0 1 ${reverse} 0 -${radius * 2}`
      },
      perimeter () {
        let radius = 50 - parseFloat(this.relativeStrokeWidth) / 2
        return 2 * Math.PI * radius
      },
      circlePathStyle () {
        let perimeter = this.perimeter;
        return {
          strokeDasharray: `${perimeter}px,${perimeter}px`,
          strokeDashoffset: (1 - this.value / this.totalPercentage) * perimeter + 'px',
          transition: 'stroke-dashoffset 1s linear 0s, stroke 1s linear'
        }
      },
      stroke () {
        let ret
        switch (this.st) {
          case 'success':
            ret = '#13ce66'
            break
          case 'warning':
            ret = '#f7ba2a'
            break
          case 'info':
            ret = '#50bfff'
            break
          case 'exception':
            ret = '#ff4949'
            break
          default:
            ret = this.strokeColor ? this.strokeColor : '#20a0ff'
        }
        return ret
      },
      iconClass () {
        let prefix = `vm-progress-icon${this.type === 'line' ? '-circle' : ''}--`
        return prefix + (this.st === 'exception' ? 'error' : this.st)
      },
      progressTextSize () {
        return this.type === 'line'
          ? 12 + this.strokeWidth * .4
          : this.width * 0.111111 + 2
      }
    }
  }
</script>
