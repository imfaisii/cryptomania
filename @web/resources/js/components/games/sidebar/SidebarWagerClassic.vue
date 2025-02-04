<template>
    <div class="wager-classic wager-selector">
        <input
            type="text"
            v-model.lazy.number="bet"
            :placeholder="$t('general.wager')"
            :disabled="gameInstance.game && gameInstance.game.extendedState === 'in-progress'"
        />
        <div class="wager-input-controls">
            <div class="control" @click="adjustBet(bet / 2)"><i class="fas fa-slash"></i></div>
            <div class="control" @click="adjustBet(bet * 2)"><i class="fas fa-asterisk"></i></div>
        </div>
        <div class="wager-controls">
            <div class="control" @click="adjustBet(bet + (currency.startsWith('local_') ? 0.10 : 0.001))">
                +{{ currency.startsWith('local_') ? "100K" : 0.001 }}
            </div>
            <div class="control" @click="adjustBet(bet + (currency.startsWith('local_') ? 1.00 : 0.10))">
                +{{ currency.startsWith('local_') ? "1M" : 0.10 }}
            </div>
            <div class="control" @click="adjustBet(bet + (currency.startsWith('local_') ? 10.00 : 0.25))">
                +{{ currency.startsWith('local_') ? "10M" : 0.25 }}
            </div>
            <div class="control" @click="adjustBet(bet + (currency.startsWith('local_') ? 50.00 : 0.50))">
                +{{ currency.startsWith('local_') ? "50M" : 0.50 }}
            </div>
            <div class="control" @click="adjustBet(bet + (currency.startsWith('local_') ? 100.00 : 1.00))">
                +{{ currency.startsWith('local_') ? "100M" : 1.00 }}
            </div>
        </div>
    </div>
</template>

<script>
    import Bus from '../../../bus';
    import { mapGetters } from 'vuex';

    export default {
        props: {
            data: {
                type: Object
            }
        },
        data() {
            return {
                bet: 0.000
            };
        },
        computed: {
            ...mapGetters(['isGuest', 'gameInstance', 'currency', 'currencies', 'fiatView', 'demo']),

            // Get the max balance based on mode (real/demo) and fiat view
            maxBalance() {
                const balanceType = this.demo ? 'demo' : 'real';
                const balance = this.currencies[this.currency]?.balance?.[balanceType] || 0;
                return this.fiatView ? this.tokenToFiat(this.currencies[this.currency].price, balance) : balance;
            }
        },
        watch: {
            bet() {
                // Dispatch the updated bet to Vuex and emit update event
                const instance = this.gameInstance;
                instance.bet = parseFloat(this.bet);
                this.$store.dispatch('setGameInstance', instance);

                Bus.$emit('sidebar:update', { type: 'bet', value: this.bet });
            },
            currency() {
                this.setPrecision();
            },
            fiatView() {
                // Adjust bet display to fiat or crypto when fiatView changes
                this.bet = this.fiatView
                    ? this.tokenToFiat(this.currencies[this.currency].price, this.bet)
                    : this.fiatToToken(this.currencies[this.currency].price, this.bet);
            },
            demo() {
                // Adjust bet to respect the current mode's max balance
                this.bet = Math.min(this.bet, this.maxBalance);
            }
        },
        methods: {
            setPrecision() {
                this.bet = this.currency.startsWith('local_') ? 0.00 : 0.000;
            },

            // Method to adjust bet normally until it reaches max balance
            adjustBet(newBet) {
                if (this.gameInstance.game && this.gameInstance.game.extendedState === 'in-progress') return;

                // Calculate the new bet amount without capping
                const adjustedBet = parseFloat(newBet.toFixed(this.currency.startsWith('local_') ? 2 : 3));

                // Set bet to maxBalance only if adjustedBet exceeds maxBalance
                this.bet = adjustedBet > this.maxBalance ? this.maxBalance : adjustedBet;
            },

            // Convert token value to fiat using the current price
            tokenToFiat(price, value) {
                return (value * price).toFixed(2);
            },

            // Convert fiat value back to token using the current price
            fiatToToken(price, value) {
                return (value / price).toFixed(this.currency.startsWith('local_') ? 2 : 3);
            }
        },
        mounted() {
            this.setPrecision();

            Bus.$on('sidebar:updateBet', (e) => {
                this.bet = parseFloat(e.bet.toFixed(this.currency.startsWith('local_') ? 2 : 3));
            });
        }
    };
</script>
