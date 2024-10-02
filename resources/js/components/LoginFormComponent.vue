<script setup>
  import Form from './FormComponent.vue'
  import serverAPI from '../server.js'

  defineProps({
    caption: {type:String, default:'Login form'},
    okText: {type:String, default:'Ok'},
    labelName:{type:String, default: 'Name'},
    labelPassword: {type:String, default:'Secret'}
  })
</script>

<script>
export default{
  emits: ['loggedin'],
  data(){
    return{
      invalid: false,
      email: '',
      password: ''
    }
  },
  methods: {
    login() {
      this.invalid = false
      serverAPI('/api/login', {
        email: this.email, 
        password: this.password
      }, 
      'POST' )
      .catch( () => this.invalid = true )
      .then( ( json ) => {
        if(this.invalid == false){
          this.invalid = false
          this.$emit('loggedin', json)
        }
      })
    }
  }
}
</script>

<template>
  <Form 
    :caption="caption" 
    :ok-text="okText"
    @form-submitted="login"
  >
    <p 
      v-if="invalid"
      class="error" 
    >
      Invalid login. Please try again.
    </p>
    <label>
      {{ labelName }}
      <input 
        v-model="email"
        v-focus 
        type="email" 
        required
        autocomplete="false"
      >
    </label>
    <label>
      {{ labelPassword }}
      <input 
        v-model="password"
        type="password" 
        required
      >
    </label>
    <p class="small-font italic">
      <a href="#/reset-password">Forgot your password?</a>
    </p>
  </Form>
</template>

<style scoped>
#app p.error{
  color: var(--momo-red);
}
</style>