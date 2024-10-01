<script setup>
  import LoginForm from './LoginFormComponent.vue'
  import LogedinlayoutComponent from './LoggedinLayoutComponent.vue'

</script>

<script>
export default{
  emits: ["isloggedin"],
  data(){
    return{
      data: null
    }
  },
  methods:{
    showLoggedin( data ){
      this.data = data
    }
  }
}
</script>

<template>
  <div>
    <LogedinlayoutComponent 
      v-if="data" 
      :user-data="data"
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