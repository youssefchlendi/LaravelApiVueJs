import Home from "../components/home";

const home = () =>import('../components/home.vue');

export default [
    {
        path: '/home',
        component: home,
        name: 'home',
    }
]
