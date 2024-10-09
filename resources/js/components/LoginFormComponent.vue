<script setup>
  import { store } from '../store.js'
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
      'POST', 
      )
      .catch( () => this.invalid = true )
      .then( ( json ) => {
        if(this.invalid == false){
          this.invalid = false
          store.token = json.access_token
          store.userData = json
          
          /*  
            Different users can login using a different tab: so start the title with their name
          */
          var title = document.querySelector('title').innerText
          document.querySelector('title').innerText = json.data.name + ' : ' + title
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
    :class="invalid? 'invalid' : null"
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
        autocomplete="username"
        oninvalid="this.setCustomValidity('You forgot to enter your email address!')"
        oninput="this.setCustomValidity('')"
      >
    </label>
    <label>
      {{ labelPassword }}
      <input 
        v-model="password"
        type="password" 
        required
        autocomplete="current-password"
        oninvalid="this.setCustomValidity('You forgot to enter your password!')"
        oninput="this.setCustomValidity('')"
      >
    </label>
    <p class="small-font italic">
      <RouterLink to="/reset-password">
        Forgot your password?
      </RouterLink>
    </p>
  </Form>
</template>

<style scoped>
#app p.error{
  color: var(--momo-red);
}
#app form{
    margin: 20vh auto;
}
#app p{
    margin: 0;
}
</style>