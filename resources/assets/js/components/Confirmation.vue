<template>
    <div>
        <modal :active="isConfirming" @close="close">
            <template slot="header">{{ title }}</template>
            
            {{ message }}

            <template slot="footer">
                <button class="button is-primary" :class="buttonClass" @click="confirm">Confirm</button>
            </template>
        </modal>
    </div>
</template>
<script>
    export default {
        props: ['title', 'message', 'isConfirming'],

        data() {
            return {
                isLoading: false
            }
        },

        methods: {
            close() {
                this.$emit('close');
            },

            confirm() {
                this.isLoading = true;
                this.$emit('confirm');
            }
        },

        computed: {
            buttonClass() {
                return this.isLoading ? "is-loading" : '';
            }
        },

        watch: {
            isConfirming: function(newValue, oldValue) {
                if(!oldValue)
                {
                    this.isLoading = false;
                }
            }
        }
    };
</script>