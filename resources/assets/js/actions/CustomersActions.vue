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