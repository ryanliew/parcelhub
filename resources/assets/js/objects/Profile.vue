<template>
  <div>
    <loader v-if="loading"></loader>
    <div class="card">
      <div class="card-header">
        <div class="card-header-title level">
          <div class="level-left">
            <div class="level-item">
              Profile
            </div>
          </div>
          <div class="level-right" v-if="canManage">
            <div class="level-item">
              <button class="button is-primary" @click="back()">
                <i class="fa fa-arrow-circle-left"></i>
                <span class="pl-5">Back to list</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-content">
        <form @submit.prevent="submit"
              @keydown="form.errors.clear($event.target.name)"
              @input="form.errors.clear($event.target.name)"
              @keyup.enter="submit">
          <div class="columns">
            <div class="column">
              <text-input v-model="form.name" :defaultValue="form.name"
                          label="Name"
                          :required="true"
                          name="name"
                          type="text"
                          :editable="true"
                          :error="form.errors.get('name')"
                          :focus="true">
              </text-input>
            </div>
            <div class="column">
              <text-input v-model="form.phone" :defaultValue="form.phone"
                          label="Phone"
                          :required="true"
                          name="phone"
                          type="text"
                          :editable="true"
                          :error="form.errors.get('phone')">
              </text-input>
            </div>
          </div>
          <div class="field">
            <text-input v-model="form.address" :defaultValue="form.address"
                        label="Address"
                        :required="true"
                        name="address"
                        type="text"
                        :editable="true"
                        :error="form.errors.get('address')">
            </text-input>
          </div>
          <div class="field">
            <text-input v-model="form.address_2" :defaultValue="form.address_2"
                        label="City"
                        :required="true"
                        name="address_2"
                        type="text"
                        :editable="true"
                        :error="form.errors.get('address_2')">
            </text-input>
          </div>
          <div class="columns">
            <div class="column">
              <text-input v-model="form.postcode" :defaultValue="form.postcode"
                          label="Postcode"
                          :required="true"
                          name="postcode"
                          type="text"
                          :editable="true"
                          :error="form.errors.get('postcode')">
              </text-input>
            </div>
            <div class="column">
              <selector-input v-model="selectedCountry"
                              :defaultData="selectedCountry"
                              label="Country"
                              :hideLabel="false"
                              :required="true"
                              :potentialData="countries"
                              placeholder="Select country"
                              name="countries"
                              :unclearable="true">
              </selector-input>
            </div>
            <div class="column">
              <text-input v-model="form.state" :defaultValue="form.state"
                          label="State"
                          :required="true"
                          name="state"
                          type="text"
                          :editable="true"
                          :error="form.errors.get('state')"
                          v-if="!selectedCountry || !selectedCountry.states.length > 0">
              </text-input>
              <selector-input v-model="selectedState"
                              :defaultData="selectedState"
                              :hideLabel="false"
                              label="State"
                              :required="true"
                              :potentialData="selectedCountry.states"
                              placeholder="Select state"
                              name="states"
                              :unclearable="true"
                              v-else>
              </selector-input>
            </div>
          </div>

          <div class="is-divider" data-content="PASSWORD"></div>

          <div class="level">
            <div class="level-left">
              <div class="level-item">
                <a class="button is-info" @click="changePassword" v-if="!form.change_password">Change password</a>
                <a class="button is-danger" @click="unchangePassword" v-else>Cancel</a>
              </div>
              <div class="level-item" v-if="form.change_password">
                <text-input v-model="form.password" :defaultValue="form.password"
                            label="New Password"
                            :required="true"
                            name="password"
                            type="password"
                            :editable="true"
                            :error="form.errors.get('password')">
                </text-input>
              </div>
              <div class="level-item" v-if="form.change_password">
                <text-input v-model="form.password_confirmation" :defaultValue="form.password_confirmation"
                            label="Retype new password"
                            :required="true"
                            name="password_confirmation"
                            type="password"
                            :editable="true"
                            :error="form.errors.get('password_confirmation')">
                </text-input>
              </div>
            </div>
          </div>

          <button class="button is-primary mt-15" :disabled="this.form.errors.any()" :class="buttonClass">Update
          </button>
        </form>
      </div>
    </div>
    <confirmation :isConfirming="confirmSubmit"
                  title="Confirmation"
                  message="Confirm profile update?"
                  @close="confirmSubmit = false"
                  @confirm="onSubmit">
    </confirmation>
  </div>
</template>

<script>
export default {
  props: ['canManage', 'user', 'countries'],
  data() {
    return {
      loading: true,
      countries: [],
      form: new Form({
        name: '',
        phone: '',
        address: '',
        address_2: '',
        state: '',
        postcode: '',
        country: '',
        id: '',
        password: '',
        password_confirmation: '',
        change_password: false
      }),
      profile: '',
      confirmSubmit: false,
      selectedCountry: '',
      selectedState: "",
    };
  },

  mounted() {
    this.getUser();
  },

  methods: {
    getUser() {
      if (!this.user) {
        this.loading = true;
        axios.get('/internal/user')
            .then(response => this.setUser(response.data));
      } else {
        this.setUser(this.user);
      }
    },

    setUser(user) {
      this.profile = user;

      this.form.id = this.profile.id;

      this.form.name = this.profile.name;
      this.form.phone = this.profile.phone;
      this.form.address = this.profile.address;
      this.form.address_2 = this.profile.address_2;
      this.form.state = this.profile.state;
      this.form.postcode = this.profile.postcode;
      this.form.country = this.profile.country;

      this.loading = false;

    },

    back() {
      this.$emit('back');
    },

    submit() {
      this.confirmSubmit = true;
    },

    onSubmit() {
      this.confirmSubmit = false;
      this.form.post(this.action)
          .then(response => this.onSuccess(response))
          .catch(response => this.onError(response));
    },

    onSuccess() {
      this.form.submitting = false;
      if (this.user) {
        this.back();
      } else {
        this.getUser();
      }
    },

    onError() {
      this.form.submitting = false;
    },

    changePassword() {
      this.form.change_password = true;
    },

    unchangePassword() {
      this.form.password = '';
      this.form.password_confirmation = '';
      this.form.change_password = false;
    }
  },

  computed: {

    action() {
      let action = '/user/update';
      return action;
    },

    buttonClass() {
      return this.form.submitting ? 'is-loading' : '';
    }
  },

  watch: {
    selectedCountry(newVal) {
      this.form.country = newVal.value;
    },

    selectedState(newVal) {
      this.form.state = newVal.value;
    }
  }
}
</script>