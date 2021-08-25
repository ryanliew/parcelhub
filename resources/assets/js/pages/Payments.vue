<template>
  <div>
    <transition name="slide-fade">
      <div class="card" v-if="!isPurchasing">
        <div class="card-header">
          <div class="card-header-title level">
            <div class="level-left">
              <div class="level-item">
                {{ mainTitle }}
              </div>
            </div>
            <div class="level-right" v-if="!can_manage">
              <div class="level-item">
                <button class="button is-primary is-tooltip-danger is-tooltip-left" :class="tooltipClass"
                        :disabled="!canPurchase" @click="modalOpen" data-tooltip="Sold out temporarily">
                  <i class="fa fa-plus-circle"></i>
                  <span class="pl-5">Purchase lots</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-content">
          <table-view ref="payments"
                      :fields="fields"
                      url="/internal/payments"
                      :searchables="searchable"
                      :dateFilterable="true"
                      dateFilterKey="payments.created_at">
          </table-view>
        </div>
      </div>
    </transition>
    <transition name="slide-fade">
      <div class="card" v-if="isPurchasing">
        <div class="card-header">
          <div class="card-header-title level">
            <div class="level-left">
              <div class="level-item">
                Purchase lots
              </div>
            </div>
            <div class="level-right">
              <div class="level-item">
                <button class="button is-warning" @click="back()">
                  <i class="fa fa-arrow-circle-left"></i>
                  <span class="pl-5">Cancel</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-content">
          <form @submit.prevent="submit"
                @keydown="form.errors.clear($event.target.name)"
                @input="form.errors.clear($event.target.name)"
                @keyup.enter="submit"
                v-if="this.selectableLots.length > 0">

            <div class="level">
              <div class="level-item has-text-centered">
                <div>
                  <p class="heading">
                    Lots count
                  </p>
                  <p class="title">
                    {{ totalLots }}
                  </p>
                </div>
              </div>
              <div class="level-item has-text-centered">
                <div>
                  <p class="heading">
                    Total volume (m³)
                  </p>
                  <p class="title">
                    {{ totalVolume }}
                  </p>
                </div>
              </div>
              <div class="level-item has-text-centered">
                <div>
                  <p class="heading">
                    Price (RM)
                  </p>
                  <p class="title">
                    {{ subPrice }}
                  </p>
                </div>
              </div>
              <div class="level-item has-text-centered">
                <div>
                  <p class="heading">
                    Total Price (RM)
                  </p>
                  <p class="title">
                    {{ totalPrice }}
                  </p>
                </div>
              </div>
            </div>

            <div class="is-divider" data-content="PURCHASE DETAILS"></div>
            <div class="columns">
              <div class="column">
                <selector-input
                    v-model="selected_branch"
                    label="Branch"
                    :required="true"
                    :potentialData="branchesOptions"
                    name="branch"
                    :editable="true"
                    placeholder="Select a branch"
                    :error="form.errors.get('selectedBranch')">>
                </selector-input>

                <transition name="custom-classes-transition"
                            enter-active-class="fadeUp-enter-active fadeUp-enter-to"
                            leave-active-class="fadeDown-leave-active fadeDown-leave-to">
                  <table class="table is-hoverable is-fullwidth" v-if="showLots">
                    <thead>
                    <th>Lot type</th>
                    <th>Price (RM)</th>
                    <th>Volume (m³)</th>
                    <th>Quantity</th>
                    </thead>
                    <tbody>
                    <tr v-for="(category, index) in categories">
                      <td>{{ category.name }}</td>
                      <td>{{ category.price }}</td>
                      <td>{{ category.volume | convertToMeterCube}}</td>
                      <td v-if="availableLots(category).length > 0">
                        <text-input v-model="categories[index].quantity" :defaultValue="categories[index].quantity"
                                    :label="'Quantity'"
                                    :required="true"
                                    type="number"
                                    :editable="true"
                                    :hideLabel="true"
                                    name="quantity"
                                    @input="updateTotals(category)"
                                    :error="categories[index].error"
                                    :ref="'category-'+category.id">
                        </text-input>
                      </td>
                      <td v-else>Sold out</td>
                    </tr>
                    </tbody>
                  </table>
                </transition>
              </div>
              <div class="is-divider-vertical"></div>
              <div class="column">

                <div class="field">
                  <text-input
                      v-model="form.rental_duration"
                      :defaultValue="form.rental_duration"
                      :required="true"
                      label="Rental duration (months)"
                      name="rental_duration"
                      type="number"
                      :editable="true"
                      :error="form.errors.get('rental_duration')">
                  </text-input>
                </div>

                <transition name="custom-classes-transition"
                            enter-active-class="fadeUp-enter-active fadeUp-enter-to"
                            leave-active-class="fadeDown-leave-active fadeDown-leave-to">
                  <div class="field mt-10" v-if="totalPrice > 0">
                    <p class="is-size-6">
                      <b>Select payment method</b>
                    </p>
                    <div class="buttons">
                      <button type="button" class="button"
                              :class="form.payment_definition_id == definition.id ? 'is-info' : ''"
                              v-for="definition in definitions"
                              :key="definition.id"
                              @click="setPaymentDefinition(definition)">
                        <figure class="image is-32x32">
                          <img class="is-rounded" :src="definition.image_path_display"/>
                        </figure>

                        <span class="pl-2">{{ definition.name }}</span>
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
            </div>

            <button type="submit" :disabled="!canSubmit || form.submitting"
                    class="button is-primary is-tooltip-danger is-tooltip-right" :class="submitButtonClass"
                    :data-tooltip="submitTooltipText">Submit
            </button>
          </form>
          <article class="message" v-else>
            <div class="message-body">
              We are currently fully occupied. Please try again later or contact our support team.
            </div>
          </article>
        </div>

      </div>

    </transition>

    <modal :active="isViewing" @close="isViewing = false" v-if="selectedPayment">
      <template slot="header">{{ dialogTitle }}</template>

      <div class="columns">
        <div class="column">
          <figure class="image">
            <img :src="selectedPayment.picture">
          </figure>
        </div>
        <div class="column">
          <text-input :defaultValue="selectedPayment.name"
                      :editable="false"
                      :required="false"
                      label="Paid by"
                      type="text">
          </text-input>
          <text-input :defaultValue="'RM' + selectedPayment.price"
                      :editable="false"
                      :required="false"
                      label="Amount payable"
                      type="text">
          </text-input>
          <text-input :defaultValue="selectedPayment.lots[0].rental_duration + ' months'"
                      :editable="false"
                      :required="false"
                      label="Rental duration"
                      type="text">
          </text-input>
          <div v-if="selectedPayment.lots[0].expired_at">
            <text-input :defaultValue="selectedPayment.lots[0].expired_at | date"
                        :editable="false"
                        :required="false"
                        label="Expire date"
                        type="text">
            </text-input>
          </div>
          <div>
            <div v-html="$options.filters.formatPaymentStatus(selectedPayment.status)"></div>
          </div>

          <div v-if="can_manage || selectedPayment.status == 'true'">
            <div class="is-divider" data-content="Lots purchased"></div>

            <table class="table is-hoverable is-fullwidth">
              <thead>
              <tr>
                <th>Name</th>
                <th>Monthly fee(RM)</th>
                <th>Volume(m³)</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="lot in selectedPayment.lots">
                <td v-text="lot.name"></td>
                <td v-text="lot.price"></td>
                <td>{{ lot.volume | convertToMeterCube }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <template slot="footer" v-if="can_manage">
        <button v-if="selectedPayment.status !== 'true'" class="button is-primary" :class="approveLoadingClass"
                @click="approve">Approve
        </button>
      </template>
    </modal>

    <confirmation :isConfirming="confirmSubmit"
                  title="Confirmation"
                  message="Confirm payment approval?"
                  @close="confirmSubmit = false"
                  @confirm="onApprove">
    </confirmation>

    <confirmation :isConfirming="confirmPayment"
                  title="Confirmation"
                  message="Confirm payment approval?"
                  @close="confirmPayment = false"
                  @confirm="onSubmit">
    </confirmation>

    <confirmation :isConfirming="confirmCompleteProfile"
                  title="Profile incomplete"
                  message="You need to complete your profile before you can complete your purchase"
                  @close="back()"
                  @confirm="redirectToProfile()">
    </confirmation>

  </div>
</template>

<script>
import TableView from '../components/TableView.vue';
import Accordion from '../components/Accordion.vue';

export default {
  props: ['can_manage', 'can_purchase'],

  components: {TableView, Accordion},

  data() {
    return {
      definitions: [],
      isPurchasing: false,
      dialogActive: false,
      override: false,
      showLots: false,
      form: new Form({
        payment_slip: '',
        selectedBranch: '',
        lot_purchases: '',
        price: '',
        rental_duration: '',
        payment_definition_id: '',
      }),
      approveForm: new Form({
        id: ''
      }),
      categories: '',
      selected_branch: '',
      branchesOptions: [],
      selectableLots: [],
      rental_duration: '',
      totalLots: 0,
      totalVolume: 0,
      subPrice: 0,
      paymentSlip: {name: 'No file selected'},
      selectedPayment: '',
      isViewing: false,
      submitting: false,
      confirmSubmit: false,
      confirmPayment: false,
      searchable: 'payments.status',
      confirmCompleteProfile: false
    };
  },

  mounted() {
    if (this.can_manage) {
      this.searchable += ',users.name';
    }

    this.$events.on('viewPayment', data => this.view(data));

    this.getBranches();

    this.getLotCategories();
  },

  methods: {
    getLotCategories() {
      axios.get('/internal/categories')
          .then(response => this.setLotCategories(response.data));
    },
    getBranches() {
      axios.get('/internal/branches/allselector')
          .then(response => this.setBranches(response));

      this.getPaymentGatewayDefinitions();
    },
    getPaymentGatewayDefinitions() {
      axios.get("/internal/payments/definitions")
          .then(response => this.setDefinitions(response));
    },
    setDefinitions(response) {
      this.definitions = response.data;
    },
    setBranches(response) {
      this.branchesOptions = response.data.map(branches => {
        let obj = {};
        obj['label'] = branches.branch_name;
        obj['value'] = branches.id;
        return obj;
      });
    },
    setLotCategories(data) {
      //this.categories = data.data;
      this.selectableLots = [];
      data.data.forEach(function (category) {
        category.quantity = 0;
        category.error = '';
        category.lots.forEach(function (lot) {
          if (lot.user_id == null) {
            lot.selected = false;
            this.selectableLots.push(lot);
          }
        }.bind(this))
      }.bind(this));
      this.categories = data.data;

      this.isPurchasing = this.getParameterByName('new') == 'true';
    },
    setPaymentDefinition(selected) {
      this.form.payment_definition_id = selected.id;
    },

    getParameterByName(name, url) {
      if (!url) url = window.location.href;
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return '';
      return decodeURIComponent(results[2].replace(/\+/g, " "));
    },

    view(data) {
      this.selectedPayment = data;
      this.isViewing = true;
    },

    approve() {
      this.confirmSubmit = true;
    },

    onApprove() {
      this.confirmSubmit = false;
      this.submitting = true;
      this.approveForm.id = this.selectedPayment.id;
      this.approveForm.post('/payment/approve')
          .then(response => this.onSuccess());
    },

    back() {
      this.confirmCompleteProfile = false;
      this.isPurchasing = false;
    },
    redirectToProfile() {
      window.location.href = "/profile";
    },
    reset() {
      this.selectableLots.forEach(function (lot) {
        lot.selected = false;
      });
      this.totalLots = 0;
      this.totalVolume = 0;
      this.subPrice = 0;
      this.rental_duration = '';
    },

    submit() {
      this.confirmPayment = true;
    },

    onSubmit() {
      this.confirmPayment = false;
      let selectedLots = [];
      if (this.selected_branch != null) {
        this.form.selectedBranch = this.selected_branch.value;
      }
      this.categories.forEach(function (category) {
        let lots = [];
        let availableLots = this.availableLots(category);
        lots = _.take(availableLots, category.quantity);
        lots.forEach(function (lot) {
          selectedLots.push(lot.lot);
        });
      }.bind(this));

      //let selectedLots = _.filter(this.selectableLots, function(lot){ return lot.selected; });
      this.form.lot_purchases = selectedLots.map(function (lot) {
        let obj = {};
        obj['id'] = lot.id;
        obj['rental_duration'] = this.form.rental_duration;
        return obj;
      }.bind(this));

      this.form.price = this.totalPrice;

      this.form.post(this.action)
          .then(data => this.onSuccess(data))
          .catch(error => this.onFail(error));
    },

    onSuccess(data) {
      flash("Redirecting to payment gateway", "success");
      console.log(data);
      window.location.href = data.payment.payment_response;
      // this.isPurchasing = false;
      // this.isViewing = false;
      // this.submitting = false;
      //
      //
      // this.$refs.payments.refreshTable();
    },

    onFail(error) {

    },

    changePaymentSlipImage(e) {
      this.paymentSlip = {src: e.src, file: e.file};
      this.form.payment_slip = e.file;
      this.form.errors.clear('payment_slip');
    },

    toggleCheck(lot) {
      if (lot.selected) {
        this.totalVolume = this.totalVolume + parseInt(lot.volume);
        this.subPrice += lot.price;
        this.totalLots++;
      } else {
        this.totalVolume = this.totalVolume - parseInt(lot.volume);
        this.subPrice -= lot.price;
        this.totalLots--;
      }

    },

    updateTotals(category) {
      category.error = "";
      let quantity = parseInt(category.quantity);
      let availableLotCount = this.availableLots(category).length;

      if (availableLotCount < quantity) {
        category.quantity = 0;
        category.error = "We only have " + availableLotCount + " of " + category.name + " lots left";
      }

      if (category.quantity) {
        this.subPrice = _.sumBy(this.categories, function (category) {
          return parseInt(category.quantity) * category.price;
        });
        this.totalVolume = _.sumBy(this.categories, function (category) {
          return this.$options.filters.convertToMeterCube(parseInt(category.quantity) * category.volume);
        }.bind(this));
        this.totalLots = _.sumBy(this.categories, function (category) {
          return parseInt(category.quantity);
        });
      }
    },

    availableLots(category) {
      let availableLots = [];
      this.selectableLots.forEach(function (lot, key) {
        if (lot.category_id == category.id && lot.user_id == null) {
          availableLots.push({'lot': lot, 'index': key});
        }
      }.bind(category));
      return availableLots;
    },

    modalOpen() {
      this.form.reset();
      this.reset();
      this.isPurchasing = true;
    }
  },

  watch: {
    selected_branch(newVal) {
      if (newVal)
        this.showLots = true;
    },

    isPurchasing(newVal) {
      if (newVal && !this.can_purchase) {
        this.confirmCompleteProfile = true;
      }
    }
  },

  computed: {
    dialogTitle() {
      return this.isPurchasing ? "Purchase lot" : 'View payment #' + this.selectedPayment.id;
    },

    action() {
      return "/payment/purchase";
    },

    totalPrice() {
      return this.subPrice * this.form.rental_duration;
    },

    canSubmit() {
      return this.totalLots > 0 && this.form.payment_definition_id && this.form.rental_duration;
    },

    canPurchase() {
      return !this.selectableLots.length == 0;
    },

    tooltipClass() {
      return this.canPurchase ? '' : 'tooltip';
    },

    submitButtonClass() {
      let classes = [];

      if (!this.canSubmit || this.form.errors.any()) {
        classes.push("tooltip");
      }
      if (this.form.submitting) {
        classes.push("is-loading");
      }
      return classes;

    },

    submitTooltipText() {
      let text = '';

      if (this.selected_branch == '' && !this.totalLots > 0) {
        text = 'Please select a branch';
      }

      if (!this.totalLots > 0 && this.selected_branch != '') {
        text = 'Please select at least one lot';
      }

      if (this.form.errors.any()) {
        text = 'There are errors in the form';
      }

      if (!this.form.payment_definition_id) {
        text = 'Please select payment method';
      }

      return text;
    },

    approveLoadingClass() {
      return this.submitting ? 'is-loading' : '';
    },

    mainTitle() {
      return this.can_manage ? 'Purchases' : 'Purchase history';
    },

    fields() {
      let displayFields = [
        {name: 'name', title: 'Made by'},
        {name: 'created_at', sortField: 'payments.created_at', title: 'Purchase date', callback: 'date'},
        {name: 'price', sortField: 'price', title: 'Amount payable(RM)'},
        {name: 'status', sortField: 'status', title: 'Status', callback: 'purchaseStatusLabel'},
        {name: '__component:payments-actions', title: 'Actions'}
      ];

      if (!this.can_manage) {
        displayFields = _.drop(displayFields);
      }

      return displayFields;
    }
  },
}
</script>
