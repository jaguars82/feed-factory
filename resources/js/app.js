import './bootstrap';
import '../css/app.css';
import 'element-plus/dist/index.css'

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import ElementPlus from 'element-plus'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

/* Font Awesome */
import { library } from '@fortawesome/fontawesome-svg-core'; /* import the fontawesome core */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'; /* import font awesome icon component */
/* import specific icons */
import { faTable, faArrowUpRightFromSquare } from '@fortawesome/free-solid-svg-icons';
/* add icons to the library */
library.add(faTable, faArrowUpRightFromSquare);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Feed-factory (grch.ru project)';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
      const application = createApp({ render: () => h(app, props) });
      for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        application.component(key, component)
      }
      return application
        .component('font-awesome-icon', FontAwesomeIcon)
        .use(plugin)
        .use(ElementPlus)
        .use(ZiggyVue, Ziggy)
        .mount(el);
  },
});

InertiaProgress.init({ color: '#4B5563' });
