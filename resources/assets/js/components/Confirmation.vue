<template>
    <div>
        <modal :active="isConfirming" @close="close">
            <template slot="header">{{ title }}</template>
            
            {{ message }}

            <template slot="footer">
                <button class="button is-primary" @click="confirm">Confirm</button>
            </template>
        </modal>
    </div>
</template>
<script>
    export default {
        props: ['title', 'message', 'isConfirming'],

        methods: {
            close() {
                this.$emit('close');
            },

            confirm() {
                this.$emit('confirm');
            }
        }
    };
</script>