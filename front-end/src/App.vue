<template>
  <v-app id="app" theme="dark">
    <router-view/>
  </v-app> 
</template>

<style lang="scss">
#app {

  .v-application--wrap{

    font-family: Poppins, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    width: 100%;
    height: auto;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;

    .v-btn{
      .v-btn__content{
        font-family: Poppins, sans-serif;
        text-transform: initial;
        font-weight: bold;
      }
    }

  }
}

</style>
