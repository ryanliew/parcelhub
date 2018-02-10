<template>
    <div>
        <div class="columns is-gapless">
            <div class="column">
                <button class="button is-primary" @click="itemAction('edit', rowData, rowIndex)">
                    <span class="icon">
                        <i class="fa fa-edit"></i>
                    </span>
                    <span>Edit</span>
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
            this.$events.fire('edit', data);
            /*let location = "";

            if(action !== 'view') location = "/transactions/create?type=" + action + "&unit=" + data.id;
            else location = "/unit/" + data.id;
            
            window.location.href = location;*/
            
        }
    }
  }
</script>