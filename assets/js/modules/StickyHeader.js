import throttle from 'lodash/throttle'
import debounce from 'lodash/debounce'

class StickyHeader {
  constructor() {
    this.siteHeader = document.querySelector(".header__top")
    this.pageSections = document.querySelectorAll(".page-section")
    this.browserHeight = window.innerHeight
    this.previousScrollY = window.scrollY
    this.events()
  }

  events() {
    window.addEventListener("scroll", throttle(() => this.runOnScroll(), 200))
    window.addEventListener("resize", debounce(() => {
      this.browserHeight = window.innerHeight
    }, 333))
  }

  runOnScroll() {
    this.determineScrollDirection()

    if (window.scrollY > 60) {
      this.siteHeader.classList.add("header__top--scrolled")
    } else {
      this.siteHeader.classList.remove("header__top--scrolled")
    }

    this.pageSections.forEach(el => this.calcSection(el))
  }

  determineScrollDirection() {
    if (window.scrollY > this.previousScrollY) {
      this.scrollDirection = 'down'
    } else {
      this.scrollDirection = 'up'
    }
    this.previousScrollY = window.scrollY
  }

  calcSection(el) {
    let scrollOffset = 0; // Initialize the scroll offset to 0
  
    // Check if the current section is the "about" section or "contact" section
    if (el.classList.contains('staff__wrapper')) {
      // If it is, set the scroll offset to -25rem (adjust as needed)
      scrollOffset = -25 * parseFloat(getComputedStyle(document.documentElement).fontSize);
    }
  
    if (window.scrollY + this.browserHeight > el.offsetTop - scrollOffset && window.scrollY < el.offsetTop + el.offsetHeight - scrollOffset) {
      let scrollPercent = el.getBoundingClientRect().top / this.browserHeight * 100
  
      // Add a condition to check if scrollPercent is in the range [0, 50] (adjust as needed)
      if (scrollPercent >= 0 && scrollPercent <= 50) {
        let matchingLink = el.getAttribute("data-matching-link");
        document.querySelectorAll(`.navigation__link:not(${matchingLink})`).forEach(el => el.classList.remove("is-current-link"));
        document.querySelector(matchingLink).classList.add("is-current-link");
      }
    }
  
    // Check if the user has scrolled to the bottom of the page
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
      let contactLink = document.querySelector('[data-matching-link="#contact-link"]');
      document.querySelectorAll(`.navigation__link:not(${contactLink.getAttribute('data-matching-link')})`).forEach(el => el.classList.remove("is-current-link"));
      contactLink.classList.add("is-current-link");
    }
  }
}

export default StickyHeader