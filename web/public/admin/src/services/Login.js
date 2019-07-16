import axios from 'axios';

export default class LoginServices {

    async login( username, password) {
        if(username === null || username.length == 0) return 'Debe indicar el usuario';
        if(password === null || password.length == 0) return 'Debe indicar la contraseña';
        
        try{            
            let response = await axios.post( '/api-login', {
                username: username,
                password: password
            });              

            if(response.data.error){
                return response.data.message;                
            }else{
                localStorage.setItem('Tomy-SlimCMS-Token', response.data.token);
                return true;                
            }  
            
        }catch(err){
            console.log(err)
        }
    }

    isLogged() {
        return (localStorage.getItem('Tomy-SlimCMS-Token') !== null);
    }

    logout() {
        localStorage.removeItem('Tomy-SlimCMS-Token');
    }
  
}