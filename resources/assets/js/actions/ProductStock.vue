<template>
    <div>
        <div v-if="rowData.total_quantity > rowData.threshold">
            {{ rowData.total_quantity }}
        </div>
        <div class="tooltip is-danger is-tooltip-danger is-tooltip-bottom" :data-tooltip="tooltipText" v-else>
            {{ rowData.total_quantity }} <i class="fa fa-angle-double-down"></i>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        rowData: {
            type: Object,
            required: true
        },
        rowIndex: {
            type: Number,
        }
    },
    methods: {
        
    },

    computed: {
        tooltipText() {
            return "Low stock level";
        }
    }
  }
</script>