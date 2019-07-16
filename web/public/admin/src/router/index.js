import Vue from 'vue'
import Router from 'vue-router'

import HelloWorld from '@/pages/HelloWorld'
import Login from '@/pages/Login'
import Redirects from '@/pages/Redirects'
import Paths from '@/pages/Paths'

Vue.use(Router)

const isValid = (to, from, next )=>{
  if(localStorage.getItem('Tomy-SlimCMS-Token') !== null){
    next()
  }else{
    return next('/admin/login')
  }
}



export default new Router({
  mode: 'history',
  routes: [
    { path: '/admin/login', component: Login },
    { path: '/admin/', component: HelloWorld, beforeEnter: isValid },
    { path: '/admin/redirects', component: Redirects, beforeEnter: isValid },
    { path: '/admin/paths', component: Paths, beforeEnter: isValid },
    

    { path: '*', redirect: '/admin/login' }
  ]
})
