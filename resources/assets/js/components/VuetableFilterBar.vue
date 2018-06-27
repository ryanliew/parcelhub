<template>
    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <div class="field has-addons">
                    <div class="control has-icons-left has-icons-right">
                        <input class="input" type="text" placeholder="Search" v-model="filterText" @keyup.enter="doFilter">
                        <span class="icon is-small is-left">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                    <div class="control">
                        <button class="button is-link" @click="resetFilter">Reset</button>
                    </div>
                </div>
            </div>
            <div class="level-item" v-if="dateFilterable">
                <div class="field">
                    <input class="input" type="date" placeholder="Start date" v-model="filterDateStart">
                </div>
            </div>
            <div class="level-item" v-if="dateFilterable">
                -
            </div>
            <div class="level-item" v-if="dateFilterable">
                <div class="field">
                    <input class="input" type="date" placeholder="End date" v-model="filterDateEnd">
                </div>
            </div>
            <div class="level-item" v-if="dateFilterable">
                <button class="button is-link" @click="doFilter">Filter by date</button>
            </div>
        </div>     
    </div>
</template>

<script>
    export default {
        props: ['dateFilterable'],

        data () {
            return {
                filterText: '',
                filterDateStart: '',
                filterDateEnd: ''
            }
        },

        mounted() {
            this.$events.on("set-filter-text", data => this.setFilterText(data));
        },

        methods: {
            doFilter () {
                this.$events.fire('filter-set', { text: this.filterText, start: this.filterDateStart, end: this.filterDateEnd });
            },
            resetFilter () {
                this.filterText = '';
                this.filterDateStart = '';
                this.filterDateEnd = '';
                this.$events.fire('filter-reset');
            },

            setFilterText(data) {
                this.filterText = data.text;
                this.doFilter();
            }
        }
    }
</script>