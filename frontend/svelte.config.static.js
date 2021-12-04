// svelte.config.js
import adapter from '@sveltejs/adapter-static';

export default {
    kit: {
        paths: {
            base: '/~dvolm359/cnmt-310-final-project'
        },
        adapter: adapter({
            // default options are shown
            pages: 'build',
            assets: 'build',
            fallback: null
        }),
        target: '.container'
    }
};
