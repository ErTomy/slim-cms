<template>
  <div class="login-page">
    <div class="form">    
      <form class="login-form">
        <input type="text" placeholder="username" v-model="username" @keyup="error=null"/>
        <input type="password" placeholder="password" v-model="password" @keyup="error=null"/>
        <button @click.prevent="onSubmit" class="btn btn-primary">login</button> 

        <div class="alert alert-danger" role="alert" v-if="error!==null">
          {{error}} 
        </div>
        
      </form>
    </div>
  </div>
</template>

<script>
import LoginServices from '../Services/Login';

export default {
  name: 'Login',
  data () {
    return {
      username: null,
      password: null,
      error:null
    }
  },
  methods: {
    onSubmit: function () {
      this.error = null;
      const service = new LoginServices;
      service.login(this.username, this.password)
             .then((result) => {
        if(result === true){
          this.$emit('login-event');
          this.$router.push('/admin/');
        }else{
          this.error = result;
        }          
      });        
    }
  }
}
</script>

<style scoped>
.alert{
  margin-top:10px;
}
.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}

.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}

</style>
