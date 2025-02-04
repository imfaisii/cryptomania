<script>
import Bus from '../../bus';
import { mapGetters } from 'vuex';

export default {
    methods: {
        open() {
            Bus.$emit('modal:new', {
                name: 'tip',
                component: {
                    data() {
                        return {
                            to: '',
                            amount: 0,
                            public: true,
                            disabled: false
                        }
                    },
                    computed: {
                        ...mapGetters(['channel'])
                    },
                    methods: {
                        tip() {
                            // Validate the amount before making the API call
                            const parsedAmount = parseFloat(this.amount);
                            if (isNaN(parsedAmount) || parsedAmount <= 0) {
                                this.$toast.error(this.$i18n.t('general.chat_commands.modal.tip.invalid_amount'));
                                return;
                            }

                            this.disabled = true;

                            axios.post('/api/chat/tip', {
                                amount: parsedAmount,
                                user: this.to.trim(),
                                public: this.public,
                                channel: this.channel
                            }).then(() => {
                                Bus.$emit('modal:close');
                                this.$toast.success(this.$i18n.t('general.chat_commands.modal.tip.success', { name: this.to, value: this.amount }));
                            }).catch((error) => {
                                const code = error.response?.data?.code;
                                if (code === 1) {
                                    this.$toast.error(this.$i18n.t('general.chat_commands.modal.tip.invalid_name'));
                                } else if (code === 2) {
                                    this.$toast.error(this.$i18n.t('general.chat_commands.modal.tip.invalid_amount'));
                                } else if (code === 3) {
                                    this.$toast.error('/tip is temporarily disabled');
                                } else {
                                    this.$toast.error(this.$i18n.t('general.error.unexpected_error'));
                                }
                                this.disabled = false;
                            });
                        }
                    },
                    template: `
                        <div class="tip-modal">
                            <div class="cc_label">{{ $t('general.chat_commands.modal.tip.user') }}</div>
                            <input v-model="to" type="text" placeholder="Enter username">

                            <div class="cc_label">{{ $t('general.chat_commands.modal.tip.amount') }}</div>
                            <input v-model="amount" type="number" min="0" step="0.01" placeholder="Enter amount">

                            <div class="custom-control custom-checkbox mt-2">
                                <label>
                                    <input type="checkbox" class="custom-control-input" v-model="public">
                                    <div class="custom-control-label">{{ $t('general.chat_commands.modal.tip.make_public') }}</div>
                                </label>
                            </div>

                            <button class="btn btn-primary" @click="tip" :disabled="disabled">{{ $t('general.chat_commands.modal.tip.send') }}</button>
                        </div>`
                }
            });
        }
    }
}
</script>

<style lang="scss">
@import "resources/sass/variables";

.xmodal.tip {
    max-width: 250px;
    padding-top: 20px !important;

    .cc_label {
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .tip-modal {
        display: flex;
        flex-direction: column;
        
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border: 1px solid $border-color;
            border-radius: 4px;
        }

        .custom-control-label {
            padding-left: 5px;
        }

        .btn {
            align-self: flex-end;
        }
    }
}
</style>
