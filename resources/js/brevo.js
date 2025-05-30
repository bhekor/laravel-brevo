import { createApp } from 'vue';
import BrevoStats from './components/BrevoStats.vue';
import BrevoWebhookLogs from './components/BrevoWebhookLogs.vue';

const Brevo = {
    install(app, options = {}) {
        app.component('BrevoStats', BrevoStats);
        app.component('BrevoWebhookLogs', BrevoWebhookLogs);
        
        app.provide('brevo', {
            apiBaseUrl: options.apiBaseUrl || '/brevo-api',
        });
    }
};

export default Brevo;

// Auto-install when used directly in browser
if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(Brevo);
}