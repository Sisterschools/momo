<script setup>
  import { RouterLink } from 'vue-router';
  import { store } from '../store.js'

  defineProps({
    columns: {type:Object, required:true, default: new Object},
    selectableRows: {type: Boolean, required: false, default: false},
    shiftClick: {type: Boolean, required: false, default: false},
    showIDColumn: { type: Boolean, required: false, default: false}, 
    items: {type: Array, required: true, default: new Array},
    onRowClick: {type: Function, required: false, default: () => {}},
    caption: {type: String, required: true, default: 'List'},
  })
</script>

<script>
export default{
  data(){
    return {
      showItems: [],
      itemsHaveValidIds: true,
      selectedIds: [],
      prevSelected: null,
      transscribe: [],
      subItems: [],
      visibleColumns: [],
      id: 'id'
    }
  },
  computed:{
    parseItems: function(){
      var newItems = []

      this.items.forEach( ( item ) => {
        var newItem = {}
        Object.keys(this.columns).reverse().forEach((j) => {
          if(this.columns[j]['as'])
            this.transscribe.push([j, this.columns[j]['as']])
          if(this.columns[j]['subItem'])
            this.subItems.push(this.columns[j]['subItem'])
          for(var i in item){
            if( i == j ){
              if(this.columns[j]['type'] == 'date'){
                var d = new Date(item[j])
                if( d ){
                  var opts = this.columns[j]['params'] || 
                    {
                      year: 'numeric', 
                      month: 'short', 
                      day:'numeric', 
                      hour:'numeric', 
                      minute: 'numeric'
                    }
                  d = d.toLocaleDateString('en-EN', opts)
                  item[j] = d
                }
              }
              newItem = Object.assign({ [i]: item[j] }, newItem)
            }
          }
        })
        newItems.push(newItem)
      })
      return newItems
    },
    filtered: function(){
      var objs = this.showItems.map( ( obj ) => {
        var o = Object.assign({}, obj)
        this.subItems.forEach( ( si ) => {
          for(var i in si){
            var rpl = si[i].split('.')
            rpl = rpl[1].replaceAll("'", "")
            this.showItems.forEach( ( item ) => {
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
      var objs = Object.keys(this.showItems[0]),
        items = {}
      objs.forEach((k) => {
        items[k] = k
      })
      this.transscribe.forEach( ( tr ) => {
        var o = objs.findIndex(( q ) => q == tr[0])
        if(o > -1)
          items[tr[0]] = tr[1]
      })
      return items
    }
  },
  beforeMount(){
    Object.keys(this.columns).forEach( (key, n) => { 
      if( typeof this.columns[key]['visible'] == 'undefined' || 
        this.columns[key]['visible'] == true)
        this.visibleColumns.push(n)
      if( this.columns[key]['type'] == 'id')
        this.id = key
    })
    this.items.forEach( ( obj ) => {
      if( typeof obj[this.id] == 'undefined') this.itemsHaveValidIds = false
    })
  },
  mounted(){
    store.isListComponent = true
  },
  
  unmounted(){
    store.isListComponent = false;
  },
  methods:{
    toggleDropdown(){
      var el = this.$el.querySelector('ul.dropdown'),
        icon = this.$el.querySelector('.expandable')
      el.classList.toggle('hide')
      icon.classList.toggle('expanded')
    },
    classIfNotVisibleColumn(key){
      var i = Object.keys(this.columns).indexOf(key),
        min = Math.min(...this.visibleColumns)
      if(i == min)
        return 'cell firstVisible'
      else if(this.visibleColumns.indexOf(i) == -1)
        return 'hide'
      return 'cell'
    },
    tableClasses(){
      return 'table ' + (this.selectableRows && this.itemsHaveValidIds ? ' selectableRows' : '')
    },
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
        .querySelector('div:not(.actions)')
        .getAttribute('data-src')
      if(id)
        this.onRowClick( id )
    },
    selectOrDeselectAll( evnt ){
      if(! evnt.target.checked) 
        this.selectedIds = []
      else{
        this.showItems.forEach(( i ) => {
          this.selectedIds.push(i[this.id])
        })
      }
    }
  }
}
</script>

<template>
  <div>
    <div class="header1">
      <div>
        <select 
          v-if="selectableRows"
          :disabled="selectedIds.length == 0"
        >
          <option>With selected ...</option>
        </select>
        <p>{{ caption }}</p>
        <div>
          <ul>
            <li
              @click="toggleDropdown"
            >
              <span class="expandable" />
              Columns
            </li>
            <li>
              <ul class="dropdown hide">
                <li
                  v-for="(n, i) in Object.keys(columns)" 
                  :key="n"
                >
                  <label>
                    <input 
                      v-model="visibleColumns"
                      type="checkbox"
                      :value="i"
                    > 
                    {{ n }}
                  </label>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <p v-if="(showItems = parseItems) && showItems.length == 0">
      Nothing here yet: create one 
      <RouterLink :to="store.addNew">
        here
      </RouterLink>
    </p>
    <div 
      v-else
      :class="tableClasses()"
    >
      <div 
        v-if="showItems && showItems[0]"
        class="row" 
      >
        <div 
          v-if="selectableRows && itemsHaveValidIds"
          class="actions"
        >
          <input
            type="checkbox" 
            class="selectAll"
            @change="selectOrDeselectAll"
          >
        </div>
        <div
          v-for="(value, key) in filtered0"
          :key="key"
          :class="classIfNotVisibleColumn(key)" 
        >
          {{ value }}
        </div>
      </div>
      <div  
        v-for="(item, n) in filtered"
        :key="item"
        class="row" 
      >
        <div 
          v-if="selectableRows && itemsHaveValidIds"
          class="actions"
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
          :class="classIfNotVisibleColumn(index)"
          @click.stop="_onRowClick"
        >
          {{ prop }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.header1 div{
  box-sizing: border-box;
}
.header1 > div:nth-child(2){
  width: 100%;
}
.header1 select{
  position: absolute;
  left: 0.5em;
  margin-top: 1em; 
  padding: 0.5em 0.5em;
  min-width: 12em;
  max-width: 12em;
}
#app .header1 ul{
  cursor: pointer;
  position: absolute;
  background-color: var(--momo-white);
  right: 0.5em;
  margin-top: 1em;
  z-index: 99;
}
#app .header1 ul ul{
  border: 2px solid var(--momo-blue);
}
#app .header1 ul li{
  padding-top: 0;
}
#app .header1 ul li input{
  display: inline;
  width: 2em;
  accent-color: var(--momo-blue);
  color: var(--momo-white);
}
.header1 > div:nth-child(1){
  width: 100%;
}
.header1 > div:nth-child(2){
  width: 100%;
  position: relative;
  left: -12em; 
}
p{
  text-align: center;
  width: calc(100% - 12em);
  float:left;
}
.dropdown{
  width: 12em;
}

</style>