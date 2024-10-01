<script setup>
  import SchoolsListComponent from './SchoolsListComponent.vue'
  
  defineProps({
    userData: { type:{}, required: true, default: null}
  })
</script>

<script>
  export default{
    computed: {
      whichComponent(){
        return SchoolsListComponent;
      }
    },
  }
</script>

<template>
  <div>    
    <component 
      :is="whichComponent" 
      :user-data="userData" 
    />
  </div>
</template>