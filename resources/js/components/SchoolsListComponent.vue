<script setup>
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
    serverAPI( '/api/schools', null, 'GET', this.userData.access_token )
    .catch( console.log )
    .then( ( json ) => {
      this.items = [json];//[{test:1, test2:2},{test:3, test2:4}]
    } )
  }
}
</script>

<template>
  <ListComponent :items="items" />
</template>