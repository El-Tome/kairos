import {Store} from '../Context/StoreContext';

export interface InertiaPageProps {
    initialPage: {
        props: {
            initialStore: Store;
        };
    };
}