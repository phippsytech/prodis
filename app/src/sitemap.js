let sitemap = {
    "/clients":{
        name : "Client Name",
        roles: ['house','therapist','admin'],
        component: './routes/Documents/Documents.svelte',
        children: {
            "/clients/:client_id":{
                name : "Client Name",
                roles: ['house','therapist','admin'],
                component: './routes/Documents/Documents.svelte',
                children: {
                    "/clients/:client_id/casenotes":{
                        name : "Case Notes",
                        roles: ['therapist','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/timetracking":{
                        name : "Time Tracking",
                        roles: ['therapist','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/timeline":{
                        name : "Timeline",
                        roles: ['house','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/roster":{
                        name : "Roster",
                        roles: ['house','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/forms":{
                        name : "Forms",
                        roles: ['house','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/documents":{
                        name : "Documents",
                        roles: ['house','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/staff":{
                        name : "Staff",
                        roles: ['house','admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                    "/clients/:client_id/settings":{
                        name : "Settings",
                        roles: ['admin'],
                        component: './routes/Documents/Documents.svelte'
                    },
                }
            },
     
        }
    },
    "/timetracking":{
        name : "Sessions to bill",
        component: './routes/Documents/Documents.svelte',
        roles: ['therapist','admin'],
        children: {
            "/timetracking/billed":{
                name : "Billed Sessions",
                component: './routes/Documents/Documents.svelte',
                roles: ['therapist','admin'],
            },
        }
    },
}


