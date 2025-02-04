<template>
  <div  v-if="games.length<=0" style="display: flex; justify-content: center;">  
    <loader></loader>
  </div>
  <div v-else class="container" style="">
    

    <game-category-search :IsHome="true"></game-category-search>

    <!-- <original-game-list v-if="tab=='originals'||tab==='lobby'" :isIndex="tab=='originals'?true:true"></original-game-list> -->
    <template v-if="tab==='lobby'">
      <div v-if="originalGames.length > 0" class="game-list" id="slot-games">
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/original.svg" alt="Original">
          </div>
          <div class="name">
            Originals
          </div>
          <div class="viewAll" @click="$router.push('/casino/game/category/originals')" >
            {{ $t('general.viewAll') }}
          </div>
          <div class="arrows">
            <div class="arrow" 
                @click="findPage('original').current > 0 ? updatePage(Object.assign(findPage('original'), { current: findPage('original').current - 1 })) : null"
                :class="(findPage('original') && findPage('original').current <= 0) ? 'disabled' : ''">
              <web-icon icon="fal fa-chevron-left"></web-icon>
            </div>
            <div class="arrow" 
                @click="findPage('original').current < findPage('original').max ? updatePage(Object.assign(findPage('original'), { current: findPage('original').current + 1 })) : null"
                :class="(findPage('original') && findPage('original').current >= findPage('original').max) ? 'disabled' : ''">
              <web-icon icon="fal fa-chevron-right"></web-icon>
            </div>
          </div>
        </div>
        <div class="CardGrid_cardGridElement__IfdAD " :key="'original'" :class="(!sort ? '' : 'sorted')">
          <game-list-entry v-for="game in paginatedOriginalGames" :key="game.id" :game="game"></game-list-entry>
        </div>
      </div>
      <div v-if="slotGames.length > 0" class="game-list" id="slot-games">
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/slots.svg" alt="Slots">
          </div>
          <div class="name">
           Trending Slots
          </div>
          <div class="viewAll" @click="$router.push('/casino/game/category/slots')" >
            {{ $t('general.viewAll') }}
          </div>
          <div class="arrows">
            <div class="arrow" 
                @click="findPage('slot').current > 0 ? updatePage(Object.assign(findPage('slot'), { current: findPage('slot').current - 1 })) : null"
                :class="(findPage('slot') && findPage('slot').current <= 0) ? 'disabled' : ''">
              <web-icon icon="fal fa-chevron-left"></web-icon>
            </div>
            <div class="arrow" 
                @click="findPage('slot').current < findPage('slot').max ? updatePage(Object.assign(findPage('slot'), { current: findPage('slot').current + 1 })) : null"
                :class="(findPage('slot') && findPage('slot').current >= findPage('slot').max) ? 'disabled' : ''">
              <web-icon icon="fal fa-chevron-right"></web-icon>
            </div>
          </div>
        </div>
        <div class="CardGrid_cardGridElement__IfdAD " :key="'slot'" :class="(!sort ? '' : 'sorted')">
          <game-list-entry v-for="game in paginatedSlotGames" :key="game.id" :game="game"></game-list-entry>
        </div>
      </div>
      <div v-if="liveGames.length > 0" class="game-list" id="live-games">
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/casino.svg" alt="Live">
          </div>
          <div class="name">
           Trending Live Games
          </div>
          <div class="viewAll" @click="$router.push('/casino/game/category/live')" >
            {{ $t('general.viewAll') }}
          </div>
          <div class="arrows">
            <div class="arrow" @click="findPage('live').current > 0 ? updatePage(Object.assign(findPage('live'), { current: findPage('live').current - 1 })) : null"
                :class="(findPage('live') && findPage('live').current <= 0) ? 'disabled' : ''"><web-icon icon="fal fa-chevron-left"></web-icon></div>
            <div class="arrow" @click="findPage('live').current < findPage('live').max ? updatePage(Object.assign(findPage('live'), { current: findPage('live').current + 1 })) : null"
                :class="(findPage('live') && findPage('live').current >= findPage('live').max) ? 'disabled' : ''"><web-icon icon="fal fa-chevron-right"></web-icon></div>
          </div>
        </div>
        <div class="CardGrid_cardGridElement__IfdAD " :key="'live'" :class="(!sort ? '' : 'sorted')">
          <game-list-entry v-for="game in paginatedLiveGames" :key="game.id" :game="game"></game-list-entry>
        </div>
      </div>
      <provider-slider v-if="tab==='lobby'"></provider-slider>
    </template>
    <template v-else-if="tab==='originals'">
      <div class="game-list" >
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/original.svg" alt="Original">
          </div>
          <div class="name">
            Originals
          </div>
        </div>
        <category-game-list :sort="{ type: 'category', by: 'originals' }" :isCategory="false" :isIndex="false"></category-game-list>
      </div>
    </template>
    <template v-else-if="tab==='slots'">
      <div class="game-list" >
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/slots.svg" alt="Original">
          </div>
          <div class="name">
            Slots
          </div>
        </div>
        <category-game-list :sort="{ type: 'category', by: 'slots' }" :isCategory="false" :isIndex="false"></category-game-list>
      </div>
    </template>
    <template v-else-if="tab==='live'">
      <div class="game-list" >
        <div class="category category-subtitle">
          <div class="icon">
            <img src="/icons/casino.svg" alt="Original">
          </div>
          <div class="name">
            Live Games
          </div>
        </div>
        <category-game-list :sort="{ type: 'category', by: 'live' }" :isCategory="false" :isIndex="false"></category-game-list>
      </div>
    </template>

  </div>
</template>

<script>
  import {mapGetters} from 'vuex';
  import AuthModal from "../modals/AuthModal.vue";
  import PasswordResetModal from "../modals/PasswordResetModal.vue";
  import OriginalGameList from "../ui/OriginalGameList.vue";
  import SlotGameList from "../ui/SlotGameList.vue";
  import LiveGameList from "../ui/LiveGameList.vue";
  import Games from "../ui/GameList.vue";
  import IndexSlider from "../ui/IndexSlider.vue";
  import ProviderSlider from "../ui/ProviderSlider.vue";
  import GameCategorySearch from "../ui/GameCategorySearch.vue";
  import Bus from "../../bus";

  export default {
    data() {
      return {
        tab: 'lobby',
        gamesPerView: 0,
        page: [],
        width: 0
      }
    },
    watch: {
      games() {
        this.updateGames();
      }
    },
    computed: {
      ...mapGetters(['games', 'isGuest', 'providers']),
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
        const start = this.findPage('slot')?(this.findPage('slot').current * this.gamesPerView):0;
        const end = start + this.gamesPerView;
        const popularGames = this.slotGames.slice(start, end);
        // console.log('popularGames', popularGames);
        return popularGames;
      },
      originalGames() {
        // return this.shuffleArray(this.games.filter(game => game.category.includes('slots')).filter(e => !e.isHidden));
        let games = this.games.filter(game => game.category.includes('originals')).filter(e => !e.isHidden);
        games.sort((a,b) => {
          if (!isNaN(a.popularity) & !isNaN(b.popularity)) {
            return b.popularity - a.popularity;
          } else {
            return 0;
          }
        });
        return games;
      },
      paginatedOriginalGames() {
        const start = this.findPage('original')?(this.findPage('original').current * this.gamesPerView):0;
        const end = start + this.gamesPerView;
        return this.originalGames.slice(start, end);
      },
      liveGames() {
        // return this.shuffleArray(this.games.filter(game => game.category.includes('slots')).filter(e => !e.isHidden));
        let games = this.games.filter(game => game.category.includes('live')).filter(e => !e.isHidden);
        games.sort((a,b) => {
          if (!isNaN(a.popularity) & !isNaN(b.popularity)) {
            return b.popularity - a.popularity;
          } else {
            return 0;
          }
        });
        return games;
      },
      paginatedLiveGames() {
        const start = this.findPage('live')?(this.findPage('live').current * this.gamesPerView):0;
        const end = start + this.gamesPerView;
        return this.liveGames.slice(start, end);
      }
    },
    mounted() {
      this.updateGamesPerView();
      window.addEventListener('resize', this.updateGamesPerView);
      this.updatePage({
        id: 'slot',
        current: 0,
        max: 1
      });
      this.updatePage({
        id: 'live',
        current: 0,
        max: 1
      });
      this.updatePage({
        id: 'original',
        current: 0,
        max: 1
      });
    },
    methods: {
      updateGamesPerView() {
        this.width = window.innerWidth;
  
        let prev = this.gamesPerView;
        if (window.innerWidth <= 991) this.gamesPerView = 9;
        else this.gamesPerView = 6;
  
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
          max: 1
        });
        this.updatePage({
          id: 'live',
          current: 0,
          max: 1
        });
        this.updatePage({
          id: 'original',
          current: 0,
          max: 1
        });
      },
      shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      },
      openFaucetModal() {
        this.$router.push('/bonus');
      },
      openAuthModal(type) {
        AuthModal.methods.open(type);
      }
    },
    created() {
      if (this.$route.params.user && this.$route.params.token)
        PasswordResetModal.methods.open(this.$route.params.user, this.$route.params.token);
      else if(this.$route.params.token) {
        axios.post('/auth/confirmEmail', {
            token: this.$route.params.token,
        }).then(() => {
            this.$toast.success(this.$i18n.t('general.profile.email_confirmed'));
        }).catch(() => {
            this.$toast.error(this.$i18n.t('general.profile.invalid_token'))
        });
      }
      
      Bus.$on('tab:lobby', () => (this.tab = 'lobby'));
      Bus.$on('tab:originals', () => (this.tab = 'originals'));
      Bus.$on('tab:slots', () => (this.tab = 'slots'));
      Bus.$on('tab:live', () => (this.tab = 'live'));
    },
    components: {
    GameCategorySearch,
    // Games,
    OriginalGameList,
    SlotGameList,
    LiveGameList,
    ProviderSlider,
    IndexSlider,
}
  }
</script>

<style lang="scss" scoped>
  @import "resources/sass/variables";

  .slotExplorerIndex {
    .games {
      justify-content: unset;

      @media(max-width: 1610px) {
        justify-content: center;
      }
    }

    .game_poster {
      width: calc(20% - 14px) !important;
      height: 225px !important;
      margin: 7px !important;

      @media(max-width: 1500px) {
        width: calc(25% - 14px) !important;
      }

      @media(max-width: 991px) {
        width: 155px !important;
        height: 155px !important;
        margin: 3px !important;
      }
    }

    .text .name {
      display: none;
    }

    .index_cat {
      margin-left: 0;
      margin-bottom: 15px;
    }
  }

  .index_cat_banner {
    display: flex;

    .slots {
      background: url('/img/misc/index_slots.png') no-repeat left;
    }

    .sports {
      background: url('/img/misc/sports.png') no-repeat left;
    }

    .live_games_unavailable {
      background: rgba(255, 255, 255, .025);
      border-radius: 12px;
      width: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;

      i, svg {
        font-size: 2.5em;
        margin-bottom: 15px;
      }
    }

    .slots, .sports {
      width: 50%;
      background-size: cover;
      height: 200px;
      margin: 5px;
      cursor: pointer;
      border-radius: 12px;
      transition: all .3s ease;

      &:hover {
        transform: scale(1.01);
      }
    }

    @media(max-width: 991px) {
      .slots, .sports {
        margin: 0;
      }

      .slots {
        margin-bottom: 10px;
      }
    }
  }

  .indexCategories {
    height: 75px;
    margin-bottom: 40px;

    .os-host {
      flex: 1;
      width: 0;

      .os-content {
        display: flex;
      }
    }

    @include themed() {
      display: flex;
      padding-left: 40px;
      padding-right: 40px;
      background: t('sidebar');

      .category {
        cursor: pointer;
        transition: color .3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px 20px;
        white-space: nowrap;

        svg, i {
          margin-right: 10px;
        }

        &:hover {
          color: t('secondary');
        }

        &.active {
          color: t('secondary');
        }
      }
    }
  }

  @media(max-width: 450px) {
    .indexCategories {
      padding-left: 10px !important;
      padding-right: 10px !important;
    }
  }
</style>