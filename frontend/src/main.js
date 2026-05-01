import { createApp } from 'vue'
import App from './App.vue'
import { router } from './router'
import './style.css'
import "./api/interceptor"
createApp(App).use(router).mount('#app')
