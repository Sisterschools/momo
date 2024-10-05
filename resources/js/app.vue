<script setup>
import "external-svg-loader";
import { store } from './store.js'
import school from '../svg/school.svg';
import student from '../svg/student.svg'

import UserMenu from './components/UserMenuComponent.vue';

</script>

<script>
export default{
  methods:{
    logout(){
      store.token = null
      store.router.push("/")
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
      <li @click="logout">
        - Logout
      </li>
      <li>
        - Settings
      </li>
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
      <RouterLink to="/list-users">
        <svg 
          :data-src="student" 
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