<template>
  <div class="ota animate" v-if="version">
    <div class="type">
      <div class="c1">
        <div class="smTitle">Version Information</div>
        <div class="currentInfo">
          <div class="title">Your server is up to date.</div>
          <div class="description">See the current software versions below.</div>
        </div>
      </div>
      <div class="c2">
        <div class="smTitle">Current Versions</div>
        <div class="versions">
          <div class="version">
            <div>Vortex Holdings LLC</div>
            <div>{{ version.phoenix }}</div>
          </div>
          <div class="separated"></div>
          <div class="version">
            <div>PHP</div>
            <div>{{ version.php }}</div>
          </div>
          <div class="version">
            <div>Laravel</div>
            <div>{{ version.laravel }}</div>
          </div>
          <div class="version">
            <div>Vue.js | BackEnd</div>
            <div>{{ version.vueAdmin }}</div>
          </div>
          <div class="version">
            <div>Vue.js | FrontEnd</div>
            <div>{{ version.vue }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        version: null
      };
    },
    created() {
      window.axios.get('/api/version').then(({ data }) => {
        this.version = data.version;
      });
    }
  };
</script>

<style lang="scss" scoped>
  @import "resources/sass/container";
  @import "resources/sass/themes";

  .ota {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px); /* Adds a subtle blur effect for better readability */
  }

  .smTitle {
    font-size: 1.2em;
    font-weight: 500;
    color: #333;
    margin-bottom: 15px;
  }

  .type {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 30px;

    @include min(0, bp('md')) {
      flex-direction: column;
    }

    .c1 {
      flex: 1;
      min-width: 200px;
      .currentInfo {
        .title {
          font-size: 1.3em;
          color: #007bff;
          font-weight: bold;
          margin-bottom: 8px;
        }

        .description {
          font-size: 1em;
          color: #666;
        }
      }
    }

    .c2 {
      flex: 2;
      .versions {
        .separated {
          width: 100%;
          height: 2px;
          margin: 10px 0;
          background: #ccc;
        }

        .version {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 10px 0;
          border-bottom: 1px solid #e0e0e0;

          &:last-child {
            border-bottom: none;
          }

          div {
            flex: 1;
          }

          div:first-child {
            font-weight: 600;
            color: #555;
          }

          div:last-child {
            text-align: right;
            color: #007bff;
          }
        }
      }
    }
  }
</style>
