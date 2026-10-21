import './bootstrap';

import Alpine from 'alpinejs';

// Import Audio System (Make it globally available)
import './audio-manager.js';
import './sfx-integration.js';
import './validation-sfx-helper.js';

window.Alpine = Alpine;

Alpine.start();
