import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../pages/LoginPage.vue'
import DashboardPage from '../pages/DashboardPage.vue'
import IpListPage from '../pages/IpListPage.vue'
import AuditPage from '../pages/AuditPage.vue'

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: LoginPage },
    { path: '/dashboard', component: DashboardPage },
    { path: '/ips', component: IpListPage },
    { path: '/audit', component: AuditPage },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router