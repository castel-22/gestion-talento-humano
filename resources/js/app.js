import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

// Chart.js se importa solo en las vistas que lo necesitan vía @push('scripts')
// El dashboard.js ya no se importa globalmente para evitar errores en otras páginas.