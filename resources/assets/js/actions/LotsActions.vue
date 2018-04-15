<template>
    <div>
        <div class="columns is-gapless">
            <div class="column">
                <button class="button is-warning" @click="itemAction('edit', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-edit"></i>
                    </span>
                    <span>Edit</span>
                </button>
            </div>
            <div class="column" v-if="rowData.lot_status == 'approved'  || !rowData.user_id">
                <button class="button is-info" @click="itemAction('assign', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-user"></i>
                    </span>
                    <span>Reassign owner</span>
                </button>
            </div>
            <div class="column" v-if="rowData.user_id && rowData.lot_status == 'false'">
                <button class="button is-success" @click="itemAction('approve', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-user"></i>
                    </span>
                    <span>Approve owner</span>
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