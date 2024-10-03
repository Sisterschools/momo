<script setup>
  import { store } from '../store.js'
  import LoginForm from './LoginFormComponent.vue'
  import LogedinlayoutComponent from './LoggedinLayoutComponent.vue'

</script>

<script>
export default{
  data(){
    return{
      data: null
    }
  },
}
</script>

<template>
  <div>
    <LogedinlayoutComponent 
      v-if="store.token" 
      :user-data="store.userData"
    />
    <LoginForm 
      v-else
      caption="Login" 
      ok-text="Login"
      label-name="Your emailaddress :"
      label-password="and password :"
    />
  </div>
</template>