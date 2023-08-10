import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory('/justice/public/'),
    routes: [
        {
            path: '/users/phonebook',
            name: 'Phonebook',
            component: () => import('@/js/views/Phonebook.vue')
        },
        {
            path: '/users/:sid?',
            name: 'Users',
            component: () => import('@/js/views/Users.vue')
        },
        {
            path: '/computers/:sid?',
            name: 'Computers',
            component: () => import('@/js/views/Computers.vue')
        },
        {
            path: '/printers/:id?',
            name: 'Printers',
            component: () => import('@/js/views/Printers.vue')
        },
        {
            path: '/servers',
            name: 'Servers',
            component: () => import('@/js/views/Servers.vue')
        },
        {
            path: '/attendance',
            name: 'Attendance',
            component: () => import('@/js/views/Attendance.vue')
        },
        {
            path: '/schedule',
            name: 'Schedule',
            component: () => import('@/js/views/Schedule.vue')
        },
        {
            path: '/ip',
            name: 'IP',
            component: () => import('@/js/views/IP.vue')
        },
        {
            path: '/',
            redirect: '/users'
        }
    ]
});

export default router