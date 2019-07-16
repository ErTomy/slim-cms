import axios from 'axios';

axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('Tomy-SlimCMS-Token');

export default class DataServices {

    async get( file ) {        
        try{            
            let response = await axios.get( '/api/' + file);    
            return response.data;  
        }catch(err){
            console.log(err);
        }
    }
    
    async set( file,  json ) {        
        try{            
            let response = await axios.post( '/api/' + file, {
                data: (file.indexOf('.html') > 0)?json:JSON.stringify(json)
            });    
            return response.data.result;  
        }catch(err){
            console.log(err)
        }        
    }
        
    delete( file ) {        
        try{            
            axios.delete( '/api/' + file);                
        }catch(err){
            console.log(err)
        }
    }
  
}