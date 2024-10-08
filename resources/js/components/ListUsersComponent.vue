<script setup>
  import { store } from '../store.js'
  import ListComponent from './ListComponent.vue'
  import serverAPI from '../server.js'

  defineProps({
  })
</script>

<script>
export default{
  data(){
    return {
      items: [],
      selectableRows: (store.userData.data.role == "admin" || store.userData.data.role == "school")
    }
  },  
  mounted(){
    store.addNew = '/user/add'
    serverAPI( '/api/users', null, 'GET', store.token )
    .catch( console.log )
    .then( ( json ) => {
      this.items = json.data
    } )
  },
  methods:{
    selectUser( id ){
      store.router.push({name: 'viewuser', params: {id}})
    }
  }
}
</script>

<template>
  <ListComponent 
    :items="items" 
    :columns="{
      id: { type:'id', visibility:false},
      name: {},
      email: {},
      role: {},
      created_at:{type:'date', visibility:false},
      updated_at:{type:'date', visibility:false}
    }"
    :selectable-rows="selectableRows"
    :shift-click="true"
    :on-row-click="selectUser"
    caption="Users"
  /> 
</template>