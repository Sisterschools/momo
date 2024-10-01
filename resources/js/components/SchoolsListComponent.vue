<script setup>
  import ListComponent from './ListComponent.vue'
  defineProps({
    userData: { type: {}, required:  true, default: '4321'}
  })
</script>

<script>

</script>

<template>
  <div>
    <span>{{ userData }}</span>
  </div>
  <ListComponent />
</template>