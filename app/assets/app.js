/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

const isBackoffice = window.location.pathname.startsWith('/admin');

if (isBackoffice) {
    import('./backoffice/standalone');
    import('./backoffice/app.tsx');
} else {
    import('./frontoffice/standalone');
    import('./frontoffice/app.tsx');
}