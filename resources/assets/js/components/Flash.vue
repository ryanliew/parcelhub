<template>
    <div></div>
</template>
<script>
    export default {
        props: ['message'],

        data() {
            return {
                body: this.message,
                level: 'success'
            }
        },

        created() {
            if (this.message) {
                this.flash();                
            }

            window.events.$on('flash', data => {
                this.flash(data);
            });
        },

        methods: {
            flash(data) {
                if(data) {
                    this.body = data.message;
                    this.level = data.level;
                }

                switch(this.level)
                {
                    case 'success': this.$noty.success(this.body); break;
                    case 'warning': this.$noty.warning(this.body); break;
                    case 'info': this.$noty.info(this.body); break;
                    case 'danger': this.$noty.error(this.body); break;
                }
            },
        }
    };
</script>