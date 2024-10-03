<script setup>
import "external-svg-loader";
import { store } from './store.js'
import school from '../svg/school.svg';
</script>

<template>
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