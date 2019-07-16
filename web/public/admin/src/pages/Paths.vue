<template>
  <div class="container">
    <div class="alert alert-danger" role="alert" v-if="error!==null">
      {{error}} 
    </div>
    <div class="alert alert-success" role="alert" v-if="saved!==null">
      {{saved}}
    </div>

    <div class="row" v-if="editPage===null">
      <div class="form-group has-search">
        <span class="fa fa-search form-control-feedback"></span>
        <input type="text" class="form-control" placeholder="Search" v-model="searched" @keyup="search()">
      </div>
    </div>

    <div class="row" v-if="editPage===null">
      <div class="col-1"><b>#</b></div>
      <div class="col-sm text-left"><b>Ruta</b></div>
      <div class="col-sm text-left"><b>Title</b></div>
      <div class="col-sm text-left"><b>Template</b></div>
    </div>  

    <div v-if="editPage===null">
      <div  v-for="(path, index) in filtered" :key="index">
        <div class="row" v-if="view(index)">

          <div class="col-1">
            <a href="#" @click.prevent="select(index)">
              <i class="far" :class="[selected==index ? 'fa-minus-square' : 'fa-plus-square']"></i>
            </a>
          </div>
          <div class="col-sm text-left">{{index}}</div>
          <div class="col-sm text-left">{{path.params.title}}</div>
          <div class="col-sm text-left">{{path.template}}</div>          
        </div>
      </div>  

      <div class="row botonera" v-if="pagination.pages > 1">
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

      <div class="row botonera">
        <div class="col">
          <div class="form-group">
            <button class="btn btn-primary float-left" @click.prevent="add()">Añadir nueva página</button>            
          </div>       
        </div>  
      </div>
    </div>
    <div v-else>
      <div class="row"> 
        <div class="col text-left">
          <form>
             <table width="100%">
               <tr>
                 <td>Ruta:</td>
                 <td colspan="2">
                   <input type="text" class="form-control" id="origen" placeholder="/ruta" v-model="editPage.from" @keypress="error = null"> 
                  </td>
               </tr>
               <tr>
                 <td>Plantilla:</td>
                 <td colspan="2">
                   <select v-model="editPage.template" class="custom-select">
                      <option v-for="(template,index) in templates" :key="index" :value="template">{{template}}</option>
                    </select>
                 </td>
               </tr>
               <tr>
                 <td>Sitemap:</td>
                 <td>
                   <input type="text" class="form-control" placeholder="valor de 0 a 100" v-model="editPage.sitemap.priority" @keypress="error = null"> 
                 </td>  
                 <td>
                      <select v-model="editPage.sitemap.changefreq" class="custom-select">
                        <option value="always">always</option>
                        <option value="hourly">hourly</option>
                        <option value="daily">daily</option>
                        <option value="weekly">weekly</option>
                        <option value="monthly">monthly</option>
                        <option value="yearly">yearly</option>
                        <option value="never">never</option>
                        <option value="">No cargar en el sitemap</option>
                      </select>                    
                 </td>
               </tr>
               <tr>
                 <td colspan="3" align="center"><h5>Parámetros</h5></td>
               </tr>
               <tr v-for="(param, key) in editPage.params" :key="key">
                 <td>{{key}}</td>                 
                 <td>
                   <input type="text" v-model="editPage.params[key]" class="form-control">
                 </td>
                 <td>
                   <a href="#" @click.prevent="deleteParam(key)" v-if="key != 'title' && key != 'description' && key != 'keywords'"><i class="far fa-trash-alt"></i> Borrar</a>
                 </td>                 
               </tr>
                <tr>
                  <td colspan="2"><a href="#" @click.prevent="showModal = true">Añadir parametro</a></td>
                </tr>
                <tr>
                 <td colspan="3" align="center"><h5>Contenido HTML</h5></td>
               </tr>
               <tr>
                 <td colspan="3">
                   <ckeditor :editor="editor" v-model="editPage.contentHTML" :config="editorConfig"></ckeditor>
                 </td>
               </tr>
             </table> 


              <div class="form-group" v-if="selected == null">
                <button class="btn btn-primary" @click.prevent="addSave()">Añadir</button>
                <button class="btn btn-secundary float-right" @click.prevent="editPage=null;error = null;">Cancelar</button>
              </div>          
              <div class="form-group text-center" v-else>
                <button class="btn btn-primary float-left" @click.prevent="addSave()">Guardar</button>
                <button class="btn btn-danger" @click.prevent="showModalBorrar=true">Borrar</button>
                <button class="btn btn-secundary float-right" @click.prevent="editPage=null;error = null;selected=null;">Cancelar</button>
              </div>          



          </form>         
        </div>       
      </div>      
    </div>


    <div v-if="showModal">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">

          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Nuevo parámetro</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true" @click="showModal = false">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <input v-model="paramName"  class="form-control">
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" @click="showModal = false">Cancelar</button>
                      <button type="button" class="btn btn-primary" @click="addParam()">Añadir</button>
                  </div>
              </div>
          </div>

          </div>
        </div>
      </transition>
    </div>

    <div v-if="showModalBorrar">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">

          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-body">
                    ¿Seguro que quieres borrar la página?
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" @click="showModalBorrar = false">Cancelar</button>
                      <button type="button" class="btn btn-danger" @click="deletee()">Borrar</button>
                  </div>
              </div>
          </div>

          </div>
        </div>
      </transition>
    </div>



  </div>
</template>

<script>
import DataServices from '../services/Data';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
  name: 'Paths',
  data () {
    return {
      pagination:{
        page:1,
        records:0,
        itemsPerPage:10,
        pages:0,
        start:0,
        end:0        
      },
      searched:'',
      selected:null,  
      value:'',    
      editPage:null,
      error:null,
      saved:null,
      paths:{},
      filtered:{},
      templates:[],
      paramName:'' ,
      showModal:false,
      showModalBorrar:false,
      editor: ClassicEditor,
      editorConfig:{
        toolbar: [ 'heading', '|', 'bold', 'italic', '|', 'bulletedList', 'numberedList' , '|', 'link', 'blockQuote', 'insertTable'],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
            ]
        }
      }  
    }
  },
  mounted: function(){
    const service = new DataServices();
    service.get('rutas.json').then((result)=> {
      this.paths = result;
      this.search();
    });
    service.get('templates').then((result)=> {
      this.templates = result;
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
        if(this.searched.length == 0 || from.indexOf(this.searched) >= 0 || this.paths[from].params.title.indexOf(this.searched) >= 0){
          this.filtered[from] = this.paths[from];
          this.pagination.records++;          
        }          
      }
      this.pagination.pages = Math.ceil( this.pagination.records / this.pagination.itemsPerPage );
      this.goto(1);
    },
    select: function(index) {
      if(index !== null && this.paths[index].content.length > 0){
        const service = new DataServices();
        service.get(this.paths[index].content).then((result)=> {
          this.editPage = this.paths[index];
          this.editPage.from = index;
          this.editPage.contentHTML = result;
          this.selected=(this.selected==index)?null:index;    
        });
      }else{
        this.editPage = this.paths[index];
        this.editPage.from = index;
        this.editPage.contentHTML = '';
        this.selected=(this.selected==index)?null:index;
      }     
    },
    add:function(){
      this.selected = null;
      this.editPage = {
        from:'',
        template:'',
        content:'',
        contentHTML:'',
        sitemap:{
            priority:'',
            changefreq:''
        },
        params:{
            title:'',
            description:'',
            keywords:''
        }
      }      
    },
    addParam:function(){
      this.error = null;
      if(this.editPage.params.hasOwnProperty(this.paramName)){
        this.error = 'Ya hay un parametro con ese nombre';
      }else{
        this.editPage.params[this.paramName] = '';
        this.paramName = '';
      }
      this.showModal = false;
    },
    deleteParam:function(param){
      this.$delete(this.editPage.params, param);    
    },
    addSave:function(){
      if(this.validate(this.editPage)){
        const service = new DataServices();
        var from = this.editPage.from;

        this.paths[from] = this.editPage;
        this.$delete(this.paths[from], 'from');

        if(this.editPage.contentHTML !== undefined){
          if(this.selected === null || !this.paths[this.selected].hasOwnProperty('content') || this.paths[this.selected].content.length == 0){
            var rand = function() { return Math.random().toString(36).substr(2); };
            var token = function() { return rand() + rand() + rand();  };
            this.paths[from].content = token() + '.html';          
          }else if(this.selected != from){
            this.$delete(this.paths, this.selected);
          }           
          service.set(this.paths[from].content, this.editPage.contentHTML).then((result)=> {
            if(result){
              delete this.paths[from].contentHTML;
              this.editPage = null; 
              this.save();    
              this.search();        
            }else{
              this.error = 'No se han podido salvar los cambios';
            }        
          }); 
        }else{
          this.editPage = null;     
          this.search();        
        }          
      }
    },    
    deletee: function(){

      if(this.paths[this.selected].hasOwnProperty('content') && this.paths[this.selected].content.length > 0){
        const service = new DataServices();
        service.delete(this.paths[this.selected].content);
      }

      this.$delete(this.paths, this.selected);      
      this.showModalBorrar = false
      this.error = null;
      this.selected = null;
      this.editPage = null;   
      this.save();  
      this.search();
    },
    validate:function(path){

      this.error = null;
      if(path.from.length == 0){
        this.error = 'Debe indicar la ruta de origen';
        return false;
      } 
      if(path.from[0] != '/'){
        this.error = 'La ruta de origen debe empezar con "/"';
        return false;
      }
      
      if(path.from != this.selected && this.paths.hasOwnProperty(path.from)){
        this.error = 'Ya existe una página con esta ruta';
        return false;
      }
      
      if(path.template.length == 0){
        this.error = 'Debe indicar la plantilla';
        return false;
      }

      if (isNaN(path.sitemap.priority) || path.sitemap.priority < 0 || path.sitemap.priority > 100){
        this.error = 'La prioridad en el sitemap debe ser entre 0 y 100';
        return false;
      }

      if(path.params.title.length == 0){
        this.error = 'Debe indicar el title';
        return false;
      }      
      
      return true;
    },
    save: function(){
      const service = new DataServices();
      let _self = this;
      service.set('rutas.json', this.paths).then((result)=> {
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
  .container, .botonera { padding-top: 15px;}  

  .modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}


</style>