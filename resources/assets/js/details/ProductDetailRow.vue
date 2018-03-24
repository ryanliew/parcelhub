<template>
    <div class="row-detail" @click="onClick">
        <div class="has-text-centered" v-if="rowData.lots.length > 0">
            <p class="heading">This product can be found in the following lots</p>
        </div>
        <div class="level" v-for="(lot, index) in rowData.lots">
            <div class="level-left">
                <div class="level-item">
                    <text-input :defaultValue="lot.name"
                                label="Name"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="lot.pivot.total_quantity"
                                label="Quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="lot.pivot.total_incoming_quantity"
                                label="Incoming quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="lot.pivot.total_outgoing_quantity"
                                label="Outgoing quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
            </div>
            
        </div>
        <div class="has-text-centered" v-if="rowData.lots.length == 0">
            <p class="heading">Not in any lots yet</p>
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
                type: Number
            }
        },

        methods: {
            onClick (event) {
                console.log('my-detail-row: on-click', event.target)
            }
        },
    }
</script>