import {wrap} from 'svelte-spa-router/wrap'

// import Menu from './routes/Menu.svelte'

import Logout from './routes/Logout.svelte'

import NotFound from './routes/NotFound.svelte'

export default {

    

    '/':  wrap({
        asyncComponent: () => import('./routes/My.svelte')
    }),

    '/logout': Logout,
    '/404': NotFound,


    // '/clients/:client_id': wrap({
    //     asyncComponent: () => import('./routes/Staff/Billing/Unbilled.svelte'),
    //     conditions: [
    //         (detail) => {
    //             replace(detail.location+'/unbilled')
    //         }
    //     ]
    // }),

    '/clients/:client_id/details': wrap({
        asyncComponent: () => import('./routes/Clients/Details.svelte')
    }),

    '/clients/:client_id/timeline': wrap({
        asyncComponent: () => import('./routes/Clients/Timeline.svelte')
    }),

    '/clients/:client_id/timeline': wrap({
        asyncComponent: () => import('./routes/Clients/Timeline.svelte')
    }),


 
    '*': NotFound,
}