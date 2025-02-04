<template>
  <div v-if="games" class="game-list">
    <template v-if="dropdownProvider">
      <div v-if="isCategory" class="category">
        <!-- <div class="icon">
          <web-icon icon="slots"></web-icon>
        </div> -->
        <div class="search-input">
          <web-icon icon="fal fa-search"></web-icon>
          <input :placeholder="$t('general.search')" id="searchInput" v-model="search">
        </div>
        <div style="display: flex;">
          <div class="name" style="flex-grow: 1;">
            <dropdown class="providerDropdown" :chevron="true" style="font-size: 14px" :entries="findDropdownProviders().map(e => { return { id: e, name: e }; })"
                      :onSelect="(e) => { this.dropdownProvider = e.id; this.curIdx = 0; this.isLastPage = false; }" :select="dropdownProvider"></dropdown>
          </div>
          <div class="name">
            <div style="font-size: 14px; margin-right: 5px;">Sort by:</div> 
            <dropdown class="sortDropdown" :chevron="true" style="font-size: 14px" :entries="findDropdownSorts().map(e => { return { id: e, name: e }; })"
                      :onSelect="(e) => { this.dropdownSort = e.id; this.curIdx = 0; this.isLastPage = false; }" :select="dropdownSort"></dropdown>
          </div>
        </div>
        
        <div class="viewAll" @click="$router.push('/casino/game/provider/' + dropdownProvider)" v-if="!sort">
          {{ $t('general.viewAll') }}
        </div>
        <div class="arrows" v-if="!sort">
          <div class="arrow" @click="findPage(dropdownProvider).current > 0 ? updatePage(Object.assign(findPage(dropdownProvider), { current: findPage(dropdownProvider).current - 1 })) : null"
               :class="findPage(dropdownProvider).current <= 0 ? 'disabled' : ''"><web-icon icon="fal fa-chevron-left"></web-icon></div>
          <div class="arrow" @click="findPage(dropdownProvider).current < findPage(dropdownProvider).max ? updatePage(Object.assign(findPage(dropdownProvider), { current: findPage(dropdownProvider).current + 1 })) : null"
               :class="findPage(dropdownProvider).current >= findPage(dropdownProvider).max ? 'disabled' : ''"><web-icon icon="fal fa-chevron-right"></web-icon></div>
        </div>
      </div>
      <div class="CardGrid_cardGridElement__IfdAD" :key="dropdownProvider + '2'" :class="(dropdownProvider.replaceAll(' ', '_').replaceAll('(', '').replaceAll(')', '') + ' ') + (!sort ? '' : 'sorted')">
        <game-list-entry v-for="(game) in sortGames(dropdownProvider, dropdownSort, search).slice(0, curIdx + gamesPerView)" :key="game.id" :game="game"></game-list-entry>
      </div>
      <div class="more-games-container">
        <button @click="loadMoreGames" v-if="!isLastPage" class="more-games-button">more games</button>
      </div> 
    </template>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import GameListEntry from "./GameListEntry.vue";
  import ProviderSlider from "./ProviderSlider.vue";

  export default {
    components: {
      GameListEntry,      
      ProviderSlider,
    },
    data() {
      return {
        icons: {
          default: 'originals',
          'Slots (Originals)': 'slots',
          'Originals (Classic)': 'casino'
        },
        gamesPerView: 0,
        page: [],
        width: 0,
        search: '',
        visibleGames: [],
        dropdownProvider: null,
        dropdownSort: 0,
        curIdx: 0,
        isLastPage: false,
        popularGames: null
      }
    },
    props: {
      isCategory: {
        default: true,
        type: Boolean
      },
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
        this.setDefaultDropdownProvider();
      },
      // search() {
      // }
    },
    computed: {
      ...mapGetters(['games']),      
      providers() {
        let allProviders = ['All Providers']; // New provider 'All Providers'
        let gameTypes = this.games ? [...new Set(this.games.filter(e => !e.isHidden).map(e => e.type))] : [];
        let uniqueProviders = [...new Set([...allProviders, ...gameTypes])].sort(e => e !== 'Originals' ? 0 : -1);
        // console.log('aaaa------', uniqueProviders)
        return uniqueProviders;
      }
    },
    mounted() {
      this.updateGamesPerView();
      window.addEventListener('resize', this.updateGamesPerView);
      this.setDefaultDropdownProvider();
      this.visibleGames = this.sortGames(this.dropdownProvider, this.dropdownSort, this.search).slice(0, this.gamesPerView)
    },
    methods: {
      // openSearch() {
      //   alert('search:toggle');
      // },
      setDefaultDropdownProvider() {

        const providers = this.findDropdownProviders();
        let index = 0;
        // console.log('bbb------', providers[index])
        // if(providers.length >= 3) index = 3;

        this.dropdownProvider = providers[index];
      },
      findDropdownProviders() {
        return this.providers;
      },
      findDropdownSorts() {
        return ['Normal', 'Popular', 'Alphabetical', 'Random'];
      },
      sortGames(provider, sort, search) {
        let games = null;

        if (provider === 'All Providers') {            
            if (!this.sort) {
              games = this.games.slice(this.findPage(provider).current * this.gamesPerView, (this.findPage(provider).current + 1) * this.gamesPerView);
            } else if (this.sort.type === 'category') {
                games = this.games.filter(e => e.category.includes(this.sort.by));
            } else {
                games = this.games;
            }
        } else {
            if (!this.sort) {
                games = this.games.filter(e => e.type === provider).slice(this.findPage(provider).current * this.gamesPerView, (this.findPage(provider).current + 1) * this.gamesPerView);
            } else if (this.sort.type === 'category') {
                games = this.games.filter(e => e.type === provider && e.category.includes(this.sort.by));
            } else {
                games = this.games.filter(e => e.type === provider);
            }
        }

        games = games.filter(e => !e.isHidden).filter(e => e.name.toLowerCase().includes(this.search.toLowerCase()));

        if (sort === 'Popular') {
          games.sort((a,b) => {
            if (!isNaN(a.popularity) & !isNaN(b.popularity)) {
              return b.popularity - a.popularity;
            } else {
              return 0;
            }
          });
        } else if (sort === 'Alphabetical') {
          games.sort((a,b) => {
            return a.name.localeCompare(b.name);
          })
        } else if (sort === 'Random') {
          games = this.shuffleArray(games);
        }
        
        return games;
      },
      loadMoreGames() {
        // Increment the current index to show the next set of games
        this.curIdx += this.gamesPerView;
        // Update the visibleGames array with the next set of games
        // this.visibleGames = this.sortGames(this.dropdownProvider, this.dropdownSort).slice(0, this.curIdx + this.gamesPerView);
        this.isLastPage = this.curIdx + this.gamesPerView >= this.sortGames(this.dropdownProvider, this.dropdownSort, this.search).length;
      },
      updateGamesPerView() {
        this.width = window.innerWidth;

        let prev = this.gamesPerView;
        if(window.innerWidth <= 991) this.gamesPerView = 9;
        else this.gamesPerView = 24;

        if(prev !== this.gamesPerView) this.updateGames();
      },
      findPage(type) {
        return this.page.filter((e) => e.id === type)[0];
      },
      updatePage(object) {
        this.page = this.page.filter((e) => e.id !== object.id);
        this.page.push(object);
      },
      updateGames() {
        this.providers.forEach(providerName => {
          let filteredGames = this.games;
          if (providerName !== 'All Providers') {
              filteredGames = this.games.filter(e => e.type === providerName);
          }
          this.updatePage({
              id: providerName,
              current: 0,
              max: Math.floor((filteredGames.length - 1) / this.gamesPerView)
          });
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

<style lang="scss">
  @import "resources/sass/themes";

  .game-list {    

    .category {
      margin-top: 25px;
      display: flex;
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 25px;
      width: 100%;
      gap: 1rem;
      margin-top: 20px;

      @media(max-width: 991px) {
        font-size: 12px;
        margin-top: 15px;
        margin-bottom: 10px;
      }

      @include themed() {
        display: flex;
        align-items: center;

        @media (max-width: 991px) {
          background: transparent;
          flex-direction: column;
          align-items: unset;
          padding: unset;
          margin-top: 0;

          .search-input {
            margin-left: unset;
            margin-top: 10px;

            input {
              background: transparent;
              border: 1px solid t('input');
            }
          }
        }
      }

      .search-input {
        position: relative;
        // cursor: pointer;
        flex-grow: 1;
        // width: 100%;

        input {
          padding-left: 50px;
          // pointer-events: none;
          border-radius: .375rem;

          @include themed() {
            background: t('input-dark');

            &::placeholder {
              color: t('link');
            }
          }
        }

        svg, i {
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          left: 20px;
        }
      }

      .icon {
        margin-right: 15px;
        display: flex;
        align-items: center;
      }

      .name {
        display: flex;
        align-items: center;
        font-size: 22px;
        font-weight: 100;
        margin-top: -1px;

        @media(max-width: 500px) {
          font-size: 14px;
        }

        .providerDropdown {
          font-size: 14px !important;
          margin-right: 10px;

          .wesContainer {
            @include themed() {
              background: t('border') !important;
              border-radius: 5px;
              padding-top: 13px;
              padding-bottom: 13px;
              min-width: 150px;
            }
          }

          .exchangeList {
            @include themed() {
              background: t('body') !important;
            }
          }

          .name {
            font-size: 14px !important;
          }
        }
        
        .sortDropdown {
          font-size: 14px !important;
          // margin-right: 10px;

          .wesContainer {
            @include themed() {
              background: t('border') !important;
              border-radius: 5px;
              padding-top: 13px;
              padding-bottom: 13px;
            }
          }

          .exchangeList {
            @include themed() {
              background: t('body') !important;
            }
          }

          .name {
            font-size: 14px !important;
          }
        }
      }

      @include themed() {
        .viewAll {
          margin-left: auto;
          margin-right: 15px;
          background: t('block');
          padding: 10px 15px;
          cursor: pointer;
          color: t('link');
          border-radius: 6px;
        }

        .arrows {
          display: flex;

          .arrow {
            padding: 15px 20px;
            background: t('block');
            color: t('text');
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;

            @media(max-width: 991px) {
              padding: 5px 10px;
            }

            &.disabled {
              cursor: default;

              i, svg {
                opacity: .5;
                pointer-events: none;
              }
            }

            &:first-child {
              border-top-left-radius: 10px;
              border-bottom-left-radius: 10px;
            }

            &:last-child {
              border-top-right-radius: 10px;
              border-bottom-right-radius: 10px;
              margin-left: -1px;
            }

            i, svg {
              font-size: .9em;
              transition: all .3s ease;
            }

            &:not(.disabled):hover {
              i, svg {
                transform: scale(1.15);
              }
            }
          }
        }
      }
    }
    .category-subtitle {
      flex-direction: row !important;
      @media (max-width: 991px) {
        padding: 10px !important
      }
    }
  }
  // .search-input {
  //   position: relative;
  //   cursor: pointer;
  //   width: 100%;

  //   input {
  //     padding-left: 50px;
  //     pointer-events: none;
  //     border-radius: .375rem;

  //     @include themed() {
  //       background: t('input-dark');

  //       &::placeholder {
  //         color: t('link');
  //       }
  //     }
  //   }

  //   svg, i {
  //     position: absolute;
  //     top: 50%;
  //     transform: translateY(-50%);
  //     left: 20px;
  //   }
  // }
  .more-games-container {
    width: 100%; 
    display: flex; 
    justify-content: center; 
    margin-top: 20px;
  }
  .more-games-button {
    transition: 0.2s all;
    border: 0;
    position: relative;
    // flex: 1 1;
    font-weight: 500;
    line-height: 1.625rem;
    border-radius: .375rem;
    height: 2.25rem;
    padding: .469rem 1rem;
    cursor: pointer;
    color: #bec6d1;
    font-size: 0.875rem;
    background-color: #19202e;
    pointer-events: inherit;
    -webkit-user-select: inherit;
    user-select: inherit;
    opacity: 1;

    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
