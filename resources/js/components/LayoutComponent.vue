<script setup>
  import LoginForm from './LoginFormComponent.vue'
  import LogedinlayoutComponent from './LoggedinLayoutComponent.vue'
</script>

<script>
export default{
  data(){
    return{
      loggedin: false
    }
  },
  methods:{
    showLoggedin(){
      this.loggedin = true
    }
  }
}
</script>

<template>
  <div>
    <LogedinlayoutComponent 
      v-if="loggedin" 
    />
    <LoginForm 
      v-else
      caption="Login" 
      ok-text="Login"
      label-name="Your emailaddress :"
      label-password="and password :"
      @loggedin="showLoggedin"
    />
  </div>
</template>