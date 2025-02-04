<template>
  <div class="container-fluid">
    <div :class="`game-container`">
      <div :class="`game-content game-type-third-party`">
        <div>
          <div class="thirdPartyContainer">
            <transition name="fade" mode="out-in">
              <div :key="1" v-if="loading" class="loading">
                <loader></loader>
              </div>
            </transition>

            <!-- <sidebar-play ref="tpPlay" class="d-none"></sidebar-play> -->

            <!-- <iframe id="frame" :src="url" v-if="url" frameborder="0"
                allow="accelerometer; autoplay; encrypted-media; gyroscope" allowfullscreen=""></iframe> -->

            <!-- <div v-html="htmlContent"></div> -->
            <div id="iFrame-container"></div>

            <!-- <script type="text/javascript" src="https://stage.sportbook.work/assets/sdk/init.js"></script> -->
            <!-- <script>
              document.addEventListener("DOMContentLoaded", () => {  const bettingFrame = BetSdk.init({
                mainFrameUrl: "https://cryptomania.gg/sports"
                host: "https://stage.sportbook.work",
                cid: "cryptomania-stage",
                lang: "en",
                token: this.token,
                containerId: "iFrame-container",
                parent: "https://cryptomania.gg/sports",  
                allowParentUrlUpdate: false
                })
              })
            </script>  -->

            <!-- <div class="thirdPartySidebar" v-if="currencies[currency].price < 0">
              <div class="conversion">{{ $t('third-party.unavailable_currency') }}</div>
            </div> -->
          </div>
        </div>
      </div>
    </div>      
    <div class="game-footer"></div>
  </div>

</template>

<script>
  import {mapGetters} from 'vuex';
  import AuthModal from "../modals/AuthModal.vue";
  export default {
    data() {
      return {
        game: null,
        loading: true,
        url: null,
        token: "",
        htmlContent: "", 
        expand: false
      }
    },
    computed: {
      ...mapGetters(['gameInstance', 'games', 'currencies', 'currency', 'demo', 'isGuest', 'gameInstance', 'user'])
    },
    created() {
      // _.forEach(this.games, (e) => {
      //   if (e.id === this.$route.params.id) this.game = e;
      // });

      // if (!this.game) this.$router.push('/');
    },
    async mounted() {
      try {
        await this.loadSDKScript();
        this.initBetSDK();
      } catch (error) {
        console.error("Error loading SDK script:", error);
      }
      // axios.post('/api/getSportsbook', this.user).then(({data}) => {
      //   console.log('gamelog----', data);
      //   this.url = data.link;
      //   this.token = data.token;
      //   const newScript = document.createElement('script');
      //   newScript.src = "https://stage.sportbook.work/assets/sdk/init.js";
      //   document.body.appendChild(newScript);
      //   const newScript1 = document.createElement('script');
      //   newScript1.textContent = 'document.addEventListener("DOMContentLoaded", () => {  const bettingFrame = BetSdk.init({' +
      //           'mainFrameUrl: "https://cryptomania.gg/sports",' +
      //           'host: "https://stage.sportbook.work",' +
      //           'cid: "cryptomania-stage",' +
      //           'lang: "en",' +
      //           'token: "' + this.token + '",' +
      //           'containerId: "iFrame-container",' +
      //           'parent: "https://cryptomania.gg/sports",' +
      //           'allowParentUrlUpdate: false' +
      //           '})' +
      //         '})';
      //   document.body.appendChild(newScript1);
      //   this.loading = false;
      // });
      // if (this.currencies[this.currency].price > 0) {
      //   setTimeout(() => this.$refs.tpPlay.onClick(), 100);
      //   this.loading = true;
      // }
      
      // this.updateGameInstance((i) => {
      //   i.bettingType = 'manual';
      //   i.playTimeout = false;
      // });
    },
    watch: {
      currency() {
        axios.post('/api/update/currency', {currency: this.currency}).then(({data}) => {
          // let tmpUrl = this.url;
          // this.url = null;
          // this.url = tmpUrl;
          //this.$router.push('/casino/game/' + this.gameInstance.game.id)
          this.$router.go();
        });
      }
    },
    components: {
      
    },
    methods: {
      openAuthModal() {
        AuthModal.methods.open('auth');
      },            
      getClientData() {
        return {};
      },
      getSidebarComponents() {
        return [];
      },
      loadSDKScript() {
        return new Promise((resolve, reject) => {
          const script = document.createElement('script');
          script.src = "https://stage.sportbook.work/assets/sdk/init.js";
          script.type = "text/javascript";
          script.onload = resolve;
          script.onerror = reject;
          document.head.appendChild(script);
        });
      },
      initBetSDK() {
        if (typeof BetSdk !== 'undefined') {
          axios.post('/api/getSportsbook', this.user).then(({data}) => {
            console.log('gamelog----', data);
            this.url = data.link;
            this.token = data.token;
            this.loading = false;
            BetSdk.init({
              mainFrameUrl: "https://cryptomania.gg/sports",
              host: 'https://stage.sportbook.work/',
              cid: 'cryptomania-stage',
              lang: 'en',
              token: this.token,
              containerId: 'iFrame-container',
              width: "100%",
              height: "100%",
              customStyles: false,
              parent: "https://cryptomania.gg/sports",
              allowParentUrlUpdate: false
            });
          });
        } else {
          console.error("BetSdk not loaded");
        }
      }
    }
  }
</script>

<style lang="scss">
  @import "resources/sass/variables";

  .game-padding {
    padding: 0 50px;
  }

  .tpBackdrop {
    height: 250px;
    position: relative;
    pointer-events: none;

    .game-name-big {
      position: absolute;
      font-size: 3em;
      top: 50%;
      transform: translateY(-50%);
      left: 4vmax;
    }

    .backdropImage {
      position: absolute;
      height: 400%;
      z-index: -1;
      margin-top: -110px;
      margin-left: -65px;
      width: calc(100% + 130px);
      filter: blur(33px);
      background-size: 100% 100%;

      &:after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
        width: 100%;
        height: 100%;

        @include themed() {
          background: radial-gradient(circle at 50% -90%, transparent, t('body'));
        }
      }
    }
  }

  .game-type-local {
    padding: 15px;
  }

  .game-type-third-party {
    display: flex;
    flex-direction: column;
    position: relative;
    min-height: 350px;
  }

  .game-header {
    @include themed() {
      display: flex;
      background: darken(t('sidebar'), 3.5%);
      padding: 20px 35px;
      font-size: 1.1em;
      user-select: none;
      border-top-left-radius: 6px;
      border-top-right-radius: 6px;
      align-items: center;

      .game-category {
        cursor: pointer;
      }

      .game-name {
        cursor: default;
        font-weight: 600;
      }

      .game-slash {
        cursor: default;
        margin: 0 10px;
      }
    }
  }

  @media(max-width: 991px) {
    .game-padding {
      padding: unset !important;
    }

    .tpBackdrop {
      height: 90px;

      .game-name-big {
        font-size: 2.2em;
        left: 10px;
      }

      .backdropImage {
        margin-left: -15px !important;
        width: calc(100% + 30px) !important;
      }
    }
  }

  .thirdPartyContainer {
    width: 100%;
    margin: auto;
    height: calc(100vh - #{$header-height} - 15px * 2) !important;
    overflow: hidden;
    position: relative;

    .loading {
      @include themed() {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 100;

        .warning {
          font-weight: 600;
        }

        .btn {
          margin-top: 15px;
          width: 130px;
        }
      }
    }

    iframe {
      border: none;
      width: 100%;
      min-height: 100%;
      position: absolute;
      z-index: 200;
      top: 0;
      left: 0;
    }
  }
</style>
