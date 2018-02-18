<template>
    <div class="flex items-center justify-center my-6 relative text-center text-white">
        <circular-countdown
            :value="percentage"
            :total-percentage="totalPercentage"
            type="circle"
            stroke-color="#e5e9f2"
            stroke-linecap="round"
        >
            <slot>{{ remaining }}s</slot>
        </circular-countdown>
    </div>
</template>

<script>
import Progress from "./CircularProgress";

export default {
  components: {
    "circular-countdown": Progress
  },
  props: ["totalPercentage", "start", "reset"],
  data() {
    return {
      interval: null,
      percentage: 0
    };
  },
  computed: {
    remaining() {
      return Math.abs(this.totalPercentage - this.percentage);
    },
    finished() {
      return this.percentage >= this.totalPercentage;
    }
  },
  watch: {
    start(val) {
      if (val) {
        this.startCountdown();
      }
    },
    reset(val) {
      if (val) {
        this.percentage = 0;

        clearInterval(this.interval);
      }
    },
    percentage(val) {
      if (val >= this.totalPercentage) this.$emit("finished");
    }
  },
  methods: {
    startCountdown() {
      this.refreshEverySecond();
    },
    refreshEverySecond() {
      this.percentage++;

      this.interval = setInterval(() => {
        if (this.start) this.percentage++;
      }, 1000);

      this.$on("finished", () => clearInterval(this.interval));
    }
  }
};
</script>
