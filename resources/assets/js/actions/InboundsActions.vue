<template>
    <div>
        <div class="field has-addons">
            <p class="control">
                <button class="button is-info" @click="itemAction('viewInbound', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <span>View</span>
                </button>
            </p>
            <p class="control">
                <button class="button is-primary" @click="download">
                    <span class="icon">
                        <i class="fa fa-download"></i>
                    </span>
                    <span>GR Form</span>
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
            if(data.type !== 'inbound')
                action = 'viewReturn';

            this.$events.fire(action, data);            
        },

        download() {
            window.location.href = "/download/inbound/report/" + this.rowData.id;
        }
    }
  }
</script>