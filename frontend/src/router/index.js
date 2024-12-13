import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/main_page',
      name: 'main_page',
      component: () => import('../components/mainpage/MainPage.vue'),
    },
    {
      path: '/',
      name: 'login',
      component: () => import('../components/loginAndRegist/LoginPage.vue'),
    },
    {
      path: '/regist',
      name: 'regist',
      component: () => import('../components/loginAndRegist/RegistUser.vue'),
    },
  ],
})

export default router
