import { createApp } from 'vue';
import App from './app.vue'
import Layout from './components/LayoutComponent.vue';
import { createWebHashHistory, createRouter } from 'vue-router'

import ResetPassword from './components/ResetPasswordComponent.vue'

const routes = [
  { path: '/', component: Layout },
  { path: '/reset-password', component: ResetPassword },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})


createApp(App)
  // Register the v-focus directive
  .directive('focus', {
    mounted(el) {
      el.focus()
    }
  })
  .use(router)
  .mount('#app')