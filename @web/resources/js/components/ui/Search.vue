<template>
    <div class="search" :class="show ? 'show' : ''">
        <div class="container">
            <div class="input">
                <input id="searchInput" v-model="search" placeholder="Search...">
                <web-icon icon="fal fa-search" class="search"></web-icon>
                <div @click="closeSearch()" class="close"><web-icon icon="fal fa-times" class="close"></web-icon></div>
            </div>
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                <div class="results">
                    <div class="result" @click="show = false; $router.push(result._searchUrl)" v-for="result in results" :key="result.id">
                        <img :src="result.image" alt onerror="this.src = '/img/misc/unknown-game-image.jpg'">

                    </div>
                </div>
            </overlay-scrollbars>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Bus from "../../bus";

export default {
  computed: {
    ...mapGetters(['games'])
  },
  data() {
    return {
      show: false,
      search: '',
      results: []
    }
  },
  watch: {
    show() {
      if (this.show) {
        setTimeout(() => {
          document.querySelector('#searchInput').focus();
        });
      }
    },
    search() {
      if (this.search === '' || this.search.length < 3) {
        this.results = [];
        return;
      }
      let results = [];

      const isMobile = window.innerWidth <= 991;

      this.games.forEach((game) => {
        if (game.isMobile !== null)
          if ((game.isMobile && !isMobile) || (!game.isMobile && isMobile)) return;

        // console.log("Icon URL:", game.icon); // Add this line to log icon URLs

        if (
          game.name.toLowerCase().includes(this.search.toLowerCase()) ||
          game.id.toLowerCase().includes(this.search.toLowerCase())
          // game.category[0].toLowerCase().includes(this.search.toLowerCase())
        ) {
          game._searchUrl = '/casino/game/' + game.id;
          results.push(game);
        }
      });
      this.results = results;
    }
  },
  created() {
    Bus.$on('search:toggle', () => (this.show = !this.show));
  },
  methods: {
    closeSearch() {
      this.search = '';
      this.results = [];
      this.show = false;
    }
  }
};
</script>

<style lang="scss">
@import "resources/sass/themes";

.search {
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(black, .8);
    backdrop-filter: blur(15px);
    z-index: 99999;
    pointer-events: none;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity .3s ease;

    &.show {
        opacity: 1;
        pointer-events: unset;
    }

    .results {
        // height: calc(100vh - 250px);
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 20px; // Add padding for better spacing

        .os-content {
            display: flex;
            flex-wrap: wrap;
        }
    }

    .result {
        cursor: pointer;
        transition: transform .3s ease;
        margin-right: 15px;
        margin-bottom: 15px;
        width: 140px; // 294px;
        // height: 400px;
        border-radius: 10px;
        overflow: hidden; // Ensure image overflow is hidden

        @include themed() {
            background-color: t('sidebar');
        }

        &:hover {
            transform: scale(1.05); // Increase scale on hover for subtle effect
        }

        img {
            width: 100%; // Make image responsive within container
            height: auto; // Maintain aspect ratio
            border-radius: 10px;
            object-fit: cover; // Ensure image covers container without distortion
        }
    }

    .input {
        margin-top: 50px;
        margin-bottom: 30px; // Reduce bottom margin for better spacing
        position: relative;

        .icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: .7; // Adjust opacity for better visibility
            color: white; // Ensure icon color is consistent
        }

        .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: .7; // Adjust opacity for better visibility
            cursor: pointer;
            transition: opacity .3s ease;
            color: white;

            &:hover {
                opacity: 1;
            }
        }

        input {
            background: rgba(163, 190, 245, .1);
            border: none;
            border-radius: 30px; // Update border-radius for a softer look
            box-shadow: 0 0 0 3px transparent;
            color: #fff;
            font-size: 18px; // Reduce font size for better readability
            padding: 15px 50px; // Adjust padding for better input field size
            width: calc(100% - 80px); // Adjust width to accommodate icons
            height: 50px; // Reduce height for a more compact look
            outline: none;

            &::placeholder {
                color: rgba(255, 255, 255, 0.7); // Adjust placeholder color for better visibility
            }
        }
    }
}
</style>

