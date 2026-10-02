import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Default from './layout/wrapper/index.vue'
import Blank from './layout/wrapper/blank.vue'
import Client from './layout/wrapper/client.vue'
const app = createApp(App)

app.use(router)
app.component("default-layout", Default);
app.component("blank-layout", Blank);
app.component("client-layout", Client);
app.component("khach_hang-layout", Client);

app.mount("#app")