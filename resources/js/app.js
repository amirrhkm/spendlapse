import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import localizationService from './services/LocalizationService';

const app = createApp(App);

// Provide localization service globally
app.config.globalProperties.$t = localizationService.t.bind(localizationService);

app.use(router);
app.mount('#app');
