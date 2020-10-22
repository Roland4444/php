import OwnersList from './OwnerList.vue';
import Form from './Form.vue';

const routes = [
    { path: '/', component: OwnersList },
    { path: '/add', component: Form },
    {
 path: '/edit/:id', component: Form, name: 'edit', props: true,
},
];

export default routes;
