import $ from 'jquery';

class MobileNav {
  constructor() {
    this.mobileBackground = $(".mobile-navigation__background");
    this.mobileMenu = $(".mobile-navigation__menu");
    this.mobileContent = $(".mobile-navigation__nav");
    this.mobileIcon = $(".mobile-navigation__icon");
    this.body = $(".container");
    this.mobileLinks = this.mobileContent.find("a");

    this.events();
  }

  events() {
    this.mobileMenu.click(() => this.setMenuState(!this.isOpen()));
    $(document).keyup(this.keyPressHandler.bind(this));
    this.mobileLinks.click(this.closeMenu.bind(this));
  }

  keyPressHandler(e) {
    if (e.key === 'Escape' && this.isOpen()) {
      this.setMenuState(false, true);
    }
  }

  isOpen() {
    return this.mobileContent.hasClass("mobile-navigation__nav--is-visible");
  }

  setMenuState(isOpen, returnFocus = false) {
    this.mobileContent.toggleClass("mobile-navigation__nav--is-visible", isOpen);
    this.mobileBackground.toggleClass("mobile-navigation__background--is-expanded", isOpen);
    this.mobileIcon.toggleClass("mobile-navigation__icon--close-x", isOpen);
    this.body.toggleClass("fixed-position", isOpen);
    this.mobileMenu.attr("aria-expanded", isOpen ? "true" : "false");
    this.mobileMenu.attr("aria-label", isOpen ? "Close main menu" : "Open main menu");
    this.mobileContent.attr("aria-hidden", isOpen ? "false" : "true");

    if (isOpen && this.mobileLinks.length) {
      this.mobileLinks.first().trigger("focus");
    } else if (returnFocus) {
      this.mobileMenu.trigger("focus");
    }
  }

  closeMenu() {
    if (this.isOpen()) {
      this.setMenuState(false);
    }
  }
}

export default MobileNav;
