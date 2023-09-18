import $ from 'jquery';

class MobileNav {
  constructor() {
    this.mobileBackground = $(".mobile-navigation__background");
    this.mobileMenu = $(".mobile-navigation__menu");
    this.mobileContent = $(".mobile-navigation__nav");
    this.mobileIcon = $(".mobile-navigation__icon");
    this.body = $(".container");

    this.events();
  }

  events() {
    this.mobileMenu.click(this.toggleMenu.bind(this));
    $(document).keyup(this.keyPressHandler.bind(this));
    
    // Add an event handler for links within the mobile menu
    this.mobileContent.find('a').click(this.closeMenu.bind(this));
  }

  keyPressHandler(e) {
    if (e.key === 'Escape' && this.mobileIcon.hasClass("mobile-navigation__icon--close-x")) {
      this.toggleMenu();
    }
  }

  toggleMenu() {
    this.mobileContent.toggleClass("mobile-navigation__nav--is-visible");
    this.mobileBackground.toggleClass("mobile-navigation__background--is-expanded");
    this.mobileIcon.toggleClass("mobile-navigation__icon--close-x");
    this.body.toggleClass("fixed-position");

    // Toggle ARIA attributes here
    this.toggleAria();
  }

  toggleAria() {
    // Implement your ARIA attribute toggling logic here
    // You can reference this.mobileContent and this.mobileIcon as needed
  }

  closeMenu() {
    // Close the menu when a link is clicked
    if (this.mobileIcon.hasClass("mobile-navigation__icon--close-x")) {
      this.toggleMenu();
    }
  }
}

export default MobileNav;