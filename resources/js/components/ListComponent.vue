<script setup>
  defineProps({
    items: {type: Array, required: true, default: new Array}
  })
</script>

<template>
  <div class="table">
    <div 
      v-if="items && items[0]"
      class="row" 
    >
      <div
        v-for="(key) in Object.entries(items[0])" 
        :key="key"
        class="cell"
      >
        {{ key[0] }}
      </div>
      <div>[+]</div>
    </div>
    <div  
      v-for="item in items" 
      :key="item"
      class="row" 
    >
      <div 
        v-for="prop in item"
        :key="prop"
        class="cell"
      >
        {{ prop }}
      </div>
    </div>
  </div>
</template>