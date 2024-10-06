<script setup>
  import { RouterLink } from 'vue-router';
import { store } from '../store.js'

  defineProps({
    id: {type: String, required: false, default: 'id'},
    selectableRows: {type: Boolean, required: false, default: false},
    shiftClick: {type: Boolean, required: false, default: false},
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
  data(){
    return {
      itemsHaveValidIds: true,
      tableClass: 'table selectableRows ' + ( this.selectableRows && this.itemsHaveValidIds ? ' selectableRows' : ''),
      selectedIds: [],
      prevSelected: null
    }
  },
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
        console.log(m)
        if( m > -1 )
          objs.splice( m, 1 )
      })
      this.transscribe.forEach( ( tr ) => {
        var o = objs.findIndex(( q ) => q[0] == tr[0])
        if(o > -1)
          objs[0][o] = tr[1]
      })
      return objs
    }
  },
  beforeMount(){
    this.items.forEach( ( obj ) => {
      if( ! obj[this.id] ) this.itemsHaveValidIds = false
    })
  },
  mounted(){
    store.isListComponent = true
  },
  
  unmounted(){
    store.isListComponent = false;
  },
  methods:{
    onShiftClick(evnt){
      if(evnt.target.nodeName == 'INPUT'){
        var cell = evnt.target.closest('.row').querySelector('.cell:nth-child(2)'),
        selId = cell.getAttribute('data-src')
        if(evnt.shiftKey){
          selId && this.selectedIds.indexOf(selId) == -1 ? this.selectedIds.push(selId) : ''
          this.setPrevRowSelected(evnt.target)
        }
        else if(selId)
          this.prevSelected = selId
      }
    },
    setPrevRowSelected( rowEl ){
      if( ! rowEl || ! rowEl.closest ) return
      var prevRowEl = rowEl.closest('.row').previousSibling,
        prevCell = prevRowEl && prevRowEl.querySelector ? prevRowEl.querySelector('.cell:nth-child(2)') : null,
        selId = prevCell ? prevCell.getAttribute('data-src') : ''
      if( selId && selId != this.prevSelected && this.selectedIds.indexOf(selId) == -1)
         this.selectedIds.push(selId)
      else if(selId) return
      this.setPrevRowSelected( prevRowEl )
    },
    _onRowClick( o ){
      var id = 
        o.target.closest('.row')
        .querySelector('div:not([data-src=""])')
        .getAttribute('data-src')

      if(id)
        this.onRowClick( id )
    },
    selectOrDeselectAll( evnt ){
      if(! evnt.target.checked) 
        this.selectedIds = []
      else{
        this.items.forEach(( i ) => {
          this.selectedIds.push(i[this.id])
        })
      }
    }
  }
}
</script>

<template>
  <div>
    <div class="header">
      <div v-if="selectableRows">
        <select :disabled="selectedIds.length == 0">
          <option>With selected ...</option>
        </select>
      </div>
      <div>
        <p>{{ caption }}</p>
      </div>
    </div>
    <p v-if="items.length == 0">
      Nothing here yet: create one 
      <RouterLink :to="store.addNew">
        here
      </RouterLink>
    </p>
    <div 
      v-else
      :class="tableClass"
    >
      <div 
        v-if="items && items[0]"
        class="row" 
      >
        <div 
          v-if="selectableRows && itemsHaveValidIds"
          class="cell actions"
        >
          <input 
            type="checkbox" 
            class="selectAll"
            @change="selectOrDeselectAll"
          >
        </div>
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
          v-if="selectableRows && itemsHaveValidIds"
          class="cell actions"
        >
          <input 
            v-model="selectedIds"
            :value="items[n][id]"
            type="checkbox"
            @click="onShiftClick"
          >
        </div>
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
.header > div{
  float: left;
}
.header select{
  padding: 0.5em 0.5em;
}
.header > div:nth-child(1){
  position: absolute;
  margin-top: 1em;
}
.header > div:nth-child(2){
  width: 100%;
}
p{
  text-align: center;
}
</style>