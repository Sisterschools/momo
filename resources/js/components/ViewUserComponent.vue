<script setup>
  import server from '../server.js'
  import  { store } from '../store.js'
  import Form from './FormComponent.vue'
</script>

<script>
export default{
  data(){
    return{
      name: null,
      email: null,
      password: null,
      role: null
    }
  },
  methods:{
    addOrUpdateUser(){
    }
  }
}
</script>

<template>
  <div>
    <Form 
      cancel="Cancel"
      :caption="$route.params.id? 'Update user' : 'New user'"
      @form-submitted="addOrUpdateUser"
    >  
      <label>
        Name : 
        <input 
          v-model="name"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Email : 
        <input 
          v-model="email"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label>
        Password : 
        <input 
          v-model="password"
          required
          minlength="8"
          autocomplete="false"
          type="password"
        >
      </label>
      <label>
        Role :
        <select v-model="role">
          <option value="student">student</option>
          <option value="teacher">teacher</option>
        </select>
      </label>
    </Form>
  </div>
</template>