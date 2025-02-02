import React from 'react';
import {createRoot} from 'react-dom/client';

import {createInertiaApp} from '@inertiajs/inertia-react';
import {InertiaProgress} from '@inertiajs/progress';

import {InertiaPageProps} from '../types/inertia-props';

import {StoreProvider} from '../Context/StoreContext';

InertiaProgress.init({
    color: '#4B5563',
    showSpinner: true,
});

createInertiaApp({
    resolve: name => {
        const theme = window.inertiaInitialPage.props.initialStore.theme.name;
        return import(`./themes/${theme}/Pages/${name}`).then(module => module.default);
    },
    setup({el, App, props}: { el: HTMLElement; App: any; props: InertiaPageProps }) {
        const root = createRoot(el);
        root.render(
            <StoreProvider store={props.initialPage.props.initialStore}>
                <App {...props} />
            </StoreProvider>
        );
    },
});