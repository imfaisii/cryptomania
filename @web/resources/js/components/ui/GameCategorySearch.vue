<template>
  <div class="c-search">
    <div v-if="IsHome" class="tabview">
      <div class="tabview-container">
        <button class="tabview-button tabview-button-hasIcon" :class="classNameLobby" data-testid="LOBBY" id="LOBBY" @click="openLobbyTab">
          <span class="tabview-button-icon">
            <img src="/icons/home.svg" alt="home" color="currentColor">
          </span>
          <label>Lobby</label>
        </button>
        <button class="tabview-button tabview-button-hasIcon" :class="classNameOriginals" data-testid="ORIGINALS" id="ORIGINALS" @click="openOriginalsTab">
          <span class="tabview-button-icon">
            <img src="/icons/original.svg" alt="ORIGINALS">
          </span>
          <label>Originals</label>
        </button>
        <button class="tabview-button tabview-button-hasIcon" :class="classNameSlots" data-testid="SLOTS" id="SLOTS" @click="openSlotsTab">
          <span class="tabview-button-icon">
            <img src="/icons/slots.svg" alt="SLOTS">
          </span>
          <label>Slots</label>
        </button>
        <button class="tabview-button tabview-button-hasIcon" :class="classNameLive" data-testid="LIVE_CASINO" id="LIVE_CASINO" @click="openLiveTab">
          <span class="tabview-button-icon">
            <img src="/icons/casino.svg" alt="LIVE_CASINO">
          </span>
          <label>Live Casino</label>
        </button>
      </div>
    </div>
    
    <div class="search-input" @click="openSearch">
      <web-icon icon="fal fa-search"></web-icon>
      <input :placeholder="$t('general.search')">
    </div>
  </div>
</template>

<script>
  import Bus from "../../bus";
  import WebIcon from "../ui/WebIcon.vue";

  export default {
    data() {
      return {
        classNameLobby: 'tabview-button-active',
        classNameOriginals: '',
        classNameSlots: '',
        classNameLive: '',
      }
    },
    props: {
      IsHome: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      openSearch() {
        Bus.$emit('search:toggle');
      },
      openLobbyTab() {
        // console.log('lobbbby-----');
        Bus.$emit('tab:lobby');
        this.classNameLobby = 'tabview-button-active';
        this.classNameOriginals = '';
        this.classNameSlots = '';
        this.classNameLive = '';
      },
      openOriginalsTab() {
        Bus.$emit('tab:originals');
        this.classNameLobby = '';
        this.classNameOriginals = 'tabview-button-active';
        this.classNameSlots = '';
        this.classNameLive = '';
      },
      openSlotsTab() {
        Bus.$emit('tab:slots');
        this.classNameLobby = '';
        this.classNameOriginals = '';
        this.classNameSlots = 'tabview-button-active';
        this.classNameLive = '';
      },
      openLiveTab() {
        Bus.$emit('tab:live');
        this.classNameLobby = '';
        this.classNameOriginals = '';
        this.classNameSlots = '';
        this.classNameLive = 'tabview-button-active';
      }
    },
    components: {
      WebIcon
    }
  }
</script>

<style lang="scss" scoped>
  @import "resources/sass/themes";

  .c-search {
    display: flex;
    width: 100%;
    gap: 1rem;
    margin-top: 20px;

    @include themed() {
      display: flex;
      align-items: center;

      @media (max-width: 991px) {
        background: transparent;
        flex-direction: column;
        align-items: unset;
        padding: unset;
        margin-top: 20px;

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
      cursor: pointer;
      width: 100%;

      input {
        padding-left: 50px;
        pointer-events: none;
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

    .tabview {
      @media (max-width: 992px) {
        // width: unset;
        width: calc(100vw - 24px);
      }

      display: flex;
      background-color: #242e42;
      border-radius: .375rem;
      padding: .375rem .375rem 0;
      gap: .2rem;
      overflow: hidden;
      overflow-x: auto;
      width: 100%;
    }

    .tabview-container {
      display: flex;
      overflow: auto;
      overflow-y: hidden;
      padding-bottom: .375rem;
      width: 100%;
      gap: .2rem;
    }

    .tabview-button {
      transition: 0.2s all;
      border: 0;
      position: relative;
      flex: 1 1;
      font-weight: 500;
      line-height: 1.625rem;
      border-radius: .375rem;
      height: 2.25rem;
      padding: .469rem 1rem;
      cursor: pointer;
      color: #bec6d1;
      font-size: 0.875rem;
      background-color: #0000;
      pointer-events: inherit;
      -webkit-user-select: inherit;
      user-select: inherit;
      opacity: 1;

      display: flex;
      justify-content: center;
      align-items: center;

      .tabview-button-icon {
        margin-right: .5rem;
        width: 1rem;
        height: 1rem;
        display: flex;
      }

      label {
        white-space: nowrap;
        cursor: inherit;
        line-height: 0;
      }
    }

    .tabview-button-active {
      color: #fff;
      background-color: #19202e;
      cursor: default;
    }
    .tabview-button-active:hover {
      color: #fff;
      background-color: #19202e !important;
      cursor: default;
    }

    // .tabview-button:active {
    //   color: #fff;
    //   background-color: #37206e;
    // }
    .tabview-button:not(.tabview-button-active):active {
      background: #4700ab;
      color: #fff;
      scale: .95;
    }
    .tabview-button:hover {
      color: #fff;
      background-color: #5d3db3;
    }

    .tabview-button-hasIcon {
      flex-direction: row;
    }
  }
</style>
