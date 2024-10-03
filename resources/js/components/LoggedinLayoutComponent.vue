<script setup>
  import ListSchoolsComponent from './ListSchoolsComponent.vue'
  
  defineProps({
    userData: { type:{}, required: true, default: null}
  })
</script>

<script>
  export default{
    computed: {
      whichComponent(){
        return ListSchoolsComponent;
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