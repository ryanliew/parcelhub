<template>
    <div>
        <div class="field has-addons">
            <p class="control">
                <button class="button is-info" @click="itemAction('view', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <span>View</span>
                </button>
            </p>
            <p class="control" v-if="!rowData.is_approved && rowData.role_name !== 'admin'">
                <button class="button is-primary" @click="itemAction('approve', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-check"></i>
                    </span>
                    <span>Approve</span>
                </button>
            </p>
            <p class="control" v-if="rowData.is_approved && rowData.role_name !== 'admin'">
                <button class="button is-danger" @click="itemAction('approve', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-times"></i>
                    </span>
                    <span>Ban</span>
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

    mounted() {
        this.$events.on('complete', data => this.loadingComplete());
    },
    methods: {
        itemAction(action, data, index){
            if(action !== 'view') this.loading = true;
            this.$events.fire(action, data);            
        },

        loadingComplete() {
            this.loading = false;
        }
    }
  }
</script>