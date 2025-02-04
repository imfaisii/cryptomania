<script>
    import Bus from '../../bus';

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'chatrule',
                    component: {
                        data() {
                            return {
                                // html: html
                            }
                        },
                        template: `
                            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                <div class="rule-title">Chat Rules</div>
                                <div class="rule-content">
                                    <p>1. No harassment against other users or Cryptomania Staff, including profanity, abuse, racism etc.</p>
                                    <p>2. No begging for tips, loans, rains</p>
                                    <p>3. No politics/religion discussion</p>
                                    <p>4. No sexual content</p>
                                    <p>5. No sharing of any personal information, including socials</p>
                                    <p>6. No spamming of emotes or excessive capital letters/repeated words</p>
                                    <p>7. No messages of advertising/trading/selling/buying or offering services, including bots/codes/scripts</p>
                                    <p>8. No suspicious behaviour that could be seen as potential scams</p>
                                    <p>9. No discussion of illegal activities</p>
                                    <p>10. No shortened URLs, please only submit the original URL</p>
                                    <p>11. No posting of Cryptomania codes</p>
                                    <p>12. Only use the designated language of the chatroom you are in</p>
                                </div>
                            </overlay-scrollbars>`
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.notification {
        max-width: 350px;

        .os-content {
            padding-top: 10px !important;
        }
    }

    .rule-title {
        font-size: 2rem;
        padding: 20px;
    }

    .rule-content {
        p {
            margin-bottom: 0.7rem;
        }
    }
</style>
