<template>
    <div>
        <div class="columns is-gapless">
            <div class="column">
                <button class="button is-info" @click="itemAction('viewPayment', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <span>View</span>
                </button>
            </div>
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
        itemAction(action, data, index){
            this.$events.fire(action, data);            
        }
    }
  }
</script>