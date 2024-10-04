<script setup>
  import { store } from '../store.js'

  defineProps({
    id: {type: String, required: false, default: 'id'},
    showIDColumn: { type: Boolean, required: false, default: false}, 
    items: {type: Array, required: true, default: new Array},
    onRowClick: {type: Function, required: false, default: () => {}},
    caption: {type: String, required: true, default: 'List'}
  })
</script>

<script>
export default{
  computed:{
    filtered: function(){
      var objs = this.items.map( ( obj ) => {
        var o = Object.assign({}, obj)
        if(! this.showIDColumn)
          delete o[this.id]
        return o
      })
      return objs
    },
    filtered0: function(){
      var objs = Object.entries(this.items[0]),
        n = objs.find( ( o ) => o[0] == this.id)
      if( ! this.showIDColumn && n )
        objs.splice( objs[n], 1)
      return objs
    }
  },
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
  <p>{{ caption }}</p>
  <div class="table">
    <div 
      v-if="items && items[0]"
      class="row" 
    >
      <div
        v-for="(key) in filtered0" 
        :key="key"
        :class="showIDColumn || id != key[0] ? 'cell': null"
      >
        {{ showIDColumn || id != key[0] ? key[0] : '' }}
      </div>
    </div>
    <div  
      v-for="(item, n) in filtered" 
      :key="item"
      class="row"
      @click.stop="_onRowClick" 
    >
      <div 
        v-for="(prop, index) in item"
        :key="prop"
        :data-src="items[n][id]"
        :class="showIDColumn || index != id ? 'cell' : null"
      >
        {{ showIDColumn || index != id ? prop : '' }}
      </div>
    </div>
  </div>
</template>

<style scoped>
p{
  text-align: center;
}
</style>