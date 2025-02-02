declare module '@inertiajs/progress' {
    export const InertiaProgress: {
        init: (options?: {
            delay?: number;
            color?: string;
            includeCSS?: boolean;
            showSpinner?: boolean;
        }) => void;
    };
}