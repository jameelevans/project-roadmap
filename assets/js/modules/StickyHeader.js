class StickyHeader {
  constructor() {
    this.siteHeader = document.querySelector(".header__top")
    this.pageSections = document.querySelectorAll(".page-section")
    this.browserHeight = window.innerHeight
    this.scrollFrame = null
    this.resizeTimer = null
    this.events()
    this.runOnScroll()
  }

  events() {
    window.addEventListener("scroll", () => this.queueScrollUpdate(), { passive: true })
    window.addEventListener("resize", () => {
      window.clearTimeout(this.resizeTimer)
      this.resizeTimer = window.setTimeout(() => {
        this.browserHeight = window.innerHeight
        this.runOnScroll()
      }, 200)
    }, { passive: true })
  }

  queueScrollUpdate() {
    if (this.scrollFrame) {
      return
    }

    this.scrollFrame = window.requestAnimationFrame(() => {
      this.runOnScroll()
      this.scrollFrame = null
    })
  }

  runOnScroll() {
    if (!this.siteHeader) {
      return
    }

    if (window.scrollY > 60) {
      this.siteHeader.classList.add("header__top--scrolled")
    } else {
      this.siteHeader.classList.remove("header__top--scrolled")
    }

    this.pageSections.forEach(el => this.calcSection(el))
  }

  calcSection(el) {
    const matchingLink = el.getAttribute("data-matching-link")
    const currentLink = matchingLink ? document.querySelector(matchingLink) : null

    if (!currentLink) {
      return
    }

    if (window.scrollY + this.browserHeight > el.offsetTop && window.scrollY < el.offsetTop + el.offsetHeight) {
      let scrollPercent = el.getBoundingClientRect().top / this.browserHeight * 100

      if (scrollPercent >= 0 && scrollPercent <= 50) {
        document.querySelectorAll(`.navigation__link:not(${matchingLink})`).forEach(el => el.classList.remove("is-current-link"));
        currentLink.classList.add("is-current-link");
      }
    }

    // Check if the user has scrolled to the bottom of the page
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
      const contactSection = document.querySelector('[data-matching-link="#contact-link"]')
      const contactLink = document.querySelector("#contact-link")

      if (contactSection && contactLink) {
        document.querySelectorAll(".navigation__link").forEach(link => link.classList.remove("is-current-link"))
        contactLink.classList.add("is-current-link")
      }
    }
  }
}

export default StickyHeader
