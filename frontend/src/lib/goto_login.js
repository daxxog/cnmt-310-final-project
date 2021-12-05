import { onMount } from 'svelte';
import { goto } from '$app/navigation';

import { BACKEND_ENDPOINT, CREDENTIAL_MODE } from './const.js';

export const gotoLogin = async => {
    await goto('/~dvolm359/cnmt-310-final-project/login');
};

export const requireLogin = (next) => {
    onMount(async () => {
        // try fetching the user information
        const response = await fetch(BACKEND_ENDPOINT + '/user.php', { credentials: CREDENTIAL_MODE }).
                        then( res => res.json() );

        if( (response.result ?? false) === 'Success' ) {
            // the user is logged in, so run our code
            await next();
        } else {
            // the user isn't logged in
            await gotoLogin();
        }
    });
};
