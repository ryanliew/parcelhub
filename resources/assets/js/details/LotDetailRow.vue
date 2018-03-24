<template>
    <div class="row-detail" @click="onClick">
        <div class="has-text-centered" v-if="rowData.products.length > 0">
            <p class="heading">The following product can be found in this lot</p>
        </div>
        <div class="level" v-for="(product, index) in rowData.products">
            <div class="level-left">
                <figure class="level-item">
                    <p class="image is-48x48">
                        <img :src="product.picture">
                    </p>
                </figure>
                <div class="level-item">
                    <text-input :defaultValue="product.name"
                                label="Name"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="product.pivot.total_quantity"
                                label="Quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="product.pivot.total_incoming_quantity"
                                label="Incoming quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
                <div class="level-item">
                    <text-input :defaultValue="product.pivot.total_outgoing_quantity"
                                label="Outgoing quantity"
                                :required="true"
                                type="text"
                                :editable="false">
                    </text-input>
                </div>
            </div> 
        </div>
        <div class="has-text-centered" v-if="rowData.products.length == 0">
            <p>No products in the lot</p>
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