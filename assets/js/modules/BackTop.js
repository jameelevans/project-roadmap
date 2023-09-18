class BackToTop {
  constructor() {
    this.backTopBtn = document.querySelector(".backtop");
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
      if (window.scrollY > 100) { // Show the button when user scrolls down a certain amount
        this.backTopBtn.classList.add("backtop--is-visible");
      } else {
        this.backTopBtn.classList.remove("backtop--is-visible");
      }
    });
  }
}

// Usage
new BackToTop();