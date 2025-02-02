import React, {createContext, useContext} from 'react';

interface Theme {
    name: string;
    path: string;
    assets: string;
    config: Record<string, any>;
}

export interface Store {
    id: number;
    name: string;
    domain: string;
    theme: Theme;
    logo: string;
    locale: string;
    currency: string;
    isMultistore: boolean;
    parentStore?: number;
    childStores?: number[];
}

const StoreContext = createContext<Store | null>(null);

export const useStore = () => {
    const store = useContext(StoreContext);
    if (!store) {
        throw new Error('useStore must be used within a StoreProvider');
    }
    return store;
};

interface StoreProviderProps {
    store: Store;
    children: React.ReactNode;
}

export const StoreProvider: React.FC<StoreProviderProps> = ({store, children}) => (
    <StoreContext.Provider value={store}>
        {children}
    </StoreContext.Provider>
);