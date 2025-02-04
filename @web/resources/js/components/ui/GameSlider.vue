vue
<template>
  <div class="game-slider">
    <div class="slider-wrapper">
      <game-list-entry v-for="game in games" :key="game.id" :game="game"></game-list-entry>
    </div>
    <button @click="prev" :disabled="isFirstPage">Prev</button>
    <button @click="next" :disabled="isLastPage">Next</button>
  </div>
</template>

<script>
export default {
  props: {
    games: {
      type: Array,
      required: true
    },
    updatePage: {
      type: Function,
      required: true
    },
    category: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      currentPage: 0,
      gamesPerView: 6 // Adjust as needed
    }
  },
  computed: {
    isFirstPage() {
      return this.currentPage === 0;
    },
    isLastPage() {
      return this.currentPage >= Math.ceil(this.games.length / this.gamesPerView) - 1;
    }
  },
  methods: {
    prev() {
      if (!this.isFirstPage) {
        this.currentPage--;
        this.updatePage({ id: this.category, current: this.currentPage });
      }
    },
    next() {
      if (!this.isLastPage) {
        this.currentPage++;
        this.updatePage({ id: this.category, current: this.currentPage });
      }
    }
  }
}
</script>

<style scoped>
.game-slider {
  display: flex;
  align-items: center;
}

.slider-wrapper {
  display: flex;
  overflow-x: auto; /* Allows horizontal scrolling */
}

button {
  margin: 0 10px;
}
</style>