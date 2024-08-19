import { wrap } from 'svelte-spa-router/wrap'
import Index from '@app/routes/Index.svelte'
import Logout from '@app/routes/Logout.svelte'
import NotFound from '@app/routes/NotFound.svelte'

export default {

    '/opensesame': wrap({
        asyncComponent: () => import('./routes/OpenSesame.svelte')
    }),

    '/auth/:token': wrap({
        asyncComponent: () => import('./routes/Auth.svelte')
    }),

    '/logout': Logout,

    '/supportcatalogue': wrap({
        asyncComponent: () => import('./routes/SupportCatalogue/SupportCatalogue.svelte')
    }),

    '/users': wrap({
        asyncComponent: () => import('./routes/Users/Users.svelte')
    }),

    '/users/add': wrap({
        asyncComponent: () => import('./routes/Users/Add.svelte')
    }),

    '/users/:user_id': wrap({
        asyncComponent: () => import('./routes/Users/User.svelte')
    }),

    // '/pulse': wrap({
    //     asyncComponent: () => import('./routes/Pulse/Pulse.svelte')
    // }),

    '/': Index,
    '/404': NotFound,
    '*': NotFound,

}