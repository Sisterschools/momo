<script setup>
  import { store } from '../store.js'

  defineProps({
    id: {type: String, required: false, default: 'id'},
    items: {type: Array, required: true, default: new Array},
    onRowClick: {type: Function, required: false, default: () => {}}
  })
</script>

<script>
export default{
  mounted(){
    store.isListComponent = true
  },
  unmounted(){
    store.isListComponent = false;
  },
  methods:{
    _onRowClick( o ){
      var id = 
        o.target.closest('.row')
        .querySelector('div:not([data-src=""])')
        .getAttribute('data-src')
        
      if(id)
        this.onRowClick( id )
    }
  },
}
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
    </div>
    <div  
      v-for="item in items" 
      :key="item"
      class="row"
      @click.stop="_onRowClick" 
    >
      <div 
        v-for="(prop, index) in item"
        :key="prop"
        :data-src="index == id ? prop : null"
        class="cell"
      >
        {{ prop }}
      </div>
    </div>
  </div>
</template>