<template>
    <div class="flex items-center justify-center my-6 relative text-center text-white">
        <circular-countdown
            :size="150"
            :width="10"
            :rotate="-90"
            :value="percentage"
            :total-value="totalPercentage"
        >
            {{ `${percentage}s` }}
        </circular-countdown>
    </div>
</template>

<script>
import Progress from "./CircularCountdown";

export default {
  components: {
    "circular-countdown": Progress
  },
  props: ["dataTotalPercentage", "start", "reset"],
  data() {
    return {
      interval: null,
      percentage: this.dataTotalPercentage,
      totalPercentage: this.dataTotalPercentage
    };
  },
  watch: {
    start(val) {
      if (val) {
        this.refreshEverySecond();
      }
    },
    reset(val) {
      if (val) {
        this.percentage = this.dataTotalPercentage;

        clearInterval(this.interval);
      }
    },
    percentage(val) {
      if (val <= 0) {
        this.percentage = this.dataTotalPercentage;

        this.$emit("finished");
      }
    }
  },
  methods: {
    refreshEverySecond() {
      clearInterval(this.interval);

      this.interval = setInterval(() => {
        if (this.start) this.percentage--;
      }, 1000);

      this.$on("finished", () => clearInterval(this.interval));
    }
  }
};
</script>
