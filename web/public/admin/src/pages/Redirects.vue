<template>
  <div class="container">
    <div class="alert alert-danger" role="alert" v-if="error!==null">
      {{error}} 
    </div>
    <div class="alert alert-success" role="alert" v-if="saved!==null">
      {{saved}}
    </div>

    <div class="row" v-if="newPath===null">
      <div class="form-group has-search">
        <span class="fa fa-search form-control-feedback"></span>
        <input type="text" class="form-control" placeholder="Search" v-model="searched" @keyup="search()">
      </div>
    </div>

    <div class="row" v-if="newPath===null">
      <div class="col-sm text-left"><b>Origen</b></div>
      <div class="col-sm text-left"><b>Destino</b></div>
      <div class="col-3"><b>#</b></div>
    </div>  

    <div v-if="newPath===null">
      <div  v-for="(path, index) in filtered" :key="index">
        <div class="row record" v-if="view(index)">          
          
          <div class="col-sm text-left">{{index}}</div>

          <div class="col-sm text-left" v-if="selected==index">            
              <input type="text" class="form-control" id="destino" placeholder="/ruta" v-model="value" @keypress="error = null">                          
          </div>
          <div class="col-sm text-left" v-else>{{path}}</div>   

          <div class="col-4 botonera" v-if="selected==index">
            <a href="#" @click.prevent="update()"><i class="far fa-edit"></i> Modificar</a>
            <a href="#" @click.prevent="deletee()"><i class="far fa-trash-alt"></i> Borrar</a>
            <a href="#" @click.prevent="selected=null"><i class="far fa-window-close"></i> Cancelar</a>
          </div>
          <div class="col-3" v-else>
            <a href="#" @click.prevent="select(index)">
              <i class="far fa-edit"></i> Modificar
            </a>
          </div>


        </div>
      </div>  

      <div class="row paginacion" v-if="pagination.pages > 1">
        <div class="col">
          <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#" v-if="pagination.page > 1" @click.prevent="goto(pagination.page-1)">Anterior</a></li>
            <li class="page-item" v-for="i in (1, pagination.pages) " :key="i" :class="[(pagination.page == i)?'active':'']">
                <a class="page-link" href="#" @click.prevent="goto(i)">{{i}}</a>
            </li>
            <li class="page-item"><a class="page-link" href="#" v-if="pagination.page < pagination.pages" @click.prevent="goto(pagination.page+1)">Siguiente</a></li>
          </ul> 
        </div>  
      </div>

      <div class="row paginacion">
        <div class="col">
          <div class="form-group">
            <button class="btn btn-primary float-left" @click.prevent="add()">Añadir nueva redirección</button>
            <button class="btn btn-primary float-right" @click.prevent="save()">Guardar cambios</button>
          </div>       
        </div>  
      </div>
    </div>
    <div v-else>
      <div class="row"> 
        <div class="col text-left">
          <form>
              <div class="form-group">
                <label for="origen">Origen:</label>
                <input type="text" class="form-control" id="origen" placeholder="/ruta" v-model="newPath.from" @keypress="error = null">            
              </div>
              <div class="form-group">
                <label for="destino">Destino:</label>
                <input type="text" class="form-control" id="destino" placeholder="/ruta" v-model="newPath.to" @keypress="error = null">            
              </div>          
              <div class="form-group">
                <button class="btn btn-primary" @click.prevent="addSave()">Añadir</button>
                <button class="btn btn-danger float-right" @click.prevent="newPath=null;error = null;">Cancelar</button>
              </div>          
          </form>         
        </div>       
      </div>      
    </div>
  </div>
</template>

<script>
import DataServices from '../services/Data';

export default {
  name: 'Redirects',
  data () {
    return {
      pagination:{
        page:1,
        records:0,
        itemsPerPage:10,
        pages:10,
        start:0,
        end:0        
      },
      searched:'',
      selected:null,  
      value:'',    
      newPath:null,
      error:null,
      saved:null,
      paths:{},
      filtered:{}   
    }
  },
  mounted: function(){
    const service = new DataServices();
    service.get('redirecciones.json').then((result)=> {
      this.paths = result;
      this.search();
    });
  },  
  
  methods:{    
    view:function(index){
      let num = 0;
      for(let from in this.filtered){
        num++;
        if(from == index) break;
      }   
      return (num >= this.pagination.start && num <= this.pagination.end );
    },
    goto:function(i){
      this.pagination.page = i;
      this.pagination.start = ((this.pagination.page - 1) * this.pagination.itemsPerPage) + 1;
      this.pagination.end = this.pagination.start + this.pagination.itemsPerPage -1;
      if(this.pagination.end > this.pagination.records) this.pagination.end = this.pagination.records;
    },
    search: function(){
      this.filtered = {};
      this.pagination.records = 0;
      for(let from in this.paths){
        if(this.searched.length == 0 || from.indexOf(this.searched) >= 0 || this.paths[from].indexOf(this.searched) >= 0){
          this.filtered[from] = this.paths[from];
          this.pagination.records++;          
        }          
      }
      this.pagination.pages = Math.ceil( this.pagination.records / this.pagination.itemsPerPage );
      this.goto(1);
    },
    select: function(index) {
      this.value = this.paths[index];
      this.selected=(this.selected==index)?null:index;
    },
    add:function(){
      this.newPath = {
        from:'',
        to:''
      }      
    },
    addSave:function(){
      if(this.validate(this.newPath)){
        this.paths[this.newPath.from] = this.newPath.to;
        this.newPath = null;     
        this.search();   
      }
    },
    update: function(){
      if(this.validate({from:'/*', to:this.value})){
        this.paths[this.selected] = this.value;  
        this.selected = null;
        this.search();
      }
    },
    deletee: function(){
      this.$delete(this.paths, this.selected);      
      this.error = null;
      this.search();
    },
    validate:function(path){
      this.error = null;
      if(path.from.length == 0){
        this.error = 'Debe indicar la ruta de origen';
        return false;
      } 
      if(path.to.length == 0){
        this.error = 'Debe indicar la ruta de destino';
        return false;
      }
      if(path.from[0] != '/'){
        this.error = 'La ruta de origen debe empezar con "/"';
        return false;
      }
      if(path.to[0] != '/'){
        this.error = 'La ruta de destino debe empezar con "/"';
        return false;
      }
      if(this.paths.hasOwnProperty(path.from)){
        this.error = 'Ya existe una redirección de esta ruta';
        return false;
      }
      if(path.from == path.to){
        this.error = 'La ruta de origen y destino no pueden ser la misma';
        return false;
      }
      return true;
    },
    save: function(){
      const service = new DataServices();
      let _self = this;
      service.set('redirecciones.json', this.paths).then((result)=> {
        if(result){
          this.saved = 'Cambios guardados';
          setTimeout(function(){ _self.saved=null; }, 3000);
        }else{
          this.error = 'No se han podido salvar los cambios';
        }        
      });
    }

  }
}
</script>

<style scoped>
  .detail { background-color: #eeeeee; padding: 15px;}
  .container, .paginacion { padding-top: 15px;}  
  .record { border-top: solid 1px #ccc; padding: 5px 0;}
  .botonera a {margin-left:15px;}
</style>