<template>
  <v-container>
    <v-row class="text-center">
      <v-col cols="12">
        <v-form v-model="validForm" @submit="submit">
          <v-container>
            <v-row>
               <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.name"
                  :rules="nameRules"
                  label="Nome"
                  required
                  type="text"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.email"
                  :rules="emailRules"
                  label="E-mail"
                  required
                  type="email"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="4">
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
                      :rules="birthdateRules"
                      required
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="birthdate"
                    label="Data de Nascimento"
                    :active-picker.sync="activePicker"
                    @change="save"
                  ></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
           
            <v-row>
              <v-col>
                <v-btn
                  type="submit"
                  block
                  color="success"
                  large
                  :disabled="!validForm || loading"
                  :loading="loading"
                >
                {{type=="store"?'Cadastrar':''}}
                {{type=="update"?'Atualizar':''}}
                </v-btn>
              </v-col>
            </v-row>

          </v-container>
        </v-form>
      </v-col>
    </v-row>
    <v-snackbar
      v-model="showAlert"
    >
      {{ alertText }}

      <template v-slot:action="{ attrs }">
        <v-btn
          color="white"
          text
          v-bind="attrs"
          @click="showAlert = false"
        >
          Fechar
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script>

  import {apiPost, apiPut} from '../services/api-service';

  export default {
    name: 'FormClient',
    props : ["type","data"],
    model : { 
      event : 'reload'
    },
    data(){
      return {
        validForm : false,
        loading: false,
        form : {
          id : null,
          name : null,
          email : null,
          birthdate : null
        },
        nameRules: [
          v => !!v || 'Oops, por favor insira o Nome!' ,
        ],
        emailRules: [
          v => !!v || 'Oops, por favor insira o E-mail!' ,
          v => /.+@.+/.test(v) || 'Oops, e-mail inválido!',
        ],  
        birthdateRules: [
          v => !!v || 'Oops, por favor insira a Data de Nascimento!' ,
        ], 
        activePicker: null,
        birthdate: null,
        date: null,
        menu: false, 
        showAlert:false,
        alertText : ""    
      }
    },
    mounted(){
      this.clearForm();
      if( this.type === 'update' ) {
        this.form = {
          id : this.data.id,
          name : this.data.name,
          email : this.data.email,
          birthdate : this.data.birthdate
        }

        this.birthdate = this.data.birthdate;
        this.date = `${this.$options.filters.date(this.form.birthdate)}`;
      }
    },
    methods : {
      submit(e){
        e.preventDefault();

        this.loading = true;

        if( this.type === 'store' ) {

          apiPost('/api/client/store',this.form).then((result) => {
            this.showMessage(result);

            if( !result.error ) {
              this.clearForm();
            }

            this.$emit('reload');
          }).catch((error) => {
            this.showMessage(error);
          });

        }

        if( this.type === 'update' ) {

          apiPut('/api/client/update',this.form).then((result) => {
            this.showMessage(result);

            if( !result.error ) {
              this.clearForm();
            }

            this.$emit('reload');
          }).catch((error) => {
            this.showMessage(error);
          });

        }
        
      },
      showMessage(c){
        this.loading = false;
        this.showAlert = true;
        this.alertText = c.message;
      },
      save (date) {

        this.form.birthdate = date;
      
        this.date = `${this.$options.filters.date(this.form.birthdate)}`;

        this.$refs.menu.save(date)
      },
      clearForm() {
        this.form.id = null;
        this.form.name = null;
        this.form.email = null;
        this.form.birthdate = null;
      }
    }
  }
</script>
