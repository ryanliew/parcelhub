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
                <button class="button is-danger" @click="itemAction('delete', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-times-circle"></i>
                    </span>
                    <span>Delete</span>
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