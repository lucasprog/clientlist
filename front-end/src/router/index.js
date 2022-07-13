import Vue from 'vue'
import VueRouter from 'vue-router'
import ListView from '../views/ListView.vue'
import LoginView from '../views/LoginView.vue'
import FormClient from '../components/FormClient.vue'

Vue.use(VueRouter)

Vue.component('form-client',FormClient)

const guard = (to, from, next) => {

  const user = localStorage.getItem('userLogged');

  const isAuthenticated= user?user:false;

  if(isAuthenticated) {
    next();
  }else{
    next('/login');
  }

}

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: {title: 'Login'}
  },
  {
    path: '/list',
    name: 'list',
    beforeEnter : guard,
    component: ListView,
    meta: {title: 'List'}
  },
  {
    path: '/',
    name: 'home',
    redirect : '/list'
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
