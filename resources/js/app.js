import './bootstrap';
import { HSStaticMethods } from 'preline/non-auto';

// Function to init only the plugins you use
function autoInit() {
  HSStaticMethods.autoInit(['dropdown', 'overlay']);
}

// Initial page load
autoInit();

// Re-initialize ONLY after Livewire morphs the DOM
document.addEventListener('livewire:init', () => {
  Livewire.hook('morphed', () => {
    autoInit();
  });
});
