import { createApp } from 'vue';
import { store } from './store.js'
import App from './app.vue'
import Layout from './components/LayoutComponent.vue';
import ListSchools from './components/ListSchoolsComponent.vue';
import ListUsers from './components/ListUsersComponent.vue';
import ViewSchool from './components/ViewSchoolComponent.vue';
import ViewUser from './components/ViewUserComponent.vue';
import { createWebHashHistory, createRouter } from 'vue-router'

import ResetPassword from './components/ResetPasswordComponent.vue'

const routes = [
    { path: '/', component: Layout },
    { path: '/reset-password', component: ResetPassword },
    { path: '/list-schools', component: ListSchools },
    { path: '/view-school/:id', name:'viewschool', component: ViewSchool },
    { path: '/schools/add', name:'addschool', component: ViewSchool },
    { path: '/list-users', component: ListUsers},
    { path: '/view-user/:id', name:'viewuser', component: ViewUser }
  ]  

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

store.router = router;

createApp(App)
  // Register the v-focus directive
  .directive('focus', {
    mounted(el) {
      el.focus()
    }
  })
  .use(router)
  .mount('#app')