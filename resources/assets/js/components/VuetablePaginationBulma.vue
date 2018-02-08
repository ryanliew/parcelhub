<template>
    <nav class="pagination" role="navigation">
        <a v-if="!isOnFirstPage" class="pagination-previous" @click.prevent="loadPage('prev')">
            <span>&laquo;</span>
        </a>
        <ul class="pagination-list">
            <template v-if="notEnoughPages">
                <li v-for="n in totalPage">
                    <a class="pagination-link" :class="{'is-current': isCurrentPage(n)}" @click.prevent="loadPage(n)" v-html="n"></a>
                </li>
            </template>
            <template v-else>
                <li v-for="n in windowSize">
                    <a class="pagination-link" :class="{'is-current': isCurrentPage(windowStart+n-1)}" @click.prevent="loadPage(windowStart+n-1)" v-html="windowStart+n-1"></a>
                </li>
            </template>
        </ul>

        <a class="pagination-next" v-if="!isOnLastPage" @click.prevent="loadPage('next')">
            <span>&raquo;</span>
        </a>

    </nav>
</template>

<script>
import VuetablePaginationMixin from 'vuetable-2/src/components/VuetablePaginationMixin'
export default {
  mixins: [VuetablePaginationMixin]
}
</script>