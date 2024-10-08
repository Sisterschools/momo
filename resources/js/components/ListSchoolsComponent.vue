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
    :columns="{
      id: {type:'id', visible:false},
      title:{type:'string', as:'name'}, 
      website: {type:'string'}, 
      phone_number:{ as:'phone'}, 
      founding_year:{ as:'founded'}, 
      student_capacity:{ as:'# students'}, 
      user:{ as:'admin', subItem:{user: 'user.email'}}, 
      photo:{ }, 
      address:{ }, 
      description:{ },
      created_at:{ type:'date', visible:false}, 
      updated_at:{ type:'date', visible:false}, 
    }"
    :selectable-rows="selectableRows"
    :shift-click="true"
    :on-row-click="selectSchool"
    caption="Schools"
  />
</template>