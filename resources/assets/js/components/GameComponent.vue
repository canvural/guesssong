<template>
    <div
        class="p-8 rounded overflow-hidden shadow-lg relative bg-cover"
        :style="{ width: '400px', height: '400px', backgroundImage: 'url(' + playlist_image + ')'}"
    >
        <div class="absolute w-full h-full opacity-75 bg-grey-darkest pin-t pin-l"></div>
        <div class="flex flex-col justify-between py-3 items-center absolute w-full h-full pin-t pin-l select-none">
            <audio preload id="song">
                <source :src="currentSongUrl" type="audio/mp3">
            </audio>

            <div class="text-center text-2xl text-white">
                Total Score: {{ score }}
            </div>

            <div v-if="message" class="text-center text-xl text-white">
                <p>{{ message }}</p>
            </div>

            <countdown-timer
                :data-total-percentage="30"
                :start="gameInProgress"
                :reset="resetTimer"
                @finished="timeout"
            >
            </countdown-timer>

            <button
                id="game-start"
                class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded"
                @click="startGame"
                v-if="!gameInProgress"
            >
                Start!
            </button>

            <div v-else id="answers" class="text-xs">
                <button
                    class="mt-1 bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded"
                    @click="checkAnswer(track)"
                    v-for="track in currentTracks"
                >
                    {{ track.name }} - {{ track.artists.map((artist) => artist.name).join(', ') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import swal from "sweetalert2";

export default {
  props: ["playlist_image", "playlist_id"],
  data() {
    return {
      score: 0,
      audio: null,
      message: null,
      resetTimer: false,
      gameInProgress: false,
      currentTracks: null,
      currentSongUrl: null
    };
  },
  methods: {
    async startGame() {
      try {
        var response = await axios.post(window.location.href, {
          playlist: this.playlist_id
        });
      } catch (e) {
        swal({
          type: "error",
          title: "Oops! Something went wrong.",
          text: "Can't start the game right now!"
        });

        return;
      }

      this.currentTracks = response.data.tracks;
      this.currentSongUrl = response.data.current_song_url;

      this.gameInProgress = true;

      this.audio = this.$el.querySelectorAll("audio")[0];

      this.audio.load();
      this.audio.play();
    },
    async checkAnswer(track) {
      this.audio.pause();
      this.gameInProgress = false;
      this.resetTimer = true;

      const response = await axios.post(`${window.location.href}/answer`, {
        answer: track.id,
        playlist: this.playlist_id
      });

      if (response.data.message === "finished") {
        await swal({
          type: "success",
          title: "Good Job!",
          text: `You scored ${this.score} points! Now play another one!`,
          allowOutsideClick: false
        });

        return;
      }

      this.score = response.data.score || this.score;
      this.message = response.data.message;
      this.currentTracks = response.data.tracks;
      this.currentSongUrl = response.data.current_song_url;

      this.gameInProgress = true;
      this.resetTimer = false;

      this.audio.load();
      this.audio.play();
    },

    timeout() {
      this.checkAnswer("");
    }
  }
};
</script>
