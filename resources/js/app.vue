<script setup>
import "external-svg-loader";
import { store } from './store.js'
import school from '../svg/school.svg';

import UserMenu from './components/UserMenuComponent.vue';

</script>

<script>
export default{
  methods:{
    logout(){
      store.token = null
    },
    addNew(){
      store.router.push( store.addNew )
    }
  }
}
</script>

<template>
  <div class="header">
    <user-menu  
      v-if="store.token"
      :user="store.userData ? store.userData.data : {}" 
      class="user-details"
    >
      <ul>
        <li @click="logout">
          - Logout
        </li>
        <li>
          - Settings
        </li>
      </ul>
    </user-menu>
  </div>
  <div>
    <nav v-if="store.token">
      <RouterLink to="/">
        <svg 
          :data-src="school" 
          class="icon" 
        />
      </RouterLink>&nbsp;
      <RouterLink to="/reset-password">
        <svg 
          :data-src="school" 
          class="icon"
        />
      </RouterLink>
      <div 
        v-if="store.isListComponent"
        @click="addNew"
      >
        [+]
      </div>
    </nav>
    <main>
      <RouterView 
        v-slot="{ Component }" 
      >
        <component 
          :is="Component"
        />
      </RouterView>
    </main>
  </div>
</template>