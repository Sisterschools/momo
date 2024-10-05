<script setup>
  defineProps({
    caption: {type: String, default: 'Please fill out this form'},
    cancel: {type: String, required: false, default: ''},
    okText: {type: String, default: 'Ok'}
  })
</script>

<script>
export default{
  emits: ['form-submitted'],
  mounted(){
    document.querySelector("form").addEventListener('reset', () => { history.go(-1)})
    var els = document.querySelectorAll("form input, form select, form textarea")
    els.forEach( ( el ) => {
      el.addEventListener('change', () => {
        if( ! el.checkValidity() )
          el.reportValidity()
      })
    })
  },
  methods: {
    submit() {
      this.$emit('form-submitted', this.email)
    }
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>{{ caption }}</div>
    <slot />
    <footer>
      <button 
        v-if="cancel != ''"
        type="reset" 
        :value="cancel"
      >
        {{ cancel }}
      </button>
      <button class="submit">
        {{ okText }}
      </button>
    </footer>
  </form>
</template>  

<style scoped>
footer{
  text-align: right;
}
</style>