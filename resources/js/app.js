/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

require('./registerServiceWorker');
window.Vue = require('vue');

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { BootstrapVue } from 'bootstrap-vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCog, faQuestionCircle, faUsers, faUserEdit, faBarcode, faUserMinus, faShareAlt, faChevronDown, faChevronUp, faExternalLinkAlt, faPrint, faCode, faTruck, faShoppingCart, faKey, faDesktop, faClipboardList, faPuzzlePiece, faBoxOpen, faEdit, faMinus, faCheckCircle, faTimesCircle, faEnvelopeOpenText, faListUl, faTrash, faMagic } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import VueTippy, { TippyComponent } from "vue-tippy";
import Snotify from 'vue-snotify';
import VueCountdownTimer from 'vuejs-countdown-timer';

library.add(faCog);
library.add(faQuestionCircle);
library.add(faUsers);
library.add(faUserEdit);
library.add(faUserMinus);
library.add(faBarcode);
library.add(faShareAlt);
library.add(faChevronDown);
library.add(faChevronUp);
library.add(faExternalLinkAlt);
library.add(faPrint);
library.add(faCode);
library.add(faTruck);
library.add(faShoppingCart);
library.add(faKey);
library.add(faDesktop);
library.add(faClipboardList);
library.add(faPuzzlePiece);
library.add(faBoxOpen);
library.add(faEdit);
library.add(faMinus);
library.add(faCheckCircle);
library.add(faTimesCircle);
library.add(faEnvelopeOpenText);
library.add(faListUl);
library.add(faTrash);
library.add(faMagic);
Vue.config.productionTip = false;

Vue.use(VueCountdownTimer);

Vue.use(Loading);
Vue.use(require('vue-moment'));

// Install BootstrapVue
Vue.use(BootstrapVue);
Vue.use(VueTippy);
Vue.use(Snotify, {
    global: {
        newOnTop: false,
    },
    toast: {
        position: "centerBottom",
        icon: false,
        showProgressBar: false,
        timeout: 1000,
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue mixins. It will recursively scan this directory for the Vue
 * mixins and automatically register them with their "basename".
 *
 * Eg. ./mixins/ExampleComponent.vue -> <example-mixins></example-mixins>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.mixins(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Third Party mixins
 */
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component("tippy", TippyComponent);

/**
 * Application mixins
 */
Vue.component('passport-clients', require('./components/Settings/OauthClients.vue').default);
Vue.component('passport-authorized-clients', require('./components/Settings/AuthorizedClients.vue').default);
Vue.component('passport-personal-access-tokens', require('./components/Settings/PersonalAccessTokens.vue').default);

Vue.component('autopilot-packlist-page', require('./components/AutopilotPacklistPage.vue').default);
Vue.component('dpd-configuration', require('./components/Settings/DpdConfiguration').default);



Vue.component('create-topic', require('./components/misc/CreateTopic.vue').default);
Vue.component('subscribe-topic', require('./components/misc/SubscribeTopic.vue').default);
Vue.component('missing-table', require('./components/misc/Missing.vue').default);
Vue.component('products-table', require('./components/ProductsPage.vue').default);
Vue.component('orders-table', require('./components/OrdersPage.vue').default);
Vue.component('api2cart-configuration', require('./components/Settings/Api2cartConnections.vue').default);
Vue.component('rmsapi-configuration', require('./components/Settings/RmsapiiConfiguration.vue').default);
Vue.component('packlist-configuration-modal', require('./components/Packlist/FiltersModal.vue').default);
Vue.component('autopilot-packsheet-page', require('./components/AutopilotPacksheetPage.vue').default);
Vue.component('packlist-table-entry', require('./components/Packlist/PacklistEntry.vue').default);
Vue.component('apt-configuration-modal', require('./components/Widgets/APT/ConfigurationModal.vue').default);
Vue.component('user-table', require('./components/UsersPage.vue').default);
Vue.component('printnode-configuration', require('./components/Settings/PrintNode.vue').default);
Vue.component('printer-configuration', require('./components/Settings/PrintersConfiguration.vue').default);
Vue.component('user-courier-integration-select', require('./components/Settings/UsersCourierIntegrationSelect.vue').default);
Vue.component('picks-table', require('./components/PicklistPage.vue').default);
Vue.component('auto-pilot-tuning-section', require('./components/Settings/AutoPilotTuningSection.vue').default);
Vue.component('maintenance-section', require('./components/Settings/MaintenanceSection.vue').default);
Vue.component('module-configuration', require('./components/Settings/ModuleConfiguration.vue').default);
Vue.component('order-status-table', require('./components/Settings/OrderStatusTable.vue').default);
Vue.component('mail-template-table', require('./components/Settings/MailTemplateTable.vue').default);
Vue.component('navigation-menu-table', require('./components/Settings/NavigationMenuTable.vue').default);
Vue.component('automation-table', require('./components/Settings/AutomationTable.vue').default);
Vue.component('api', require('./mixins/api').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding mixins to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
