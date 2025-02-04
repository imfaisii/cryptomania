<template>
    <div v-if="slotGames.length > 0" class="game-list" id="slot-games">
      <div class="category category-subtitle">
        <div class="icon">
          <img src="/icons/slots.svg" alt="Slots">
        </div>
        <div class="name">
          Slots
        </div>
        <div class="viewAll" style="opacity: 0"></div>
        <div class="arrows">
          <div class="arrow" 
               @click="findPage('slot').current > 0 ? updatePage(Object.assign(findPage('slot'), { current: findPage('slot').current - 1 })) : null"
               :class="findPage('slot').current <= 0 ? 'disabled' : ''">
            <web-icon icon="fal fa-chevron-left"></web-icon>
          </div>
          <div class="arrow" 
               @click="findPage('slot').current < findPage('slot').max ? updatePage(Object.assign(findPage('slot'), { current: findPage('slot').current + 1 })) : null"
               :class="findPage('slot').current >= findPage('slot').max ? 'disabled' : ''">
            <web-icon icon="fal fa-chevron-right"></web-icon>
          </div>
        </div>
      </div>
      <div class="CardGrid_cardGridElement__IfdAD " :key="'slot'" :class="(!sort ? '' : 'sorted')">
        <game-list-entry v-for="game in paginatedSlotGames" :key="game.id" :game="game"></game-list-entry>
      </div>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import GameListEntry from "./GameListEntry.vue";
  
  export default {
    components: { GameListEntry },
    data() {
      return {
        gamesPerView: 0,
        page: [],
        width: 0
      }
    },
    props: {
      sort: {
        default: null,
        type: Object
      },
      isIndex: {
        type: Boolean,
        default: false
      }
    },
    watch: {
      games() {
        this.updateGames();
      }
    },
    computed: {
      ...mapGetters(['games']),
      slotGames() {
        // return this.shuffleArray(this.games.filter(game => game.category.includes('slots')).filter(e => !e.isHidden));
        let games = this.games.filter(game => game.category.includes('slots')).filter(e => !e.isHidden);
        games.sort((a,b) => {
          if (!isNaN(a.popularity) & !isNaN(b.popularity)) {
            return b.popularity - a.popularity;
          } else {
            return 0;
          }
        });
        return games;
      },
      paginatedSlotGames() {
        const start = this.findPage('slot').current * this.gamesPerView;
        const end = start + this.gamesPerView;
        const popularGames = this.slotGames.slice(start, end);
        // console.log('popularGames', popularGames);
        return popularGames;
      }
    },
    mounted() {
      this.updateGamesPerView();
      window.addEventListener('resize', this.updateGamesPerView);
      this.updatePage({
        id: 'slot',
        current: 0,
        max: Math.floor((this.slotGames.length - 1) / this.gamesPerView)
      });
    },
    methods: {
      updateGamesPerView() {
        this.width = window.innerWidth;
  
        let prev = this.gamesPerView;
        if (window.innerWidth <= 991) this.gamesPerView = this.isIndex?9:15;
        else this.gamesPerView = this.isIndex?12:24;
  
        if (prev !== this.gamesPerView) this.updateGames();
      },
      findPage(type) {
        return this.page.filter((e) => e.id === type)[0];
      },
      updatePage(object) {
        this.page = this.page.filter((e) => e.id !== object.id);
        this.page.push(object);
      },
      updateGames() {
        this.updatePage({
          id: 'slot',
          current: 0,
          max: Math.floor((this.slotGames.length - 1) / this.gamesPerView)
        });
      },
      shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      }
    }
  }
  </script>
  