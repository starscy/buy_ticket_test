import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from "react-dom/client";
// @ts-ignore
import React from "react";


// @ts-ignore
const appName: string = import.meta.env.VITE_APP_NAME || 'Laravel';

interface AppProps {
    el: HTMLElement;
    App: React.FC<any>;
    props: any;
}

// Create the Inertia app
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name: string) => {
        return resolvePageComponent(
            `./Pages/${name}.tsx`,
            // @ts-ignore
            import.meta.glob('./Pages/**/*.{tsx,jsx}', { eager: true }) // Ensure to specify the glob options
        );
    },
    setup({ el, App, props }: AppProps) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
