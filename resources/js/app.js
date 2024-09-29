import { createApp } from 'vue';
import Layout from './components/LayoutComponent.vue';

createApp({})
  // Register the v-focus directive
  .directive('focus', {
    mounted(el) {
      el.focus()
    }
  })
  .component('layout', Layout)
  .mount('#app')