import ExpenseList from './ExpenseList.vue';
import Form from './Form.vue';

const routes = [
    { path: '/', component: ExpenseList },
    { path: '/add', component: Form },
    {
        path: '/edit/:id', component: Form, name: 'edit', props: true,
    },
];

export default routes;
