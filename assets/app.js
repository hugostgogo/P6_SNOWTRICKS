/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// MD ICONS
import 'material-icons/iconfont/material-icons.scss';
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// On page load or when changing themes, best to add inline in `head` to avoid FOUC
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
    localStorage.theme = 'dark'
} else {
    document.documentElement.classList.remove('dark')
    localStorage.theme = 'light'
}
