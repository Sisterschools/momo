<script setup>
import server from '../server.js'
import  { store } from '../store.js'
import Form from './FormComponent.vue'
</script>

<script>
export default{
  data(){
    return{
      title: '',
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      address: '',
      description: '',
      phone_number: '',
      website: '',
      founding_year: '',
      student_capacity: '',
    }
  },
  mounted(){
    if( this.$route.params.id ){
      server( '/api/schools/' + this.$route.params.id, [], 'GET', store.token )
      .then( ( json ) => {
        this.updateData(this, json.data)
      })
    }
  },
  methods:{
    updateData( t, inp ){
      for( var i in inp){
        if(typeof inp[i] == 'object')
          this.updateData(t, inp[i])
        else if( typeof t[i] != 'undefined')
          t[i] = inp[i]
      }
    },
    addOrUpdateSchool( id ){
      var uri = '/api/schools',
        method = 'POST',
        id = this.$route.params.id;

      if( id ){
        uri += '/' + id
        method = 'PUT'
      }
      server( uri, { 
        name: this.name,
        title: this.title,
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
        address: this.address,
        description: this.description,
        phone_number: this.phone_number,
        website: this.website,
        founding_year: this.founding_year,
        student_capacity: this.student_capacity,
        role: 'school' 
      }, method, store.token, 'application/json' )
      .then( ( ) => {
        store.router.push('/')
      })
    },
  }
}
</script>

<template>
  <div>
    <Form 
      cancel="Cancel"
      :caption="$route.params.id? 'Update school' : 'New school'"
      @form-submitted="addOrUpdateSchool"
    >
      <label>
        Name of the school : 
        <input 
          v-model="title"
          required
          maxlength="255"
          autocomplete="false"
        >
      </label>
      <label v-if="! $route.params.id">
        Name of contact person : 
        <input 
          v-model="name"
          required
          autocomplete="false"
        >
      </label>
      <label>
        Email to log in : 
        <input 
          v-model="email"
          required
          type="email"
          autocomplete="email"
        >
      </label>
      <label v-if="! $route.params.id">
        Password : 
        <input 
          v-model="password"
          required
          type="password"
          minlength="8"
          autocomplete="new-password"
        >
      </label>
      <label v-if="! $route.params.id">
        Password : 
        <input 
          v-model="password_confirmation"
          required
          type="password"
          minlength="8"
          autocomplete="new-password"
        >
      </label>
      <label>
        Address : 
        <textarea 
          v-model="address"
          required
          autocomplete="false"
        />
      </label>
      <label>
        Description : 
        <input 
          v-model="description"
          required
          autocomplete="false"
        >
      </label>
      <label>
        Phone : 
        <input 
          v-model="phone_number"
          type="tel"
          maxlength="20"
          autocomplete="false"
        >
      </label>
      <label>
        Website : 
        <input 
          v-model="website"
          type="URL"
          autocomplete="false"
        >
      </label>
      <label>
        Founding year : 
        <input 
          v-model="founding_year"
          min="1800"
          max="2024"
          autocomplete="false"
        >
      </label>
      <label>
        Student capacity : 
        <input 
          v-model="student_capacity"
          autocomplete="false"
        >
      </label>
    </Form>
  </div>
</template>