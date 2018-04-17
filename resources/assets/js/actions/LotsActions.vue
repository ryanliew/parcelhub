<template>
    <div>
        <div class="field has-addons">
            <p class="control">
                <button class="button is-warning" @click="itemAction('edit', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-edit"></i>
                    </span>
                    <span>Edit</span>
                </button>
            </p>
            <p class="control" v-if="rowData.lot_status == 'approved'  || !rowData.user_id">
                <button class="button is-info" @click="itemAction('assign', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-user"></i>
                    </span>
                    <span>Reassign owner</span>
                </button>
            </p>
            <p class="control" v-if="rowData.user_id && rowData.lot_status == 'false'">
                <button class="button is-success" @click="itemAction('approve', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-user"></i>
                    </span>
                    <span>Approve owner</span>
                </button>
            </p>
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