<template>
  <div class="container" v-infinite-scroll="loadGames">
    <loader class="profile-loader" v-if="!profile"></loader>
    <div class="profile-container" v-if="profile">
      <div class="profile-header">
        <div class="profile">
          <div class="avatar">
            <img :src="profile.user.avatar" alt>
          </div>
          <div class="name">
            {{ profile.user.name }}
          </div>
        </div>
        <div class="profileMenuContainer">
          <div class="profileMenu os-host-flexbox w-100">
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
              <div :class="`tab ${tab === 'info' ? 'active' : ''}`" @click="tab = 'info'">{{
                  $t('general.head.profile')
                }}
              </div>
              <div v-if="profile.isOwner" :class="`tab ${tab === 'security' ? 'active' : ''}`"
                   @click="tab = 'security'">{{ $t('general.profile.security') }}
              </div>
              <div v-if="profile.isOwner" :class="`tab ${tab === 'settings' ? 'active' : ''}`"
                   @click="tab = 'settings'">{{ $t('general.profile.settings') }}
              </div>
              <div v-if="profile.isOwner" class="tab" @click="logout">{{ $t('general.head.logout') }}</div>
            </overlay-scrollbars>
          </div>
        </div>
      </div>
      <div class="profile-content">
        <div class="incognito"
             v-if="(!profile.isOwner && profile.user.private_profile) && (!user || !$checkPermission('ignore_privacy'))">
          <img src="/img/misc/incognito-dark.png" data-incognito-dark>
          <img src="/img/misc/incognito-default.png" data-incognito-white>
          <div class="incognito-desc">
            {{ $t('general.profile.incognito') }}
          </div>
        </div>
        <div class="tab_content" v-else-if="tab === 'info'">
          <div class="incognito" v-if="profile.games === 0">
            <i class="fal fa-history"></i>
            <div class="incognito-desc">
              {{ $t('general.profile.empty') }}
            </div>
          </div>
          <template v-else>
            <div class="cat">{{ $t('general.profile.latest_games') }}</div>
            <table class="live-table">
              <thead>
              <tr>
                <th>{{ $t('general.bets.game') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('general.bets.time') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('general.bets.bet') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('general.bets.mul') }}</th>
                <th>{{ $t('general.bets.win') }}</th>
              </tr>
              </thead>
              <tbody class="live_games">
              <tr v-for="game in userGames">
                <th>
                  <div class="gameIcon" style="display: flex; align-items: center;">
                    <router-link :to="'/casino/game/'+game.metadata.id" tag="div">
                      <img :src="game.metadata.category.includes('originals')?'/icons/original.svg':(game.metadata.category.includes('slots')?'/icons/slots.svg':'icons/casino.svg')" alt="ORIGINALS">
                    </router-link>
                    <div class="name" style="margin-left: 5px;">
                      <div>
                        <router-link :to="'/casino/game/'+game.metadata.id">{{ game.metadata.name }}</router-link>
                      </div>
                      <!-- <a href="javascript:void(0)"
                         @click="openOverviewModal(game.game._id, game.game.game)">{{ $t('general.overview') }}</a> -->
                    </div>
                  </div>
                </th>
                <th class="d-none d-md-table-cell">
                  <div>
                    {{ new Date(game.game.created_at).toLocaleString() }}
                  </div>
                </th>
                <th data-highlight class="d-none d-md-table-cell">
                  <div>
                    <web-icon :icon="currencies[game.game.currency].icon"
                            :style="{ color: currencies[game.game.currency].style }"></web-icon>
                    <unit :fiat="fiatView" :to="game.game.currency" :value="game.game.wager" style="margin-left: 5px;"></unit>
                  </div>
                </th>
                <th data-highlight class="d-none d-md-table-cell">
                  <div style="display: flex; align-items: center;">
                    <img src="/img/increase.svg" style="margin-right: 5px;">
                    {{
                      (game.game.status === 'win' || game.game.multiplier < 1 ? game.game.multiplier : 0).toFixed(2)
                    }}x
                  </div>
                </th>
                <th>
                  <div :class="game.game.status === 'win' ? 'live-win' : ''">
                    <web-icon :icon="currencies[game.game.currency].icon"
                            :style="{ color: currencies[game.game.currency].style }"></web-icon>
                    <unit :fiat="fiatView" :to="game.game.currency" :value="game.game.profit" style="margin-left: 5px;"></unit>
                  </div>
                </th>
              </tr>
              </tbody>
            </table>
          </template>
        </div>
        <div class="tab_content" v-if="profile.isOwner && tab === 'security'">
          <div class="cat">{{ $t('general.profile.general') }}</div>
          <div class="input-profile-group">
            <div>{{ $t('general.profile.login') }}</div>
            <input placeholder="Login" :value="profile.user.name" disabled style="opacity: .5">
            <!--
            <button class="btn btn-primary">{{ $t('general.profile.update') }}</button>
            -->
          </div>

          <div class="input-profile-group">
            <div>{{ $t('general.profile.email') }}</div>
            <input placeholder="Email" v-model="email" type="email">
            <button class="btn btn-primary" @click="updateEmail">{{ $t('general.profile.update') }}</button>
          </div>

          <div class="cat">{{ $t('general.profile.password') }}</div>
          <div class="input-profile-group">
            <div>{{ $t('general.profile.password_current') }}</div>
            <input type="password" v-model="old_password">
          </div>
          <div class="input-profile-group">
            <div>{{ $t('general.profile.password_new') }}</div>
            <input type="password" v-model="new_password">
          </div>
          <div class="input-profile-group">
            <div>{{ $t('general.profile.password_repeat') }}</div>
            <input type="password" v-model="confirm_password">
            <button class="btn btn-primary" @click="updatePassword">{{ $t('general.profile.password_reset') }}</button>
          </div>

          <div class="cat">{{ $t('general.profile.2fa') }}</div>
          <div class="settingsNotify" style="text-align: left">
            <div class="settingsNotifyLoading" v-if="tfa_loading">
              <loader></loader>
            </div>

            <template v-if="!profile.user.tfa_enabled">
              {{ $t('general.profile.copy_this_to_2fa') }}
              <input onclick="this.select()" type="text" style="cursor: pointer !important;" class="mt-1"
                     :value="profile.secret" readonly>
              <div class="mt-2">
                <div class="text-center mb-2 mt-2">{{ $t('general.profile.keep_secure') }}</div>
                <canvas id="qrcanvas" class="d-flex ml-auto mr-auto"></canvas>
              </div>
              <div>{{ $t('general.profile.2fa_code') }}</div>
              <input type="text" v-model="tfa_code" class="mt-1">
              <button class="btn btn-primary mt-2 btn-block" @click="enable2FA">{{
                  $t('general.profile.2fa_enable')
                }}
              </button>
            </template>
            <template v-else>
              <div class="text-center" v-html="$t('general.profile.2fa_enabled')"></div>
              <button class="btn btn-primary mt-2 btn-block" @click="disable2FA">{{
                  $t('general.profile.disable_2fa')
                }}
              </button>
            </template>
          </div>
        </div>
        <div class="tab_content" v-if="profile.isOwner && tab === 'settings'">
          <div class="avatar-settings">
            <img alt :src="profile.user.avatar">
            <input class="d-none" type="file" data-file>
            <button class="btn btn-primary" @click="uploadAvatar">{{ $t('general.profile.change_avatar') }}</button>
          </div>

          <div class="cat">{{ $t('general.profile.privacy') }}</div>
          <div class="toggle">
            <label class="form-check-label">{{ $t('general.profile.set_private_profile') }}</label>
            <web-switch :value="profile.user.private_profile" :onChange="togglePrivateProfile"></web-switch>
          </div>
          <div class="toggle">
            <label class="form-check-label">{{ $t('general.profile.set_private_bets') }}</label>
            <web-switch :value="profile.user.private_bets" :onChange="togglePrivateBets"></web-switch>
          </div>
          <div class="cat">{{ $t('general.profile.fairness') }}</div>
          <div>{{ $t('general.profile.client_seed') }}</div>
          <a href="javascript:void(0)" @click="openClientSeedModal">{{ profile.user.client_seed }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex';
  import ChangeClientSeedModal from "../modals/ChangeClientSeedModal.vue";
  import OverviewModal from "../modals/OverviewModal.vue";

  import qr from 'qrcode';

  export default {
    data() {
      return {
        profile: null,
        tab: window.location.hash.length > 0 ? window.location.hash.substr(1) : 'info',

        tfa_code: '',
        tfa_loading: false,

        userGames: [],
        gamesPage: 0,

        email: '',
        old_password: '',
        new_password: '',
        confirm_password: '',

        avatarInit: false,
        chartInit: false
      }
    },
    computed: {
      ...mapGetters(['isGuest', 'currencies', 'games', 'user', 'theme', 'fiatView'])
    },
    created() {
      axios.post('/api/profile/getUser', {id: this.$route.params.tag}).then(({data}) => {
        this.profile = data;

        this.email = this.profile.user.email;
      });
    },
    watch: {
      tab() {
        this.loadTab();
      },
      profile() {
        this.loadTab();
      }
    },
    methods: {
      logout() {
        this.$store.dispatch('logout');
        this.$router.push('/');
      },
      loadGames() {
        if (this.tab === 'info' && this.profile) axios.get(`/api/user/games/${this.profile.user._id}/${this.gamesPage}`).then(({data}) => {
          this.gamesPage += 1;
          this.userGames = this.userGames.concat(data);
        });
      },
      openOverviewModal(game_id, api_id) {
        OverviewModal.methods.open(game_id, api_id);
      },
      updatePassword() {
        if (this.confirm_password !== this.new_password) {
          this.$toast.error(this.$i18n.t('general.error.invalid_password_confirm'));
          return;
        }

        axios.post('/api/user/changePassword', {
          old: this.old_password,
          new: this.new_password
        }).then(() => this.$toast.success(this.$i18n.t('general.profile.password_changed'))).catch(() => {
          this.$toast.error(this.$i18n.t('general.error.invalid_password_confirm'));
        });
      },
      updateEmail() {
        axios.post('/api/user/updateEmail', {
          email: this.email
        }).then(() => {
          this.$toast.success(this.$i18n.t('bonus.promo.success'));
        }).catch(() => {
          this.$toast.error(this.$i18n.t('general.profile.invalid_email'));
        });
      },
      loadTab() {
        setTimeout(() => {
          if (this.tab === 'security') if ($('#qrcanvas').length > 0) qr.toCanvas($('#qrcanvas')[0], this.profile.qr);

          if (this.tab === 'info') {
            this.gamesPage = 0;
            this.loadGames();

            _.forEach(this.currencies, (currency) => {
              if ($(`#profit-${currency.name}`).length === 0) return;
              const chart = new ApexCharts(document.querySelector(`#profit-${currency.name}`), {
                series: [
                  {
                    name: '',
                    data: []
                  }
                ],
                noData: {
                  text: this.$i18n.t('general.profile.chart.loading')
                },
                chart: {
                  zoom: {
                    enabled: false
                  },
                  toolbar: {
                    show: false
                  },
                  type: 'area',
                  height: 100
                },
                dataLabels: {
                  enabled: false
                },
                stroke: {
                  colors: [this.theme === 'dark' ? '#ec487f' : '#1652f0']
                },
                xaxis: {
                  type: 'numeric',
                  axisBorder: {
                    show: false
                  },
                  axisTicks: {
                    show: false
                  },
                  labels: {
                    show: false
                  },
                  tooltip: {
                    enabled: false
                  }
                },
                yaxis: {
                  tickAmount: 1,
                  floating: false,
                  decimalsInFloat: 8,

                  labels: {
                    show: false
                  },
                  axisBorder: {
                    show: false,
                  },
                  axisTicks: {
                    show: false
                  }
                },
                fill: {
                  type: 'gradient',
                  colors: [this.theme === 'dark' ? '#ec487f' : '#1652f0'],
                  gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                  }
                },
                tooltip: {
                  x: {
                    show: false,
                  },
                  fixed: {
                    enabled: false,
                    position: 'topRight'
                  }
                },
                grid: {
                  borderColor: 'transparent',
                  yaxis: {
                    lines: {
                      offsetX: -30
                    }
                  },
                  padding: {
                    left: -10,
                    right: 0
                  }
                }
              });
              chart.render();

              const load = (interval) => {
                chart.updateSeries([{
                  name: this.$i18n.t('general.profit'),
                  data: []
                }]);

                axios.post('/api/user/graph', {
                  user: this.profile.user._id,
                  interval: interval,
                  currency: currency.id
                }).then(({data}) => {
                  chart.updateSeries([{
                    name: this.$i18n.t('general.profit'),
                    data: data
                  }]);
                });
              }

              load('today');

              $(`[data-switcher-currency="${currency.id}"]`).on('click', function () {
                if ($(this).hasClass('active')) return;

                $(this).parent().find('.switcher').removeClass('active');
                $(this).addClass('active');

                load($(this).data('interval') === 'today' ? 'today' : parseInt($(this).data('interval')));
              });
            });
          }
        }, 100);
      },
      enable2FA() {
        this.tfa_loading = true;
        axios.post('/api/user/2fa_enable', {
          '2facode': this.profile.secret,
          '2faucode': this.tfa_code
        }).then(() => {
          window.location.hash = 'security';
          window.location.reload();
        }).catch(() => {
          this.tfa_loading = false;
          this.$toast.error(this.$i18n.t('general.profile.error_2fa'));
        });
      },
      disable2FA() {
        axios.post('/api/user/2fa_disable').then(() => {
          window.location.hash = 'security';
          window.location.reload();
        });
      },
      togglePrivateProfile() {
        axios.get('/api/settings/privacy_toggle');
      },
      togglePrivateBets() {
        axios.get('/api/settings/privacy_bets_toggle');
      },
      openClientSeedModal() {
        ChangeClientSeedModal.methods.open();
      },
      uploadAvatar() {
        if (!this.avatarInit) {
          $('[data-file]').on('change', () => {
            const data = new FormData();
            data.append('image', $('[data-file]')[0].files[0]);

            axios.post('/api/settings/avatar', data).then(() => this.$router.go()).catch(() => this.$toast.error('Unknown error'));
          });

          this.avatarInit = true;
        }

        $('[data-file]').click();
      }
    }
  }
</script>

<style lang="scss">
  @import 'resources/sass/variables';

  #qrcanvas {
    margin: auto;
  }

  .toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;

    .switch {
      margin-left: auto;
    }
  }

  .profile-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 15px;
    margin-bottom: 55px;
  }

  .theme--dark {
    [data-incognito-dark] {
      display: none;
    }

    [data-incognito-white] {
      display: block;
    }
  }

  .theme--default {
    [data-incognito-dark] {
      display: block;
    }

    [data-incognito-white] {
      display: none;
    }
  }

  .tab_content {
    min-height: 250px;

    .row {
      justify-content: center;
    }

    .form-check {
      height: 40px;
      display: flex;
      align-items: center;

      .profileToggle {
        margin: auto;
        margin-right: 0;
      }
    }

    .settingsNotify {
      @include themed() {
        padding: 15px;
        border: 1px solid t('secondary');
        border-radius: 3px;
        text-align: center;
        position: relative;

        .settingsNotifyLoading .loaderContainer {
          z-index: 100;
          @include blur(t('sidebar'), 0.6, 0.9, 20px);
          position: absolute;
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
          display: flex;

          .loader {
            margin: auto;
          }
        }

        .btn {
          text-transform: uppercase;
          font-weight: 600;
          margin-top: 5px;
        }
      }
    }

    .avatar-settings {
      img {
        width: 64px;
        height: 64px;
      }

      button {
        margin-left: 15px;
      }
    }

    .link-group {
      height: 40px;
      display: flex;
      align-items: center;
      position: relative;

      span {
        position: absolute;
        right: 0;
      }
    }
  }

  $profile-padding: 30px;

  .input-profile-group {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-bottom: 10px;
    position: relative;

    input {
      margin-left: auto;
      width: 70% !important;
    }

    button {
      position: absolute;
      right: 0;
    }
  }

  .profile-content {
    position: relative;
    min-height: 400px;
  }

  .profile-container {
    @include themed() {
      .user-profit {
        border: 1px solid t('border');
        border-radius: 3px;
        padding: $profile-padding;
        margin: 10px;
        cursor: pointer;
        position: relative;

        .chart-switcher {
          position: absolute;
          top: 20px;
          right: 17px;
          display: flex;
          flex-direction: row;
          z-index: 50;

          .switcher {
            color: t('link');
            transition: color 0.3s ease;
            cursor: pointer;
            margin-right: 10px;

            &:hover {
              color: t('secondary');
            }
          }

          .active {
            color: t('secondary');
          }
        }

        &:nth-child(1), &:nth-child(2), &:nth-child(3) {
          margin-top: 0;
        }

        svg, i {
          font-size: 2.35em;
          margin-bottom: 15px;
        }

        .name {
          font-size: 1.2em;
        }

        .chart {
          color: black !important;
          position: absolute;
          right: 27px;
          top: 45px;
          width: 55% !important;

          svg {
            margin-bottom: 0;
          }
        }
      }

      .profile-header {
        padding: 25px $profile-padding;
        border-bottom: 1px solid t('border');
        margin-left: -$profile-padding;
        margin-top: -$profile-padding;
        margin-bottom: $profile-padding;
        width: calc(100% + #{$profile-padding * 2});
        display: flex;
        flex-direction: row;
        font-size: 1.1em;
        align-items: center;

        .profile {
          margin-right: 20px;
          display: flex;
          cursor: pointer;
          flex-direction: row;
          align-items: center;

          .avatar {
            margin-right: 10px;

            img {
              width: 32px;
              height: 32px;
              border-radius: 50%;
            }
          }
        }

        .profileMenuContainer, .profileMenu .os-host {
          width: 0 !important;
          flex: 1;
        }

        .profileMenu {
          display: flex;
          flex-direction: row;
          align-items: center;

          .os-content {
            display: flex;
            flex-direction: row;
            align-items: center;
          }

          .tab {
            color: t('link');
            transition: color 0.3s ease;
            margin-right: 20px;
            cursor: pointer;
            white-space: nowrap;

            &:last-child {
              margin-right: 0;
            }

            &:hover {
              color: t('secondary');
            }
          }

          .tab.active {
            color: t('secondary');
          }
        }
      }

      box-shadow: t('shadow');
      border: 1px solid t('border');
      background: t('modal');
      height: 100%;
      padding: $profile-padding;
      min-height: 300px;

      input {
        background: t('modal_input');
      }

      .cat {
        font-size: 22px;
        padding-bottom: 5px;
        font-weight: 600;
        border-bottom: 1px solid t('border');
        margin-bottom: 15px;
        margin-top: 15px;
      }

      .incognito {
        position: absolute;
        height: 100%;
        z-index: 20;
        background: t('sidebar');
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;

        i {
          font-size: 6em;
        }

        img {
          opacity: 0.05;
        }

        .incognito-desc {
          margin-top: 25px;
          text-align: center;
          font-size: 1.1em;
        }
      }

      .profile-highlight-no-top-margin {
        margin-top: 0 !important;
      }

      .profile-highlight {
        border-radius: 3px;
        border: 1px solid rgba(t('text'), 0.2);
        padding: 20px;
        display: flex;
        flex-direction: column;
        text-align: center;
        justify-content: center;
        align-items: center;
        margin: 5px;

        div:first-child {
          color: t('link');
          font-size: 1em;
        }

        div:last-child {
          font-weight: 600;
          font-size: 1.05em;
        }

        .overview {
          opacity: 0.7;
          cursor: pointer;
          color: t('link');
          transition: opacity 0.3s ease;

          &:hover {
            opacity: 1;
          }
        }
      }
    }
  }

  @media (max-width: 1600px) {
    .profile-container .user-profit .chart {
      @include themed() {
        position: unset !important;
        top: unset;
        right: unset;
        width: 100% !important;
        margin-bottom: -30px;
      }
    }

    .chart-switcher {
      flex-wrap: wrap;
      justify-content: center;
      line-height: 25px;
      align-items: center;
      width: 50%;
    }
  }
</style>
