class BackTop {
  constructor() {
    this.backTopBtn = document.querySelector(".backtop");
    this.scrollFrame = null;

    if (!this.backTopBtn) {
      return;
    }

    this.createBackToTopButton();
    this.addScrollListener();
  }

  createBackToTopButton() {
    this.backTopBtn.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth" // Optional: Add smooth scrolling behavior
      });
    });
  }

  addScrollListener() {
    window.addEventListener("scroll", () => {
      if (this.scrollFrame) {
        return;
      }

      this.scrollFrame = window.requestAnimationFrame(() => {
        this.backTopBtn.classList.toggle("backtop--is-visible", window.scrollY > 100);
        this.scrollFrame = null;
      });
    }, { passive: true });
  }
}

export default BackTop;
