import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        return resolvePageComponent(
            `./Pages/${name}.tsx`, // Сначала пробуем .tsx
            import.meta.glob(['./Pages/**/*.tsx', './Pages/**/*.jsx']) // Поддержка обоих форматов
        ).catch(() => {
            // Если не удалось загрузить .tsx, пробуем .jsx
            return resolvePageComponent(
                `./Pages/${name}.jsx`,
                import.meta.glob(['./Pages/**/*.jsx', './Pages/**/*.tsx'])
            );
        });
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
