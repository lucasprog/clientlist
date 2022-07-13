<template>
  <div id="login">
    <h1>
      ClientList
    </h1>
    <v-form v-model="valid" @submit="sigIn">
      <v-container>
        <v-row>
          <v-col
            cols="12"
            md="12"
          >
            <v-text-field
              v-model="form.email"
              :rules="emailRules"
              label="E-mail"
              required
              type="email"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>

          <v-col
            cols="12"
            md="12"
          >
            <v-text-field
              v-model="form.password"
              :rules="passwordRules"
              label="Senha"
              required
              type="password"
            ></v-text-field>
          </v-col>

        </v-row>

        <v-row>
          <v-col>
            <v-btn
              type="submit"
              block
              color="success"
              large
              :disabled="!valid || loading"
              :loading="loading"
            >Entrar</v-btn>
          </v-col>
        </v-row>

      </v-container>
    </v-form>
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
  </div>
</template>

<script>

  import {apiPost, setUser} from '../services/api-service';

  export default {
    data(){
      return {
        showAlert:false,
        alertText : "", 
        valid: false,
        loading : false,
        form : {
          email : null,
          password : null
        },
        emailRules: [
          v => !!v || 'Oops, por favor insira o E-mail!' ,
          v => /.+@.+/.test(v) || 'Oops, e-mail inválido!',
        ],
        passwordRules: [
          v => !!v || 'Oops, por favor informe a Senha',
        ]
      }
    },
    mounted(){
     

    },
    methods : {
      sigIn(e) {

        e.preventDefault();
        
        this.loading = true;

        apiPost("/api/login",this.form,true).then( (result) => {

          setUser({
            email : this.form.email,
            token : result.token
          });

          window.location.href = '/list';

        }).catch((error) => {
          this.showAlert = true;
          this.alertText = error.message;

          this.loading = false;
          this.valid = false;
          this.form.email = null;
          this.form.password = null;
        });
      }
    }
  }
</script>


<style lang="scss">
  #login {
    max-width: 400px;
    max-height: 400px;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border-radius: 10px;
    -webkit-box-shadow: 1px 2px 14px -8px #000;
    box-shadow: 1px 2px 14px -8px #000;
    padding: 16px;
    background-color: #222;

    h1{
      color: #fff; 
    }
    .v-form{
      width: 100%;
    }
  }

</style>
