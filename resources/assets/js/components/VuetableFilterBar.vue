<template>
    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <div class="field has-addons">
                    <div class="control has-icons-left has-icons-right">
                        <input class="input" type="text" placeholder="Search" v-model="filterText" @keyup.enter="doFilter">
                        <span class="icon is-small is-left">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <div class="control">
                        <button class="button is-link" @click="resetFilter">Reset</button>
                    </div>
                </div>
            </div>
        </div>     
    </div>
</template>

<script>
    export default {
        data () {
            return {
                filterText: ''
            }
        },

        methods: {
            doFilter () {
                this.$events.fire('filter-set', this.filterText);
            },
            resetFilter () {
                this.filterText = ''
                this.$events.fire('filter-reset');
            }
        }
    }
</script>