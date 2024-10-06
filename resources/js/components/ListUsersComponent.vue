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
    :selectable-rows="selectableRows"
    :on-row-click="selectUser"
    caption="Users"
    :do-not-show="['created_at', 'updated_at', 'photo']"
  /> 
</template>