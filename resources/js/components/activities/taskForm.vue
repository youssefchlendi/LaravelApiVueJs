<template>
  <div>
          <b-modal
              id="modal-1"
              ref="modal"
              title="Add task"
              @hidden="resetModal"
              @ok="handleOk"
          >
              <form ref="form" @submit.stop.prevent="handleSubmit">
                  <b-form-group
                      label="Task Name"
                      label-for="name-input"
                      invalid-feedback="Name is required"
                      :state="itemNameState"
                  >
                      <b-form-input
                          id="name-input"
                          v-model="item.item_name"
                          :state="itemNameState"
                          required
                      ></b-form-input>
                      <label for="datepicker">Choose a date</label>
                      <b-form-datepicker id="datepicker" :min="new Date()" v-model="item.dead_line" class="mb-2"></b-form-datepicker>
                  </b-form-group>
              </form>
          </b-modal>
      </div>
</template>

<script>
export default {
    emits:['handleSubmit'],
    data(){
        return{
            itemNameState: null,
            item:{
                id:'',
                item_name:'',
                activity_id:'',
                status: 0,
                dead_line:'',
            }
        }
    },
    methods:{
        resetModal() {
            this.item.item_name = ''
            this.itemNameState = null
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault()
            // Trigger submit handler
            this.handleSubmit()
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            this.itemNameState = valid
            return valid
        },
        handleSubmit(){
             // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            this.$emit('handleSubmit',this.item);
            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide('modal-1')
            })
        }
    }
}
</script>

<style>

</style>
