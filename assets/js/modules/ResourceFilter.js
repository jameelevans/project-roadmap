class ResourceFilter {
  constructor() {
    this.page = document.querySelector("[data-resources-page]");

    if (!this.page) {
      return;
    }

    this.searchInput = this.page.querySelector("[data-resource-search]");
    this.sortSelect = this.page.querySelector("[data-resource-sort]");
    this.cards = Array.from(this.page.querySelectorAll("[data-resource-card]"));
    this.grid = this.page.querySelector("[data-resource-grid]");
    this.typeButtons = Array.from(this.page.querySelectorAll("[data-filter-group='type']"));
    this.topicDropdown = this.page.querySelector("[data-topic-dropdown]");
    this.topicToggle = this.page.querySelector("[data-topic-toggle]");
    this.topicPanel = this.page.querySelector("[data-topic-panel]");
    this.topicLabel = this.page.querySelector("[data-topic-label]");
    this.topicCheckboxes = Array.from(this.page.querySelectorAll("[data-topic-checkbox]"));
    this.topicActive = this.page.querySelector("[data-topic-active]");
    this.clearButton = this.page.querySelector("[data-resource-clear]");
    this.emptyClearButton = this.page.querySelector("[data-resource-empty-clear]");
    this.count = this.page.querySelector("[data-resource-count]");
    this.empty = this.page.querySelector("[data-resource-empty]");
    this.pagination = this.page.querySelector("[data-resource-pagination]");
    this.pageStatus = this.page.querySelector("[data-resource-page-status]");
    this.prevButton = this.page.querySelector("[data-resource-prev]");
    this.nextButton = this.page.querySelector("[data-resource-next]");
    // State mirrors the visible controls so the PHP-rendered cards can stay static.
    this.activeType = "all";
    this.activeTopics = new Set();
    this.currentPage = 1;
    this.perPage = 9;
    this.sortMode = "az";
    // Keeps the "By type" sort consistent with the design mockup.
    this.typeOrder = {
      "tools-checklists": 1,
      "tool": 1,
      "guide": 2,
      "quick-guide": 3,
      "window": 4,
      "fireside-chat": 5
    };

    this.page.classList.add("resources-page--enhanced");
    this.events();
    this.applyFilters();
  }

  events() {
    if (this.searchInput) {
      this.searchInput.addEventListener("input", () => {
        this.currentPage = 1;
        this.applyFilters();
      });
    }

    if (this.sortSelect) {
      this.sortSelect.addEventListener("change", () => {
        this.sortMode = this.sortSelect.value;
        this.currentPage = 1;
        this.applyFilters();
      });
    }

    this.typeButtons.forEach(button => {
      button.addEventListener("click", () => this.toggleType(button));
    });

    this.topicCheckboxes.forEach(checkbox => {
      checkbox.addEventListener("change", () => this.toggleTopic(checkbox));
    });

    if (this.topicToggle && this.topicPanel) {
      this.topicToggle.addEventListener("click", event => {
        event.stopPropagation();
        this.setTopicPanelOpen(this.topicPanel.hidden);
      });

      this.topicPanel.addEventListener("click", event => event.stopPropagation());
      document.addEventListener("click", () => this.setTopicPanelOpen(false));
      document.addEventListener("keydown", event => {
        if (event.key === "Escape" && !this.topicPanel.hidden) {
          this.setTopicPanelOpen(false);
          this.topicToggle.focus();
        }
      });
    }

    if (this.clearButton) {
      this.clearButton.addEventListener("click", () => this.clearFilters());
    }

    if (this.emptyClearButton) {
      this.emptyClearButton.addEventListener("click", () => this.clearFilters());
    }

    if (this.prevButton) {
      this.prevButton.addEventListener("click", () => this.goToPage(this.currentPage - 1));
    }

    if (this.nextButton) {
      this.nextButton.addEventListener("click", () => this.goToPage(this.currentPage + 1));
    }
  }

  toggleType(button) {
    this.activeType = button.dataset.filterValue || "all";
    this.typeButtons.forEach(item => this.setButtonState(item, item === button));
    this.currentPage = 1;
    this.applyFilters();
  }

  toggleTopic(checkbox) {
    if (checkbox.checked) {
      this.activeTopics.add(checkbox.value);
    } else {
      this.activeTopics.delete(checkbox.value);
    }

    this.currentPage = 1;
    this.updateTopicUi();
    this.applyFilters();
  }

  applyFilters() {
    // Recompute the full result set before slicing it into the current page.
    const visibleCards = this.sortCards(this.cards.filter(card => this.cardMatches(card)));
    const total = visibleCards.length;
    const totalPages = Math.max(1, Math.ceil(total / this.perPage));

    this.currentPage = Math.min(Math.max(this.currentPage, 1), totalPages);

    const start = (this.currentPage - 1) * this.perPage;
    const end = Math.min(start + this.perPage, total);
    const pageCards = visibleCards.slice(start, end);

    if (this.grid) {
      visibleCards.forEach(card => this.grid.appendChild(card));
    }

    this.cards.forEach(card => {
      card.classList.remove("resource-card--visible");
      card.classList.add("resource-card--hidden");
    });

    pageCards.forEach((card, index) => {
      card.classList.remove("resource-card--hidden");
      this.animateCard(card, index);
    });

    this.updateMeta(total, start, end, totalPages);
  }

  cardMatches(card) {
    const query = this.searchInput ? this.searchInput.value.trim().toLowerCase() : "";
    const haystack = card.dataset.resourceSearch || "";
    const types = (card.dataset.resourceTypes || "").split(" ").filter(Boolean);
    const topics = (card.dataset.resourceTopics || "").split(" ").filter(Boolean);
    const isFeatured = card.dataset.resourceFeatured === "true";
    const matchesSearch = !query || haystack.includes(query);
    const matchesType = this.activeType === "all" ||
      (this.activeType === "featured" ? isFeatured : types.includes(this.activeType));
    const matchesTopics = this.activeTopics.size === 0 || Array.from(this.activeTopics).some(topic => topics.includes(topic));

    return matchesSearch && matchesType && matchesTopics;
  }

  sortCards(cards) {
    return cards.sort((cardA, cardB) => {
      const featuredA = cardA.dataset.resourceFeatured === "true";
      const featuredB = cardB.dataset.resourceFeatured === "true";

      if (featuredA !== featuredB) {
        return featuredA ? -1 : 1;
      }

      if (this.sortMode === "newest") {
        return (cardB.dataset.resourceDate || "").localeCompare(cardA.dataset.resourceDate || "");
      }

      if (this.sortMode === "type") {
        const typeA = this.typeOrder[cardA.dataset.resourceSortType] || 99;
        const typeB = this.typeOrder[cardB.dataset.resourceSortType] || 99;

        if (typeA !== typeB) {
          return typeA - typeB;
        }
      }

      return (cardA.dataset.resourceSortTitle || "").localeCompare(cardB.dataset.resourceSortTitle || "");
    });
  }

  animateCard(card, index) {
    card.style.animationDelay = `${Math.min(index, 8) * 45}ms`;

    requestAnimationFrame(() => {
      card.classList.add("resource-card--visible");
    });
  }

  updateMeta(total, start, end, totalPages) {
    const hasSearch = this.searchInput && this.searchInput.value.trim() !== "";
    const hasFilters = this.activeType !== "all" || this.activeTopics.size > 0 || hasSearch;
    const label = total === 1 ? "resource" : "resources";

    if (this.count) {
      this.count.textContent = total === 0 ? "0 resources" : `Showing ${start + 1}\u2013${end} of ${total} ${label}`;
    }

    if (this.clearButton) {
      this.clearButton.hidden = !hasFilters;
    }

    if (this.empty) {
      this.empty.hidden = total > 0;
    }

    if (this.pagination) {
      this.pagination.hidden = totalPages <= 1 || total === 0;
    }

    if (this.pageStatus) {
      this.pageStatus.textContent = `Page ${this.currentPage} of ${totalPages}`;
    }

    if (this.prevButton) {
      this.prevButton.disabled = this.currentPage === 1;
    }

    if (this.nextButton) {
      this.nextButton.disabled = this.currentPage === totalPages;
    }
  }

  updateTopicUi() {
    // Active topic chips sit outside the dropdown so selected filters stay visible.
    if (this.topicLabel && this.topicToggle) {
      if (this.activeTopics.size > 0) {
        this.topicToggle.classList.add("resources-filter__dropdown-button--active");
        this.topicLabel.textContent = `Topics ${this.activeTopics.size}`;
      } else {
        this.topicToggle.classList.remove("resources-filter__dropdown-button--active");
        this.topicLabel.textContent = "Filter by topic";
      }
    }

    if (!this.topicActive) {
      return;
    }

    this.topicActive.innerHTML = "";

    this.topicCheckboxes
      .filter(checkbox => this.activeTopics.has(checkbox.value))
      .forEach(checkbox => {
        this.topicActive.appendChild(this.createTopicChip(checkbox));
      });
  }

  createTopicChip(checkbox) {
    const chip = document.createElement("span");
    const removeButton = document.createElement("button");

    chip.className = "resources-filter__active-topic";
    chip.appendChild(document.createTextNode(checkbox.closest("label").querySelector("span").textContent));

    removeButton.type = "button";
    removeButton.setAttribute("aria-label", `Remove ${checkbox.closest("label").querySelector("span").textContent}`);
    removeButton.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>';
    removeButton.addEventListener("click", () => {
      checkbox.checked = false;
      this.activeTopics.delete(checkbox.value);
      this.currentPage = 1;
      this.updateTopicUi();
      this.applyFilters();
    });

    chip.appendChild(removeButton);

    return chip;
  }

  setTopicPanelOpen(isOpen) {
    if (!this.topicPanel || !this.topicToggle) {
      return;
    }

    this.topicPanel.hidden = !isOpen;
    this.topicToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
  }

  goToPage(page) {
    const totalVisible = this.cards.filter(card => this.cardMatches(card)).length;
    const totalPages = Math.max(1, Math.ceil(totalVisible / this.perPage));

    this.currentPage = Math.min(Math.max(page, 1), totalPages);
    this.applyFilters();
    this.page.scrollIntoView({
      behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth",
      block: "start"
    });
  }

  clearFilters() {
    this.activeType = "all";
    this.activeTopics.clear();
    this.currentPage = 1;

    if (this.searchInput) {
      this.searchInput.value = "";
    }

    this.topicCheckboxes.forEach(checkbox => {
      checkbox.checked = false;
    });

    this.typeButtons.forEach(button => {
      this.setButtonState(button, button.dataset.filterValue === "all");
    });

    this.updateTopicUi();
    this.applyFilters();
  }

  setButtonState(button, isActive) {
    button.classList.toggle("resources-filter__pill--active", isActive);
    button.setAttribute("aria-pressed", isActive ? "true" : "false");
  }
}

export default ResourceFilter;
