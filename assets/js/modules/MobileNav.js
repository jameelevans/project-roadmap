class MobileNav {
  constructor() {
    this.mobileBackground = document.querySelector(".mobile-navigation__background");
    this.mobileMenu = document.querySelector(".mobile-navigation__menu");
    this.mobileContent = document.querySelector(".mobile-navigation__nav");
    this.mobileIcon = document.querySelector(".mobile-navigation__icon");
    this.body = document.body;
    this.mobileLinks = this.mobileContent ? this.mobileContent.querySelectorAll("a") : [];

    if (this.mobileMenu && this.mobileContent) {
      this.events();
    }
  }

  events() {
    this.mobileMenu.addEventListener("click", () => this.setMenuState(!this.isOpen()));
    document.addEventListener("keyup", this.keyPressHandler.bind(this));
    this.mobileLinks.forEach((link) => link.addEventListener("click", this.closeMenu.bind(this)));
  }

  keyPressHandler(e) {
    if (e.key === 'Escape' && this.isOpen()) {
      this.setMenuState(false, true);
    }
  }

  isOpen() {
    return this.mobileContent.classList.contains("mobile-navigation__nav--is-visible");
  }

  setMenuState(isOpen, returnFocus = false) {
    this.mobileContent.classList.toggle("mobile-navigation__nav--is-visible", isOpen);
    this.mobileBackground?.classList.toggle("mobile-navigation__background--is-expanded", isOpen);
    this.mobileIcon?.classList.toggle("mobile-navigation__icon--close-x", isOpen);
    this.body.classList.toggle("fixed-position", isOpen);
    this.mobileMenu.setAttribute("aria-expanded", isOpen ? "true" : "false");
    this.mobileMenu.setAttribute("aria-label", isOpen ? "Close main menu" : "Open main menu");
    this.mobileContent.setAttribute("aria-hidden", isOpen ? "false" : "true");
    this.mobileContent.inert = !isOpen;

    if (isOpen && this.mobileLinks.length) {
      this.mobileLinks[0].focus();
    } else if (returnFocus) {
      this.mobileMenu.focus();
    }
  }

  closeMenu() {
    if (this.isOpen()) {
      this.setMenuState(false);
    }
  }
}

export default MobileNav;
