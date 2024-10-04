<script setup>
  import { store } from '../store.js'
  import ListComponent from './ListComponent.vue'
  import serverAPI from '../server.js'

  defineProps({
    userData: { type: {}, required:  true, default: '4321'},
  })
</script>

<script>
export default{
  data(){
    return {
      items: []
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
    :on-row-click="selectSchool"
    caption="Schools"
  />
</template>