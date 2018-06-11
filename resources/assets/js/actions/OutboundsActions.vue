<template>
    <div>
        <div class="field has-addons">
            <p class="control">
                <button class="button is-info" @click="itemAction('viewOutbound', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <span>View</span>
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
        }
    }
  }
</script>