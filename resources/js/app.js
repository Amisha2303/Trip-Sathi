// Bootstrap's JS bundle: dropdowns, collapse, modal, offcanvas, tooltip and
// popover all depend on it. Imported once here so every page can rely on the
// data-api attributes working without extra per-view imports. Exposed on
// window because the layout and some Blade views use the global `bootstrap`.
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Bootstrap Icons — imported from JS so Vite resolves the font assets
// (woff2/woff) from node_modules and emits them into the build.
import 'bootstrap-icons/font/bootstrap-icons.css';

import './bootstrap';
import './wishlist';
