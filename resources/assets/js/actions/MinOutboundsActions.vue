<template>
    <div>
        <div class="field has-addons">
            <p class="control">
                <button class="button is-info" @click="itemAction('viewOutbound', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-search"></i>
                    </span>
                </button>
            </p>
            <p class="control">
                <button class="button is-primary" @click="download">
                    <span class="icon">
                        <i class="fa fa-download"></i>
                    </span>
                </button>
            </p>
            <p class="control">
                <button class="button is-danger" @click="downloadInvoice" v-if="!rowData.invoice_slip">
                    <span class="icon">
                        <i class="fa fa-download"></i>
                    </span>
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
            if(data.type !== 'outbound')
                action = 'viewOutbound';
            
            this.$events.fire(action, data);            
        },

        download() {
            window.location.href = "/download/outbound/packingList/" + this.rowData.id;
        },

        downloadInvoice() {
            return "/download/outbound/report/" + this.outbound.id;
        }
    }
  }
</script>