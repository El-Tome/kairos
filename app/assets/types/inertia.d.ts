declare global {
    interface Window {
        inertiaInitialPage: {
            component: string;
            props: {
                initialStore: {
                    id: number;
                    name: string;
                    domain: string;
                    theme: {
                        name: string;
                        path: string;
                        assets: string;
                        config: Record<string, any>;
                    };
                    logo: string;
                    locale: string;
                    currency: string;
                    isMultistore: boolean;
                    parentStore?: number;
                    childStores?: number[];
                };
                errors: Record<string, string>;
                flash: {
                    success?: string;
                    error?: string;
                };
                [key: string]: any;
            };
            url: string;
            version: string;
        }
    }
}

export {};