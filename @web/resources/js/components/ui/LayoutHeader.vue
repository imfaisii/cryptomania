<template>
  <header>
    <div class="fixed" style="border-bottom: 1px solid #2a2e38 !important;">
      <div class="container">
        <div class="header-container">
          <div class="switcher">
            <router-link to="/" tag="div" class="logo" style="display: flex !important"></router-link>
            <div class="switch home-logo" :class="isCasino ? 'active' : ''" @click="$router.push('/')" style="padding-left: 0 !important;">
              CryptoMania
            </div>
            <template v-if="$isAvailable('phoenixSport')">
              <div class="switch sportSwitch" :class="(!isCasino && sportLink === 'sport' ? 'active' : '')" @click="$router.push('/sport')">
                <web-icon icon="sport"></web-icon>
              </div>
            </template>
            <!-- <div class="switch" @click="openSearch">
              <web-icon icon="fal fa-search"></web-icon>
            </div> -->
          </div>

          <content-placeholders v-if="!isGuest && !currencies" class="wallet_loader">
            <content-placeholders-img/>
          </content-placeholders>
          <div style="display: flex;">
            <div class="wallet" v-if="!isGuest && currencies" v-click-outside="() => this.expand = false">
              <div :class="`wallet-switcher ${expand ? 'active' : ''}`">
                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                  <div v-for="(currency, i) in currencies" v-if="currency.balance" class="option" :key="i"
                      @click="$store.commit('setCurrency', currency.id); setCookie('currency', currency.id, 365); expand = false">
                    <div class="currency">
                      <web-icon :icon="currency.icon" :style="{ color: currency.style }"></web-icon>
                    </div>
                    <div class="wallet-switcher-content">
                      <span>{{ currency.name }}</span>
                      <div>
                        <unit :fiat="fiatView" :to="currency.id"
                              :value="demo ? currency.balance.demo : currency.balance.real"></unit>
                      </div>
                    </div>
                  </div>
                </overlay-scrollbars>
                <div class="option" @click="$refs.demoSwitch.toggle()">
                  <div class="wallet-switcher-content">
                    {{ $t('general.head.wallet_demo') }}
                  </div>
                  <div @click.stop>
                    <web-switch :value="demo" :onChange="() => $store.commit('setDemo', !demo)" ref="demoSwitch"></web-switch>
                  </div>
                </div>
                <div class="option" @click="$refs.fiatSwitch.toggle()" style="margin-top: 10px; padding: 5px 15px">
                  <div class="wallet-switcher-content">
                    {{ $t('general.head.view_in_fiat') }}
                  </div>
                  <div @click.stop>
                    <web-switch :value="fiatView" :onChange="() => $store.commit('setFiatView', !fiatView)" ref="fiatSwitch"></web-switch>
                  </div>
                </div>
                <!--
                <div class="option select-option">
                  <div class="wallet-switcher-content" style="padding-right: 15px; width: 100%">
                    {{ $t('general.unit') }}:
                    <select @change="$store.commit('setUnit', selectedUnit)" v-model="selectedUnit">
                      <option value="btc">BTC</option>
                      <option value="mbtc">milliBTC</option>
                      <option value="bit">microBTC</option>
                      <option value="satoshi">Satoshi</option>
                    </select>
                  </div>
                </div>
                -->
              </div>
              <div class="btn btn-secondary icon" @click="expand = !expand" style="margin-right: 0; width: 130px;">
                <web-icon :icon="currencies[currency].icon" v-if="currencies[currency]"
                          :style="{ color: currencies[currency].style }"></web-icon>
                <unit :fiat="fiatView" :to="currency" style="margin-left: 5px"
                      :value="currencies[currency].balance[demo ? 'demo' : 'real']"></unit>
                <i class="fal fa-angle-down"></i>
              </div>
            </div>
            <div v-if="!isGuest && currencies" @click="demo ? openDemoBalanceModal() : openWalletModal()">   
              <button class="wallet-balance-btn" id="wallet-btn"><img src="/img/wallet.svg" alt="wallet">
                <span class="wallet-balance-span">Wallet</span>
              </button>           
              <transition-group mode="out-in" name="balance-a" :style="{ position: 'absolute' }">
                                <span :key="`key-${i}`" v-for="(animate, i) in animated"
                                      :class="`animated text-${animate.diff.action === 'subtract' ? 'danger' : 'success'}`">
                                    <unit :fiat="fiatView" :to="currency" :value="animate.diff.value"></unit>
                                </span>
              </transition-group>
            </div>
          </div>
          
          <div v-if="isGuest" :class="`right ${isGuest ? 'ml-auto' : ''}`">
            <button class="btn btn-transparent" @click="openAuthModal('auth')">{{ $t('general.auth.login') }}</button>
            <button class="btn btn-primary" @click="openAuthModal('register')">{{
                $t('general.auth.register')
              }}
            </button>
          </div>
          <div v-else class="right">
            <router-link tag="img" :to="`/profile/${user.user._id}`" :src="user.user.avatar"></router-link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
  import Bus from '../../bus';
  import {mapGetters} from 'vuex';
  import AuthModal from "../modals/AuthModal.vue";
  import DemoBalanceModal from "../modals/DemoBalanceModal.vue";
  import TermsModal from "../modals/TermsModal.vue";
  import WalletModal from "../modals/WalletModal.vue";

  export default {
    computed: {
      ...mapGetters(['user', 'isGuest', 'demo', 'unit', 'currency', 'currencies', 'sidebar', 'fiatView'])
    },
    data() {
      return {
        expand: false,
        selectedUnit: 'btc',
        animated: [],

        nonLocalFound: false
      }
    },
    mounted() {
      this.selectedUnit = this.unit;

      Bus.$on('event:balanceModification', (e) => {
        setTimeout(() => {
          const currencies = this.currencies;
          currencies[e.currency].balance = {
            real: e.balance,
            demo: e.demo_balance
          };
          this.$store.dispatch('setCurrencies', currencies);

         // if(this.fiatView)  e.diff.value = this.tokenToFiat(this.currencies[e.currency].price, e.diff.value).toFixed(2); 

          this.animated.push(e);
          setTimeout(() => this.animated = this.animated.filter((a) => a !== e), 1000);
        }, e.delay);
      });
    },
    methods: {
      openWalletModal() {
        WalletModal.methods.open();
      },
      openSearch() {
        Bus.$emit('search:toggle');
      },
      openAuthModal(type) {
        AuthModal.methods.open(type);
      },
      openDemoBalanceModal() {
        DemoBalanceModal.methods.open();
      },
      openFaucetModal() {
        if (this.isGuest) return this.openAuthModal('auth');
        this.$router.push('/bonus');
      },
      openTerms(type) {
        TermsModal.methods.open(type);
      }
    }
  }
</script>

<style lang="scss">
  @import "resources/sass/variables";

  header {
    height: $header-height;
    display: initial !important;
    flex-shrink: 0;
    z-index: 38001;

    button {
      font-size: 0.9em !important;
      padding: 10px 20px !important;
    }

    .fixed {
      height: $header-height;
      position: sticky;
      left: 0;
      top: 0;
      width: 100%;
      z-index: 10001;

      @include themed() {
        box-shadow: t('shadow');
      }

      .sidebar-switch {
        opacity: .4;
        cursor: pointer;
        margin-left: 15px;
      }

      .header-container {
        grid-template-columns: 1fr auto 1fr;
        gap: 1rem;
        display: grid; // flex;
        align-items: center;
        height: $header-height;

        .header_search_bar {
          position: relative;
          cursor: pointer;
          margin-left: auto;

          @media(max-width: 1500px) {
            display: none;
          }

          input {
            cursor: pointer !important;
            padding-left: 55px !important;
            @include themed() {
              background: t('input') !important;
              border: 1px solid t('border') !important;
            }
            pointer-events: none !important;
            border-radius: 50px;
          }

          i, svg {
            position: absolute;
            top: 15px;
            left: 25px;
          }
        }

        .switcher {
          display: flex;
          align-items: center;

          .home-logo {
            background: none !important;
            font-size: 24px;
            font-weight: 700;
            color: white; // rgb(118, 89, 250);
            // background: url('/img/misc/logo.png') no-repeat center;
            // width: 100%;
            // height: 100%;
            // background-size: contain;
            // margin-top: auto;
            // margin-bottom: auto;
            // margin-left: auto;
            // display: none;
            // cursor: pointer;
          }

          .switch {
            
            @media(max-width: 991px) {
              display: none !important;
            }

            @include themed() {
              transition: background .3s ease, opacity .3s ease;
              opacity: .8;
              display: flex;
              align-items: center;
              justify-content: center;
              margin-right: 15px;
              cursor: pointer;
              padding: 15px;
              border-radius: 50%;
              background: t('wallet');

              &:last-child {
                margin-right: 0;
              }

              svg, i {
                transition: color .3s ease;
                color: inherit;
                display: flex;
                align-items: center;
                justify-content: center;
              }

              &.active {
                background: t('header-block');

                .name {
                  font-weight: 600;
                }

                svg, i {
                  color: t('secondary');
                }
              }
            }
          }
        }

        @media(max-width: 1800px) {
          // padding: 0 100px;
        }

        @media(max-width: 1500px) {
          // padding: 0 50px;
        }

        @media(max-width: 1350px) {
          // padding: 0 20px;
        }

        .menuSwitcher {
          display: none;
          margin-left: 15px;
          opacity: .5;
          transition: opacity .3s ease;
          cursor: pointer;

          &:hover {
            opacity: 1;
          }
        }
      }

      @include themed() {
        background: t('header');

        .logo {
          height: 40px;
          width: 40px;
          display: flex;
          cursor: pointer;
          background: url('/img/misc/logo.png') no-repeat center;
          background-size: contain;
          margin-left: unset;

          @media(min-width: 991px) {
            display: none;
          }
        }
      }

      .wallet-balance-btn {
        background-color: #6930f2;
        transition: all .3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        column-gap: .625rem;
        border-radius: 0.375rem;
        padding: .6rem 1rem;
        height: 3rem;
        color: #fff;
        border: 0;
        white-space: nowrap;
        font-size: 0.875rem;
        margin-left: 10px;

        &:hover {
          background-color: #8453f7;
        }

        .wallet-balance-span {
          display: block;
          
          @media(max-width: 991px) {
            display: none !important;
          }

        }
      }

      .right {
        display: flex;
        justify-self: flex-end;
        align-items: center;

        &.ml-auto {
          margin-left: auto;
        }

        img {
          width: 45px;
          height: 45px;
          border-radius: 50%;
          cursor: pointer;
          margin-left: 10px;
        }

        @media(max-width: 991px) {
          [data-notification-view] {
            display: none !important;
          }
        }

        .action {
          display: flex;
          align-content: center;
          position: relative;
          background: rgb(25, 32, 46);
          border: 1px solid #112D35;
          border-radius: 4px;

          .notification {
            position: absolute !important;
            top: 7px !important;
            left: 19px !important;
            width: 8px !important;
            height: 8px !important;
          }

          i {
            font-size: 1.15em;
            margin: 10px;
            opacity: 0.35;
            transition: opacity 0.3s ease;

            &:hover {
              opacity: 1;
              cursor: pointer;
            }
          }
        }

        .btn {
          padding: 10px 30px !important;
          border-radius: 200px !important;
        }
      }
    }
  }

  @include only_safari('header', (
    display: contents 
  ));

  .balance-a-enter-active, .balance-a-leave-active {
    transition: all 1s ease;
  }

  .balance-a-enter {
    margin-top: 25px;
    opacity: 1 !important;
  }

  .balance-a-enter-to {
    margin-top: 0;
    opacity: 0 !important;
  }

  .balance-a-leave, .balance-a-leave-to {
    opacity: 0 !important;
  }
</style>
