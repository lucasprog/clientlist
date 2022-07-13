import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

import colors from 'vuetify/lib/util/colors'

import moment from 'moment';

Vue.use(Vuetify);

Vue.filter('date', (value) => {
  if (value) {
      return moment(String(value)).format('DD/MM/YYYY');
  }
});

Vue.filter('datetime', (value) => {
  if (value) {
      return moment(String(value)).format('DD/MM/YYYY HH:mm');
  }
});

export default new Vuetify({
  theme: { 
    defaultTheme: 'dark',
    dark: true,
    themes: {
      light: {
        primary: colors.purple,
        secondary: colors.grey.darken1,
        accent: colors.shades.black,
        error: colors.red.accent3,
      },
      dark: {
        primary: colors.blue.lighten3,
      },
    },
  }
});
