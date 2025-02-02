import React from 'react';
import {createRoot} from 'react-dom/client';

export const mountComponent = (
    Component: React.ComponentType<any>,
    elementId: string,
    props: Record<string, any> = {}
) => {
    const el = document.getElementById(elementId);
    if (el) {
        createRoot(el).render(React.createElement(Component, props));
    }
};