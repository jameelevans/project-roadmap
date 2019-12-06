// Import all 3rd party packages 
import $ from 'jquery';

// Modular JS must use classes
class Alert {
    // Gather all elements and invoke events
    constructor() {
        this.theBody = $(window);
        this.events();
    }

    events() {
        // On window click, fire jsAlert
        this.theBody.click(this.jsAlert.bind(this));
    }

    // Function with simple alert
    jsAlert() {
        alert('JS is working! Hello from Alert! Make sure you remove me');
    }
    
  }
  
  // Make sure to export class at bottom
  export default Alert;