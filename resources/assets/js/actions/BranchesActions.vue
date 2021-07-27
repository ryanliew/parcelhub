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
             <p class="control">
                <button class="button is-info" @click="itemAction('access', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-unlock"></i>
                    </span>
                    <span>Access</span>
                </button>
            </p>
            <!-- <p class="control">
                <button class="button is-danger" @click="itemAction('delete', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-times-circle"></i>
                    </span>
                    <span>Delete</span>
                </button>
            </p> -->
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