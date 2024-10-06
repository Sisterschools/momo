<script setup>
  import { store } from '../store.js'
  import ListComponent from './ListComponent.vue'
  import serverAPI from '../server.js'

  defineProps({
    userData: { type: {}, required:  true, default: ''},
  })
</script>

<script>
export default{
  data(){
    return {
      items: [],
      selectableRows: store.userData.data.role == 'admin'
    }
  },  
  mounted(){
    store.addNew = '/schools/add'
    serverAPI( '/api/schools', null, 'GET', store.token )
    .catch( console.log )
    .then( ( json ) => {
      this.items = json.data
    } )
  },
  methods:{
    selectSchool( id ){
      store.router.push({name: 'viewschool', params: {id}})
    }
  }
}
</script>

<template>
  <ListComponent 
    :items="items"
    :selectable-rows="selectableRows"
    :on-row-click="selectSchool"
    caption="Schools"
    :sub-items="[{user: 'user.email'}]" 
    :do-not-show="[]"
    :transscribe="[
      ['phone_number', 'phone'], 
      ['founding_year', 'founded'],
      ['student_capacity', '# students'], 
      ['user', 'admin'], 
      ['title', 'name']
    ]"
  />
</template>