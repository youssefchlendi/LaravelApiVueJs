//import activities from "../components/activities";

const activities = () =>import('../components/activities.vue');

export default [
    {
        path: '/activities',
        component: activities,
        name: 'activities',
    }
]
