<template>
  <div id="app">

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark" v-if="isLogged">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <router-link class="nav-link" :to="'/admin/paths'">Rutas</router-link>
        </li>
        <li class="nav-item">
          <router-link class="nav-link" :to="'/admin/redirects'">Redirecciones</router-link>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" @click.prevent="logout()">Logout</a>
        </li>        
      </ul>
    </nav>
    <router-view @login-event="isLogged = true"/>
  </div>
</template>

<script>

import LoginServices from './Services/Login';

export default {
  name: 'App',
  data () {
    return {
      isLogged:false
    }
  }, 
  mounted: function() {  
    const service = new LoginServices;
    this.isLogged = service.isLogged();
  },
  methods: {    
    logout:function(){
      const service = new LoginServices;
      service.logout();
      this.isLogged = false;
      this.$router.push('/admin/login');
    }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

.router-link-active{
  color: #fff !important;
}

.has-search .form-control {
    padding-left: 2.375rem;
}
.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
}


.ck-editor__editable {
  min-height: 350px;
}

</style>
