<template>
  <div :class="'sidebar ' + (sidebar || mobileToggle ? 'visible' : 'hidden') + ' ' + (mobileToggle ? 'mobileToggle' : '')"
       @click="mobileToggle = false" v-click-outside="closeSidebar">
    <div class="fixed" style="border-right: 1px solid #2a2e38 !important;">
      <div class="sidebar-header" style="border-bottom: 1px solid #2a2e38 !important; align-items: center; padding-left: 1.25rem;">
        <!-- <div class="logo" @click="$router.push('/')"></div> -->
        
        <div class="toggle" @click="$store.dispatch('toggleSidebar')" style="margin: 0 !important; padding: 0 6px;">
          <!-- <web-icon :icon="sidebar ? 'fal fa-chevron-left' : 'fal fa-chevron-right'"></web-icon> -->
          <img src="/img/menu.svg" alt="menu">
        </div>
        <div class="NavigationTab_root__3914H" style="">
          <div class="NavigationTab_container__1rlca ">
            <button :class="isCasino ? 'NavigationTab_active' : 'NavigationTab_disable'" @click="$router.push('/')">
              <span>Casino</span>
            </button>
            <button :class="(!isCasino && sportLink === 'sport' ? 'NavigationTab_active' : 'NavigationTab_disable')" @click="isGuest ? openAuthModal('auth') : $router.push('/sports')">
              <span>Sports</span>
            </button>
          </div>
        </div>
      </div>
      
      <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }"
                          :class="`games ${!isCasino ? 'sportSidebar' : ''}`">
        <template>
        <!-- <template v-if="isCasino"> -->

          <div class="logo game" @click="openSearch">
            <web-icon icon="fal fa-search"></web-icon>
            <input :placeholder="$t('general.search')">
          </div>

          <div class="divider"></div>
          
          <!-- <div class="game"> -->
            <a class="NavigationToken_token__qSrff  NavigationToken_active__aTNwj " href="/token">
              <img src="/img/vtx.svg" alt="token" height="48px" width="48">
              <div class="NavigationToken_textContainer__V5wi9">
                <p>Vortex <span class="NavigationToken_code__59rbj">(VTX)</span></p>
                <p class="NavigationToken_coming__w0kag"><span class="NavigationToken_code__59rbj">$0.00</span> <span class="TokenPercentageText_positive__ZCgKR ">+0.00%</span></p>
              </div>
            </a>
          <!-- </div> -->

          <div class="divider"></div>

          <router-link tag="div" to="/" class="game">
  <img src="/img/home.svg" alt="Home" class="icon" width="24" height="24">
  <div>Home</div>
</router-link>

          <div onclick="window.location.href = '/admin'" v-if="$checkPermission('dashboard')" class="game">
            <img src="/img/dashboard.svg" alt="Admin" class="icon">
            <div>{{ $t('general.sidebar.admin') }}</div>
          </div>

          <router-link tag="div" to="/vip" class="game">
            <img src="/img/vip.svg" alt="VIP" class="icon">
            <div>VIP</div>
          </router-link>

          <template v-if="$isAvailable('phoenixSport')">
            <div class="divider"></div>
            <div class="sidebar-description">{{ $t('general.head.sport') }}</div>

            <router-link tag="div" to="/sport" class="game">
              <web-icon icon="sport"></web-icon>
              <div>{{ $t('general.head.sport') }}</div>
            </router-link>
          </template>

          <div class="divider"></div>
          <!-- <div class="sidebar-description">Promotions</div> -->

          <!-- <div class="game featured" @click="isGuest ? openAuthModal('auth') : openBonusModal()">
            <web-icon icon="stars"></web-icon>
            <div>Bonus</div>
          </div> -->

          <div class="game" @click="openLeaderboard()">
            <img src="/img/competition.svg" alt="competition" class="icon">
            <div>Competition</div>
          </div>

          <div class="divider"></div>
          <!-- <div class="sidebar-description">{{ $t('general.sidebar.all_games') }}</div> -->

          <router-link :to="`/casino/game/category/originals`" class="game">
            <img src="/icons/original.svg" alt="ORIGINALS">
            <div>Originals</div>
          </router-link>

          <router-link :to="`/casino/game/category/slots`" class="game">
            <img src="/icons/slots.svg" alt="SLOTS">
            <div>Slots</div>
          </router-link>

          <router-link :to="`/casino/game/category/live`" class="game">
            <img src="/icons/casino.svg" alt="LIVE_CASINO">
            <div>Live Games</div>
          </router-link>

          <!-- <content-placeholders class="game" v-for="n in 17" :key="n" v-if="games.length === 0">
            <content-placeholders-img/>
          </content-placeholders> -->

          <div class="divider"></div>

          <router-link tag="div" to="/partner" class="game">
            <img src="/img/affiliates.svg" alt="affiliates" class="icon">
            <div>{{ $t('footer.affiliates') }}</div>
          </router-link>
        </template>
        <!-- <template v-else>
          <div :class="`game ${betSlip ? 'router-link-exact-active router-link-active' : ''}`"
               @click="$store.dispatch('toggleBetSlip')">
            <web-icon icon="fas fa-ticket-alt"></web-icon>
            <div>{{ $t('sport.bet_slip') }}</div>
          </div>

          <content-placeholders class="game" v-for="n in 17" :key="n" v-if="sportGames.length === 0">
            <content-placeholders-img/>
          </content-placeholders>
          <template v-if="sportGames && sportGames.length > 0">
            <template v-if="!isGuest && $checkPermission('dashboard')">
              <div onclick="window.location.href = '/admin'" class="game">
                <i class="fas fa-cog"></i>
                <div>{{ $t('general.sidebar.admin') }}</div>
              </div>
              <div class="divider"></div>
            </template>

            <div v-for="game in sportGames.filter(e => e.sportType === sportType)" :key="game.id"
                 class="game" @click="navigateTo(game)">
              <web-icon :icon="game.icon"></web-icon>
              <div class="liveCount" v-if="game.liveCount > 0">{{ game.liveCount }}</div>
              <div>{{ game.name }}</div>
            </div>
          </template>
        </template> -->
      </overlay-scrollbars>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex';
  import AuthModal from "../modals/AuthModal.vue";
  import Bus from "../../bus";
  import FaucetModal from "../modals/FaucetModal.vue";
  import LeaderboardModal from "../modals/LeaderboardModal.vue";

  export default {
    data() {
      return {
        promo: false,
        language: null,
        mobileToggle: false,
        tempBlock: false
      }
    },
    created() {
      this.language = this.locale;
      Bus.$on('sidebar:toggleMobile', () => {
        this.tempBlock = true;
        setTimeout(() => this.tempBlock = false, 1);
        this.mobileToggle = !this.mobileToggle;
      });
    },
    computed: {
      ...mapGetters(['isGuest', 'user', 'theme', 'games', 'currencies', 'sidebar', 'sportGames', 'betSlip'])
    },
    methods: {
      openLeaderboard() {
        LeaderboardModal.methods.open();
      },
      openBonusModal() {
        FaucetModal.methods.open();
      },
      navigateTo(sport) {
        this.$store.dispatch('setSportFilter', sport.liveCount === 0 ? 'upcoming' : 'live');
        this.$router.push('/sport/category/' + sport.id);
      },
      closeSidebar() {
        if (!this.tempBlock) this.mobileToggle = false;
      },
      openAuthModal(type) {
        AuthModal.methods.open(type);
      },
      openSearch() {
        Bus.$emit('search:toggle');
      }
    }
  }
  </script>

  <style lang="scss">
  @import "resources/sass/variables";

  .sidebar.mobileToggle {
    display: block !important;
    width: 232px;
    opacity: 1;

    .fixed {
      // padding: 18px 30px;
      padding-bottom: 120px;
    }
  }

  @media(min-width: 1700px) {
    .sidebar.visible ~ .pageWrapper {
      padding-left: $sidebar-width-expand;
    }
  }

  @media(max-width: 991px) {
    .sidebar.visible ~ .pageWrapper {
      padding-left: 0 !important;;
    }
  }

  @media(min-width: 1700px), (max-width: 991px) {
    .sidebar .games {
      height: calc(100% - 95px) !important;
    }

    .sidebar .sidebar-header {
      display: flex !important;
      
    }

    .sidebar.visible {
      width: $sidebar-width-expand;

      .sidebar-description {
        opacity: 1;
        margin-left: 25px;
        height: auto;
      }

      .divider {
        // margin-top: 35px !important;
      }

      .fixed {
        width: $sidebar-width-expand;

        .sidebar-header {
          
          .NavigationTab_root__3914H {
            display: flex;
            padding: 1rem;
            width: 100%;
            transition: 0.2s opacity;
            opacity: 1;
            pointer-events: auto;
            
            .NavigationTab_container__1rlca {
              border-radius: .375rem;
              overflow: hidden;
              background: #504c54;
              height: 3rem;
              display: flex;

              button {
                color: white;
                flex: 1 1;
                text-align: center;
                font-size: .875rem;
                line-height: 150%;

                span {
                  padding: 10px;
                }

                &:hover {                
                  background: url('/img/misc/casino-sports-bg-disable.svg');
                  border: 1px solid #343843;                
                }
              }

              button:first-child {
                border-right: 0;
                border-radius: .375rem 0 0 .375rem;
              }

              button:last-child {
                border-left: 0;
                border-radius: 0 .375rem .375rem 0;
              }

              .NavigationTab_active {
                font-weight: 700;
                text-shadow: 0 1px 0 #000;
                background: url('/img/misc/casino-sports-bg-active.svg'); // #6d39d5;
                border: 1px solid #504c54;
              }

              .NavigationTab_disable {
                font-weight: 700;
                color: gray;
                text-shadow: 0 1px 0 #000;
                background: #202329;
                border: 1px solid #504c54;
              }
            }
          }
        }

        .NavigationToken_token__qSrff {
          gap: .62rem;
          // border-bottom: 1px solid #2a2e38;
          padding: .5rem 1.2rem;
          font-size: .875rem;
          font-weight: 700;
          display: flex;
          justify-content: start;
          transition: 0.2s all;
          position: relative;

          &:hover p:first-child {
            color: #886cff;
          }

          .NavigationToken_textContainer__V5wi9 {
            display: flex;
            flex-direction: column;
            justify-content: center;

            @media(max-width: 991px) {
              display: none;
            }

            p {
              text-align: left;
              transition: 0.2s color;
              margin: 0;

              .NavigationToken_code__59rbj {
                color: #fff;
                
              }
            }

            .NavigationToken_coming__w0kag {
              color: #3dd179;
              margin: 0;

            }
          }
        }

        .game {
          justify-content: unset;
          padding-left: 40px;
          padding-right: 40px;
          position: relative;
          white-space: nowrap;
          color: white !important;

          .liveCount {
            @include themed() {
              background: t('secondary');
              color: black;
              width: 15px;
              height: 15px;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              margin-left: 10px;
              font-size: .6em;
              font-weight: 600;
              position: absolute;
              top: 8px;
            }
          }

          i {
            width: 25px;
          }

          svg {
            margin-right: 11px;
          }

          div {
            display: block;
            opacity: 1;
            margin-left: 10px;
            max-width: 80%;
            overflow: hidden;
            text-overflow: ellipsis;
          }

          &:hover {
            color: #886cff !important;
          }
        }
      }

      .promotion {
        height: unset;

        .name, .description {
          opacity: 1;
          transition-delay: .4s;
          display: block;
        }

        .image {
          position: unset;
          top: unset;
          left: unset;
        }
      }
    }
  }

  .sidebar {
    width: $sidebar-width;
    flex-shrink: 0;
    z-index: 38002;
    transition: width 0.3s ease;

    .os-scrollbar-horizontal {
      display: none;
    }

    .sidebar-description {
      font-weight: 600;
      left: 0;
      height: 0;
      transition: height .3s ease, opacity .3s ease, margin-left .3s ease;
      margin-left: 5px;
      opacity: 0;
      text-transform: uppercase;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .promotion {
      margin-top: 20px;
      background: linear-gradient(274.28deg, rgba(255, 195, 76, 0) 0%, rgb(25 32 46) 100%), #20293c;
      padding: 20px 40px;
      display: flex;
      align-items: center;
      cursor: pointer;
      position: relative;
      height: 90px;
      transition: height .3s ease;

      .image {
        width: 40px;
        height: 40px;
        background: url('/img/misc/treasure.png') no-repeat center;
        background-size: cover;
        margin-right: 20px;

        position: absolute;
        top: 25px;
        left: 20px;
      }

      .name, .description {
        transition: opacity .3s ease;
        opacity: 0;
        transition-delay: 0s;
        display: none;
      }

      .name {
        color: #FFC34C;
        text-shadow: 0 0 8px rgba(255, 195, 76, 0.62);
        font-size: 16.5px;
      }

      .description {
        margin-top: 2px;
        font-weight: 500;
        font-size: 14.5px;
      }
    }

    .fixed {
      position: fixed;
      top: 0;
      width: $sidebar-width;
      height: 100%;

      @include themed() {
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.56);
        background: t('sidebar');
        transition: background 0.15s ease, width .3s ease;

        .sidebar-header {
          height: $header-height;
          display: flex;

          @media(max-width: 768px) {
            display: none !important;
          }

          .toggle {
            display: flex;
            // padding: 10px 14px;
            // background: t('chat-accent');
            margin: auto;
            cursor: pointer;
            font-size: 1.5rem;
            transition: background .3s ease;
            border-radius: 4px;

            &:hover {
              background: t('header-block');
            }
          }

        }

        .games {
          // height: 100%;

          .logo {
            // background: url('/img/misc/logo.png') no-repeat center;
            width: 100%;
            // height: 100%;
            background-size: contain;
            margin-top: 12px;
            margin-bottom: 12px;
            margin-left: auto;
            display: flex;
            cursor: pointer;
            padding: 16px;

            position: relative;
            cursor: pointer;
            width: 100%;

            input {
              padding-left: 50px;
              pointer-events: none;
              border-radius: .375rem;
              background-color: #0000 !important;
              border-color: #2a2e38 !important;

              // @include themed() {
              //   background: t('input-dark');

              //   &::placeholder {
              //     color: t('link');
              //   }
              // }
            }

            svg, i {
              position: absolute;
              top: 50%;
              transform: translateY(-50%);
              left: 30px;
            }
          }
          
          &.sportSidebar {
            height: calc(100% - 35px);
          }

          border-radius: 3px;

          .divider {
            margin: 5px 0;
            transition: margin-top .3s ease;
            border-bottom: 1px solid #2a2e38 !important;
          }

          .btn {
            width: calc(100% - 30px);
            margin-left: 15px;
            margin-right: 15px;
            margin-bottom: 15px;
            border-radius: 20px;

            &.btn-primary {
              border-bottom: 3px solid darken(t('secondary'), 5%);
            }

            &.btn-secondary {
              border-bottom: 3px solid darken($gray-600, 5%);
            }
          }
        }
      }

      .sidebar-header {
        .NavigationTab_root__3914H {
          display: none;
          opacity: 0;
          transition: opacity 1s ease;
        }
      }

      .NavigationToken_token__qSrff {
        gap: .62rem;
        // border-bottom: 1px solid #2a2e38;
        // padding: 1rem;
        font-size: .875rem;
        font-weight: 700;
        display: flex;
        transition: 0.2s all;
        position: relative;        
        justify-content: center;

        .NavigationToken_textContainer__V5wi9 {
          display: none;
        }
      }

      .game {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 60px;
        font-size: 16px;
        cursor: pointer;
        position: relative;
        transition: color .3s ease;

        @include themed() {
          color: t('link');

          &.featured {
            color: #ffd92b;
          }
        }

        &.router-link-exact-active {
          @include themed() {
            color: #886cff !important;
            border-left: solid 5px #886cff !important;
            background-color: #1b172d;
          }

          &:before {
            opacity: 1;
          }

          &:after {
            opacity: 1;
          }
        }

        @include themed() {
          &.highlight {
            color: t('secondary') !important;
          }
        }

        div {
          display: none;
          opacity: 0;
          transition: opacity 1s ease;
        }

        .vue-content-placeholders-img {
          display: block !important;
          opacity: 1 !important;
        }

        .vue-content-placeholders-img {
          height: 15px;
          width: 15px;
          border-radius: 3px;
        }

        img {
          width: 14px;
          height: 14px;
        }

        i {
          cursor: pointer;
        }

        &:hover {
          opacity: 1;
        }

        .online {
          position: absolute !important;
          top: 4px !important;
          left: 17px !important;
          border-radius: 50%;
          width: 15px;
          @include themed() {
            background: t('secondary');
          }
          color: white;
          height: 15px;
          font-size: 0.5em;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;
        }
      }

      .game.router-link-exact-active {
        opacity: 1;
      }
    }

    &.hidden .fixed .NavigationToken_token__qSrff .NavigationToken_textContainer__V5wi9 {
      display: none;
    }
    
    &.visible .fixed .sidebar-header {
      // .logo {
      //   display: flex; // unset;
      //   align-items: center;
      // }

      .toggle {
        margin-left: 13px !important;
      }
    }

    &.hidden .fixed .sidebar-header {
      .NavigationTab_root__3914H {
        display: none;
        opacity: 0;
        transition: opacity 1s ease;
      }
    }

    &.visible .fixed .games {
      .logo {
        display: flex; // unset;
        align-items: center;
        height: 60px;
      }

    }
  }

  .game div {
    font-weight: bold;
  }

  @media(max-width: 991px) {
    .sidebar {
      display: none;
    }
  }
</style>
