<script setup>
  import { RouterLink } from 'vue-router';
import { store } from '../store.js'

  defineProps({
    id: {type: String, required: false, default: 'id'},
    showIDColumn: { type: Boolean, required: false, default: false}, 
    items: {type: Array, required: true, default: new Array},
    onRowClick: {type: Function, required: false, default: () => {}},
    caption: {type: String, required: true, default: 'List'},
    doNotShow: {type: Array, required: false, default: new Array},
    subItems: {type: JSON, required: false, default: new Array},
    transscribe: {type: Array, required: false, default: new Array}
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
        this.doNotShow.forEach((fld) => {
          delete o[fld]
        })
        this.subItems.forEach( ( si ) => {
          for(var i in si){
            var rpl = si[i].split('.')
            rpl = rpl[1].replaceAll("'", "")
            this.items.forEach( ( item ) => {
              if(item[i.toString()] && item[i.toString()][rpl])
                item[i] = item[i.toString()][rpl]
            })
          }
        })
        return o
      })
      return objs
    },
    filtered0: function(){
      var objs = Object.entries(this.items[0]),
        n = objs.findIndex( ( o ) => o[0] == this.id)
      if( ! this.showIDColumn && n > -1 )
        objs.splice( n, 1)
      this.doNotShow.forEach((fld) => {
        var m = objs.findIndex( ( o ) => o[0] == fld )
        if( m > 0)
          objs.splice( m, 1 )
      })
      this.transscribe.forEach( ( tr ) => {
        var o = objs.findIndex(( q ) => q[0] == tr[0])
        if(o > -1)
          objs[o][0] = tr[1]
      })
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
  <div>
    <p>{{ caption }}</p>
    <p v-if="items.length == 0">
      Nothing here yet: create one 
      <RouterLink :to="store.addNew">
        here
      </RouterLink>
    </p>
    <div 
      v-else
      class="table"
    >
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
  </div>
</template>

<style scoped>
p{
  text-align: center;
}
</style>