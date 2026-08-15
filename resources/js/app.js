

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
