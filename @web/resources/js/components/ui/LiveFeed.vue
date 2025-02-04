<template>
  <div class="container">
    <div class="live">
      <div class="header">
        <template v-if="isCasino">
          <div class="live_tabs">
            <div class="tabs">
              <div v-if="!isGuest" @click="$store.dispatch('setLiveChannel', 'mine')"
                   :class="`tab ${liveChannel === 'mine' ? 'active' : ''}`">{{ $t('general.bets.mine') }}
              </div>
              <div @click="$store.dispatch('setLiveChannel', 'all')"
                   :class="`tab ${liveChannel === 'all' ? 'active' : ''}`">{{ $t('general.bets.all') }}
              </div>
              <div @click="$store.dispatch('setLiveChannel', 'high_rollers')"
                   :class="`tab ${liveChannel === 'high_rollers' ? 'active' : ''}`">{{ $t('general.bets.high_rollers') }}
              </div>
              <div @click="$store.dispatch('setLiveChannel', 'lucky_wins')"
                   :class="`tab ${liveChannel === 'lucky_wins' ? 'active' : ''}`">{{ $t('general.bets.lucky_wins') }}
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="live_tabs">
            <div class="tabs">
              <div v-if="!isGuest" @click="$store.dispatch('setLiveChannel', 'mine')"
                   :class="`tab ${liveChannel === 'mine' ? 'active' : ''}`">{{ $t('general.bets.mine') }}
              </div>
              <div @click="$store.dispatch('setLiveChannel', 'all')"
                   :class="`tab ${liveChannel !== 'mine' ? 'active' : ''}`">{{ $t('general.bets.all') }}
              </div>
            </div>
            <select @change="$store.dispatch('setLiveFeedEntryCount', parseFloat(liveFeedEntriesWrap))"
                    v-model="liveFeedEntriesWrap">
              <option value="10" :selected="liveFeedEntries === 10">10</option>
              <option value="25" :selected="liveFeedEntries === 25">25</option>
              <option value="50" :selected="liveFeedEntries === 50">50</option>
            </select>
          </div>
        </template>
      </div>
      <template v-if="isCasino">
        <div class="live_table_container">
          <loader v-if="lastGames.length==0"></loader>
          <table class="live-table" v-else>
            <thead>
              <tr>
                <th>{{ $t('general.bets.player') }}</th>
                <th>{{ $t('general.bets.game') }}</th>
                <th class="d-none d-xl-table-cell">{{ $t('general.bets.bet') }}</th>
                <th class="d-none d-xl-table-cell">{{ $t('general.bets.mul') }}</th>
                <th class="payout-column">{{ $t('general.bets.win') }}</th>
              </tr>
            </thead>
            <tbody class="live_games">
              <tr v-for="game in lastGames" :key="game.game._id">
                <td>
                  <div>
                    <a :href="game.user.private_bets !== true || (isGuest ? false : !$checkPermission('ignore_privacy')) ? 'javascript:void(0)' : $route.path"
                       @click="game.user.private_bets !== true || (isGuest ? false : !$checkPermission('ignore_privacy')) ? openUserModal(game.user._id) : false">
                      <span v-if="game.user.private_bets && (isGuest ? true : user.user.access === 'user')">
                        <web-icon icon="fad fa-user-secret mr-1"></web-icon>
                        {{ $t('general.bets.hidden_name') }}
                      </span>
                      <span v-else>
                        <img :src="game.user.avatar" class="avatar" width="24" height="24"> <!-- Fixed width & height -->
                        {{ game.user.name }}
                      </span>
                    </a>
                  </div>
                </td>
                <td>
                  <div class="gameIcon">
                    <router-link :to="`/casino/game/${game.metadata.id}`" tag="div">
                      <img :src="gameIconSrc(game.metadata.category)" alt="game category" width="30" height="30">
                    </router-link>
                    <div class="name">
                      <router-link :to="`/casino/game/${game.metadata.id}`">{{ game.metadata.name }}</router-link>
                    </div>
                  </div>
                </td>
                <td class="d-none d-xl-table-cell clickable" @click="openOverviewModal(game.game._id, game.game.game)">
                  <div>
                    <web-icon :icon="currencies[game.game.currency].icon"
                              :style="{ color: currencies[game.game.currency].style }"></web-icon>
                    <unit :fiat="fiatView" :to="game.game.currency" :value="game.game.wager" class="unit-value"></unit>
                  </div>
                </td>
                <td class="d-none d-xl-table-cell clickable" @click="openOverviewModal(game.game._id, game.game.game)">
                  <div class="multiplier">
                    <img src="/img/increase.svg" class="increase-icon" width="15" height="15">
                    {{ (game.game.status === 'win' || game.game.multiplier < 1 ? game.game.multiplier : 0).toFixed(2) }}x
                  </div>
                </td>
                <td class="payout-column clickable" @click="openOverviewModal(game.game._id, game.game.game)">
                  <div :class="game.game.status === 'win' ? 'live-win' : ''">
                    <web-icon :icon="currencies[game.game.currency].icon"
                              :style="{ color: currencies[game.game.currency].style }"></web-icon>
                    <unit :fiat="fiatView" :to="game.game.currency" :value="game.game.profit" class="unit-value"></unit>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
      <template v-else>
        <div class="live_table_container">
          <loader v-if="lastGames.length==0"></loader>
          <table class="live-table" v-else>
            <thead>
              <tr>
                <th>{{ $t('general.bets.event') }}</th>
                <th>{{ $t('general.bets.player') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('general.bets.odds') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('general.bets.bet') }}</th>
              </tr>
            </thead>
            <tbody class="live_games">
              <tr v-for="game in lastGames" :key="game.game._id">
                <td>
                  <div class="gameIcon">
                    <router-link :to="`/sport/game/`" tag="div" class="icon d-none d-md-inline-block">
                      <web-icon :icon="game.game.icon"></web-icon>
                    </router-link>
                    <div class="name">
                      <router-link :to="`/sport/category/` + game.game.category">{{ game.game.game }}</router-link>
                      <router-link :to="`/sport/category/` + game.game.category">{{ game.game.categoryName }}</router-link>
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <a @click="game.user.private_bets !== true || (isGuest ? false : !$checkPermission('ignore_privacy')) ? openUserModal(game.user._id) : false"
                       :href="game.user.private_bets !== true || (isGuest ? false : !$checkPermission('ignore_privacy')) ? `javascript:void(0)` : $route.path">
                      <span v-if="game.user.private_bets && (isGuest ? true : !$checkPermission('ignore_privacy'))">
                        <web-icon icon="fad fa-user-secret mr-1"></web-icon>
                        {{ $t('general.bets.hidden_name') }}
                      </span>
                      <span v-else>{{ game.user.name }}</span>
                    </a>
                  </div>
                </td>
                <td class="d-none d-md-table-cell clickable" @click="openOverviewModal(game.game._id, game.game.game)">
                  <div>
                    x{{ game.game.odds.toFixed(2) }}
                  </div>
                </td>
                <td class="d-none d-md-table-cell clickable" @click="openOverviewModal(game.game._id, game.game.game)">
                  <div>
                    <unit :to="game.game.currency" :value="game.game.bet"></unit>
                    <web-icon :icon="currencies[game.game.currency].icon"
                              :style="{ color: currencies[game.game.currency].style }"></web-icon>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex';
import Bus from '../../bus';
import OverviewModal from '../modals/OverviewModal.vue';
import UserModal from "../modals/UserModal.vue";

export default {
  data() {
    return {
      lastGames: [],
      liveFeedEntriesWrap: 10,
      liveFeedEntries: 10,
      intervalId: null,
    }
  },
  watch: {
    liveChannel() {
      this.getGames();
    },
    liveFeedEntries() {
      this.getGames();
    },
    lastGames(newVal) {
      if (newVal.length >= this.liveFeedEntries) newVal.pop();
    },
    $route(to, from) {
      if ((to.path.startsWith("/sport") && !from.path.startsWith("/sport")) || (from.path.startsWith("/sport") && !to.path.startsWith("/sport"))) {
        this.getGames();
      }
    },
  },
  computed: {
    ...mapGetters(['liveFeedEntries', 'isGuest', 'liveChannel', 'user', 'fiatView', 'unit', 'currencies'])
  },
  async created() {
    await this.getGames();
    this.liveFeedEntriesWrap = this.liveFeedEntries;
    Bus.$on('event:liveGame', (e) => {
      if (!this.isCasino) return;
      if (this.liveChannel === 'mine' && e.user._id !== this.user.user._id) return;
      if (this.liveChannel === 'lucky_wins' && (e.game.multiplier < 10 || e.game.status !== 'win')) return;
      if (this.liveChannel === 'high_rollers' && e.game.wager < this.currencies[e.game.currency].highRollerRequirement) return;
      setTimeout(() => this.lastGames.unshift(e), e.delay);
    });
    Bus.$on('event:liveSportGame', (e) => {
      if (this.isCasino) return;
      if (this.liveChannel === 'mine' && e.user._id !== this.user.user._id) return;
      this.lastGames.unshift(e);
    });
  },
  beforeDestroy() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  },
  methods: {
    rotateLastGames() {
      this.intervalId = setInterval(() => {
        if (this.lastGames.length > 0 && this.liveChannel === 'all') {
          this.lastGames.unshift(this.lastGames.pop());
        }
      }, 1000);
    },
    openUserModal(id) {
      UserModal.methods.open(id);
    },
    async getGames() {
      this.lastGames = [];
      try {
        const {data} = await axios.post('/api/data/latestGames', {
          mode: this.isCasino ? 'casino' : 'sport',
          type: this.liveChannel,
          count: this.liveFeedEntries
        });
        this.lastGames = data.reverse();
      } catch (error) {
        console.error('Failed to fetch games:', error);
      }
    },
    openOverviewModal(id, game) {
      OverviewModal.methods.open(id, game);
    },
    gameIconSrc(category) {
      if (category.includes('originals')) {
        return '/icons/original.svg';
      } else if (category.includes('slots')) {
        return '/icons/slots.svg';
      } else {
        return '/icons/casino.svg';
      }
    },
    formatTime(time) {
      return new Date(time).toLocaleTimeString();
    }
  }
}
</script>

<style scoped>
.container {
  display: block;
  width: 100%;
  overflow-x: hidden;
  box-sizing: border-box;
}

.live-table {
  table-layout: fixed;
  width: 100%;
  border-collapse: collapse;
}

.live-table th,
.live-table td {
  padding: 10px;
  min-width: 80px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.avatar {
  margin-right: 5px;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
}

.gameIcon {
  display: flex;
  align-items: center;
}

.gameIcon img {
  width: 30px;
  height: 30px;
  margin-right: 5px;
}

.name {
  margin-left: 5px;
  white-space: nowrap;
}

.unit-value {
  margin-left: 5px;
}

.multiplier {
  display: flex;
  align-items: center;
}

.increase-icon {
  margin-right: 5px;
  width: 15px;
  height: 15px;
}

.live-win {
  font-weight: bold;
  color: green;
}

.payout-column {
  text-align: right;
}

.clickable {
  cursor: pointer;
}

.tab {
  padding: 8px 12px;
  transition: background-color 0.3s, color 0.3s;
}

.tab.active {
  background-color: #e0e0e0;
  font-weight: bold;
}

select {
  width: 60px;
  padding: 5px;
  font-size: 0.9em;
}
</style>
