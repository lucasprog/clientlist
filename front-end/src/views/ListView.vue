<template>
  <div id="list">
    
    <div class="box-search">
      <h2>Buscar Clientes</h2>
      <v-form  @submit="search">
        <v-container>
          <v-row>
            <v-col
              cols="12"
              md="3"
            >
              <v-text-field
                v-model="formSearch.name"
                label="Nome"
                type="text"
              ></v-text-field>
            </v-col>
            <v-col
              cols="12"
              md="3"
            >
              <v-text-field
                v-model="formSearch.email"
                label="E-mail"
                type="email"
              ></v-text-field>
            </v-col>
            <v-col
              cols="12"
              md="3"
            >

            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="date"
                  label="Data de Nascimento"
                  prepend-icon="mdi-calendar"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>
              </template>
               <v-date-picker
                v-model="birthdate"
                range
                label="Data de Nascimento"
                :active-picker.sync="activePicker"
                @change="save"
              ></v-date-picker>
            </v-menu>
             
            </v-col>

            <v-col
              cols="12"
              md="2"
            >
              <v-select
                v-model="orderBy"
                :items="items"
                label="Ordenar Por"
                :hint="`${orderBy.text}`"
                item-text="text"
                item-value="value"
                persistent-hint
                return-object
                single-line
              ></v-select>
            </v-col>
            <v-col
              cols="12"
              md="2"
            >
              <v-select
                v-model="perPage"
                :items="items2"
                label="Por página"
                :hint="`${perPage.text}`"
                item-text="text"
                item-value="value"
                persistent-hint
                return-object
                single-line
              ></v-select>
            </v-col>

            <v-col cols="12"
            md="3">
              <v-btn
                type="submit"
                color="success"
                large
                :disabled="loadingSearch"
                :loading="loadingSearch"
              >Buscar</v-btn>
              <v-btn
                class="ml-2"
                type="button"
                color="primary"
                large
                @click="showFormClient()"
              >Novo Cliente</v-btn>
            </v-col>
          </v-row>

        </v-container>
      </v-form>
    </div>
    
    <v-data-table
      :headers="headers"
      :items="clientList" 
      :items-per-page="perPage.value"
      :loading="loadingDataTable"
      class="elevation-1"
    >
      <template v-slot:[`item.birthdate`]="{ item }">
        {{ item.birthdate | date }}
      </template>

      <template v-slot:[`item.edit`]="{ item }">
        <v-btn
          elevation="2"
          fab
          small
          color="cyan" 
          @click="showFormClient(item)"
        >
          <v-icon>
            mdi-pencil
          </v-icon>
        </v-btn>
      </template>

      <template v-slot:[`item.delete`]="{ item }">
         <v-btn
          elevation="2"
          fab
          small
          color="error"
          @click="showDialogDelete(item)"
        >
          <v-icon>
            mdi-delete
          </v-icon>
        </v-btn>
      </template>

    </v-data-table>

    <v-dialog class="formStoreClient" v-model="formStoreClient.showForm">
      <v-card>
        <v-card-title class="text-h5">
          {{formStoreClient.title}}
        </v-card-title>
        <form-client :type="formStoreClient.typeFormClient" :data="formStoreClient.data" @reload="search()"></form-client>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dialogDelete"
      persistent
      max-width="290"
    >
      <v-card>
        <v-card-title class="text-h5">
          Excluír Cliente!
        </v-card-title>
        <v-card-text>
          Deseja realmente Excluír esse Cliente?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            small
            color="error"
            @click="dialogDelete = false"
          >
            Não
          </v-btn>
          <v-btn
            small
            color="primary"
            @click="deleteClient()"
          >
            Sim
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    
  </div>
</template>

<script>

  import {apiGet, apiDelete} from '../services/api-service';

export default {
  data(){
    return {
      headers: [
        {
          text: 'ID',
          align: 'start',
          sortable: false,
          value: 'id',
        },
        { text: 'Nome', value: 'name' },
        { text: 'E-mail', value: 'email' },
        { text: 'Data Nascimento', value: 'birthdate' }, 
        { text: 'Editar', value:"edit" },
        { text: 'Excluír', value:"delete"},
      ],
      clientList : [],
      formSearch: {
        name : null,
        email : null,
        birthdate: {
          from : null,
          to : null
        }
      },

      orderBy: { text: 'ID', value:'id'},

      perPage: { text: '15', value:15},

      items: [
        { text: 'ID', value: 'id' },
        { text: 'Nome', value: 'name' },
        { text: 'E-mail', value: 'email' },
        { text: 'Data de Nascimento', value: 'birthdate' }
      ],

      items2: [
        { text: '05', value: 5 },
        { text: '10', value: 10},
        { text: '15', value: 15},
      ],

      activePicker: null,
      birthdate: null,
      date: null,
      menu: false,

      loadingSearch: false,
      loadingDataTable:false,

      formStoreClient : {
        title : null,
        typeFormClient : null,
        showForm : false,
        data : null
      },

      dialogDelete : false,
      idDelete : null
    }
  },
  mounted(){
    this.search();
  },
  methods: {
    getClients(params = '') {
      this.loadingDataTable = true;

      apiGet("/api/client/read",params).then((result) => {
        this.loadingDataTable = false;
        this.clientList = result.data;
      }).catch((error) => {
        this.loadingDataTable = false;
        this.showAlert = true;
        this.alertText = error.message;
      });    
    },
    search(e) {

      if( e ) {
        e.preventDefault();
      }

      this.formStoreClient.showForm = false;

      let params = '';
      
      if( this.formSearch.name ) {
        params += `name=${this.formSearch.name},`;
      } 

      if( this.formSearch.email ) {
        params += `email=${this.formSearch.email},`;
      } 

      if( this.formSearch.birthdate.from ) {
        params += `birthdate_from=${this.formSearch.birthdate.from},`;
      } 

      if( this.formSearch.birthdate.to ) {
        params += `birthdate_to=${this.formSearch.birthdate.to},`;
      } 

      params += `orderBy=${this.orderBy.value},`;
      params += `perPage=${this.perPage.value}`;

      params = params.split(',').join('&&');

      params = `?${params}`;
      
      this.getClients(params);

    },
    showFormClient(data) {     
      console.log('data',data) 
      if( data ) {
        this.formStoreClient.title = 'Atualizar Cliente';
        this.formStoreClient.typeFormClient = 'update';
        this.formStoreClient.data = data;
      }else{
        this.formStoreClient.title = 'Novo Cliente';
        this.formStoreClient.typeFormClient = 'store';
      }
      this.formStoreClient.showForm = true;
    },
    save (date) {
      
      this.formSearch.birthdate.from = date[0];
      this.formSearch.birthdate.to = date[1];

      this.date = `${this.$options.filters.date(this.formSearch.birthdate.from)} - ${this.$options.filters.date(this.formSearch.birthdate.to)}`;

      this.$refs.menu.save(date)
    },
    showDialogDelete(data) {
      this.idDelete = data.id;
      this.dialogDelete = true;
    },
    deleteClient() {
      this.dialogDelete = false;
      this.loadingDataTable = true;
      
      apiDelete(`/api/client/delete/${this.idDelete}`).then((result) =>{
        this.idDelete = null;

        this.showAlert = true;
        this.alertText = result.message;

        this.search();
        
      }).catch((error) => {
        this.loadingDataTable = false;
        this.showAlert = true;
        this.alertText = error.message;
      })
    }
  }
}
</script>
<style lang="scss">
  #list{
    max-width: 1200px;
    width: 100%;
    display: flex;
    grid-gap: 16px;
    flex-direction: column;

    .box-search{
      width: 100%;
      height: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      border-radius: 10px;
      -webkit-box-shadow: 1px 2px 14px -8px #000;
      box-shadow: 1px 2px 14px -8px #000;
      padding: 16px;
      background-color: #222;

      h2{
        color: #fff; 
        text-align: left;
        display: block;
        width: 100%;
        padding-left: 8px;  
      }
      .v-form{
        width: 100%;
      }
    }

    .formStoreClient{
      width: 100%;
      max-width: 600px;
    }
  }
</style>