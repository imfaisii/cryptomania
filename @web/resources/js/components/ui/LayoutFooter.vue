<template>
  <footer>
    <div class="container">
      <div class="footer">
        <div class="column grid">
          <div class="links">
            <div style="display: flex !important">
              <router-link to="/" tag="div" class="logo" style="display: flex !important; align-items: center;"></router-link>
              <div @click="$router.push('/')" style="padding-left: 0 !important; cursor: pointer; font-size: 1.2rem;">CryptoMania</div>
            </div>
          </div>
          <!-- <div class="links">
            <div class="name">Games</div>
            <div class="link-group">
              <router-link to="/casino/game/category/originals">Originals</router-link>
              <router-link to="/casino/game/category/slots">Slots</router-link>
              <router-link to="/casino/game/category/live">Live</router-link>
              <router-link to="/sport" v-if="$isAvailable('phoenixSport')">{{ $t('general.head.sport') }}</router-link>
            </div>
          </div> -->
          <div class="links">
            <div class="name">Support</div>
            <div class="link-group">
              <router-link to="/">Live Support</router-link>
              <router-link to="/">Help Center</router-link>
              <a href="https://www.gambleaware.org/" target="_blank" class="router-link-active">Game Responsiblity</a>
            </div>
          </div>
          <div class="links">
            <div class="name">Platform</div>
            <div class="link-group">
              <router-link to="/fairness">Provably Fair</router-link>
              <router-link to="/vip">VIP</router-link>
              <a href="javascript:;" @click="openLeaderboard()" class="router-link-active">Competition</a>
              <router-link to="/partner">Affiliates Program</router-link>
            </div>
          </div>
          <div class="links">
            <div class="name">Policy</div>
            <div class="link-group">
              <router-link to="/terms">Terms of Service</router-link>
              <router-link to="/privacyPolicy">Privacy Policy</router-link>
              <router-link to="/amlview">AML Policy</router-link>
            </div>
          </div>
          <div class="links">
            <div class="name">Community</div>
            <div class="link-group">
              <a href="https://x.com/CryptoManiagg" target="_blank" class="router-link-active">X</a>
              <a href="https://www.instagram.com/cryptomania.gg/" target="_blank" class="router-link-active">Instagram</a>
              <a href="https://discord.gg/ZxSS9UZaqY" target="_blank" class="router-link-active">Discord</a>
              <a href="https://t.me/cryptomaniavtx" target="_blank" class="router-link-active">Telegram</a>
            </div>
          </div>
        </div>        
        <div class="notes">
          <!-- <i>18+</i> -->
          <p>This website offers gaming with risk experience. To be a user of our site you must be over 18 y.o. We are not responsible for the violation of your local laws related to Vortex Holdings LLC, Play responsibly and have fun on CryptoMania.</p>
          <p>CryptoMania is owned and operated by Vortex Holdings LLC, a company incorporated under the laws of Saint Vincent and the Grenadines with registration number 3615.</p>
        </div>

        <div class="image-container">     
<img data-v-49749118="" src="/img/BTC-1713787614847.svg" alt="Bitcoin" width="32" height="32">
<img data-v-49749118="" src="/img/DOGE-1713787614847.svg" alt="Dogecoin" width="32" height="32">
<img data-v-49749118="" src="/img/XRP-1713787614847.svg" alt="XRP" width="32" height="32">
<img data-v-49749118="" src="/img/ETH-1713787614847.svg" alt="Ethereum" width="32" height="32">
<img data-v-49749118="" src="/img/EOS-1713787614847.svg" alt="EOS" width="32" height="32">
<img data-v-49749118="" src="/img/LTC-1713787614847.svg" alt="LTC" width="32" height="32">

          
          


        </div>

        <div class="image-container" style="border-bottom: 2px solid #1f2d49">
          <div style="text-align: center;">
            <a href="//www.dmca.com/Protection/Status.aspx?ID=cfcc00c5-5d33-4e31-ace0-14e2b2c1d546" title="DMCA.com Protection Status" class="dmca-badge"> <img src ="https://images.dmca.com/Badges/_dmca_premi_badge_5.png?ID=cfcc00c5-5d33-4e31-ace0-14e2b2c1d546"  alt="DMCA.com Protection Status" /></a>  <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"> </script></div>
        </div>
        
        <div style="display: flex;margin: 20px 0;">
          <div>{{`1 ${currencies[currency].name} = `}}<unit :fiat="true" :to="currency"
                    :value="1"></unit></div>
          <div style="margin-left: auto;">© 2024 CryptoMania.gg | All Rights Reserved</div>
        </div>

      </div>
    </div>
    
  </footer>
</template>

<script>
  import { mapGetters } from 'vuex';
  import TermsModal from "../modals/TermsModal.vue";
  import LeaderboardModal from "../modals/LeaderboardModal.vue";

  export default {
    computed: {
      ...mapGetters(['isGuest', 'currencies', 'unit', 'currency', 'fiatView'])
    },
    methods: {
      openTerms(type) {
        TermsModal.methods.open(type);
      },
      openLeaderboard() {
        LeaderboardModal.methods.open();
      },
    }
  }
</script>

<style lang="scss">
  @import "resources/sass/variables";

  footer {
    @include themed() {
      background: t('footer');
      margin-top: 15px;

      .logo {
        height: 30px;
        width: 30px;
        display: flex;
        cursor: pointer;
        background: url('/img/misc/logo.png') no-repeat center;
        background-size: contain;
        margin-left: unset;

        @media(min-width: 991px) {
          display: none;
        }
      }

      .footer {
        display: flex;
        flex-direction: column;
        padding: 0 50px;

        @media(max-width: 991px) {
          padding: 0;
        }

          .notes {
            display: flex;
            flex-direction: column;
            margin: 15px 0;
            border-bottom: 2px solid t('border');

            @media(max-width: 991px) {
              margin-top: 10px;
            }

            i {
              width: 36px;
              height: 36px;
              border: 2px solid #fff;
              border-radius: 50%;
              text-align: center;
              line-height: 32px;
              font-style: normal;
              font-weight: 600;
              font-size: 13px;
              margin-bottom: 15px;
            }

            small {
              font-size: 11px;
              opacity: .4;
            }
          }

        .column {
          display: flex;
          width: 100%;
          align-items: center;
          padding: 25px 0;
          border-bottom: 2px solid t('border');

          &.payment {
            img {
              width: 0;
              flex: 1;
            }

            @media(max-width: 991px) {
              display: none;
            }
          }

          &:last-child {
            border-bottom: none;
          }

          &.grid {
            display: flex; // grid;
            // grid-gap: 15px;
            gap: 15px;
            // grid-template-columns: auto auto 576px;
            align-items: flex-start;
            justify-content: space-between;
            padding: 40px 0;

            @media(max-width: 1456px) {
              // display: flex;
              // flex-direction: column;
            }

            @media(max-width: 991px) {
              display: flex;
            }
          }

          .links {
            display: flex;
            flex-direction: column;

            .name {
              text-transform: uppercase;
              margin-bottom: 15px;
              // opacity: .6;
              font-weight: 600;
            }

            .link-group {
              display: flex;
              flex-direction: column;

              a {
                // opacity: .4;
                color: #828998;
                margin-bottom: 5px;
                transition: opacity .3s ease;

                &:last-child {
                  margin-bottom: 0;
                }

                &:hover {
                  // opacity: 1;
                  color: rgb(221, 218, 218);
                }
              }
            }
          }

          .social-icons {
            display: flex;

            .social-icon {
              margin-right: 10px;
              border-radius: 10px;
              background: t('block');
              padding: 5px 10px;
              transition: background .3s ease, color .3s ease;
              cursor: pointer;

              &:hover {
                color: white;
                background: lighten(t('block'), 5%);
              }

              &:last-child {
                margin-right: 0;
              }
            }
          }

          .copy {
            opacity: .3;
            font-size: 11px;
          }
        }
      }
    }
  }

  @include media_breakpoint_down(md) {
    footer .footer {
      padding-bottom: 53px !important;

      .column {
        flex-direction: column;

        .copy {
          text-align: center;
          margin-bottom: 5px;
        }
      }
    }
  }
  .image-container {
  display: flex; /* Use flexbox to align items */
  justify-content: center; /* Center items horizontally */
  align-items: center; /* Center items vertically */
  padding: 10px;
}

.image-container img {
  margin-right: 10px; /* Add spacing between images */  
}


</style>
