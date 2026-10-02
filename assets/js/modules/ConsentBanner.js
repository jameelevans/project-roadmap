class ConsentBanner {
  constructor() {
    this.banner = document.querySelector("[data-consent-banner]");
    this.acceptButton = document.querySelector("[data-consent-accept]");
    this.dismissButton = document.querySelector("[data-consent-dismiss]");
    this.preferenceButtons = Array.from(document.querySelectorAll("[data-consent-preferences]"));
    this.title = document.querySelector("#consent-banner-title");
    this.cookieName = "projectroadmap_analytics_consent";
    this.cookieLifetime = 60 * 60 * 24 * 180;
    this.cookiePath = this.banner?.dataset.consentCookiePath || "/";
    this.measurementId = this.banner?.dataset.analyticsMeasurementId || "";
    this.lastTrigger = null;
    this.hideTimer = null;

    if (!this.banner || !this.acceptButton || !this.dismissButton) {
      return;
    }

    this.events();

    const preference = this.getPreference();
    this.updateConsent(preference || "denied");

    if (preference === "granted") {
      this.loadAnalytics();
    }

    if (!preference) {
      this.open();
    }
  }

  events() {
    this.acceptButton.addEventListener("click", () => this.savePreference("granted"));
    this.dismissButton.addEventListener("click", () => this.savePreference("denied"));
    this.preferenceButtons.forEach((button) => {
      button.addEventListener("click", () => this.open(button));
    });
    document.addEventListener("keyup", (event) => this.handleKeyup(event));
  }

  getPreference() {
    const cookie = document.cookie
      .split(";")
      .map((item) => item.trim())
      .find((item) => item.startsWith(`${this.cookieName}=`));

    if (!cookie) {
      return null;
    }

    const value = decodeURIComponent(cookie.split("=").slice(1).join("="));
    return ["granted", "denied"].includes(value) ? value : null;
  }

  savePreference(preference) {
    const secureAttribute = window.location.protocol === "https:" ? "; Secure" : "";
    document.cookie = `${this.cookieName}=${preference}; Path=${this.cookiePath}; Max-Age=${this.cookieLifetime}; SameSite=Lax${secureAttribute}`;

    this.updateConsent(preference);
    this.close(true);

    if (preference === "granted") {
      this.loadAnalytics();
    }
  }

  updateConsent(preference) {
    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function gtag() {
      window.dataLayer.push(arguments);
    };

    window.gtag("consent", "update", {
      analytics_storage: preference,
      ad_storage: "denied",
      ad_user_data: "denied",
      ad_personalization: "denied",
    });

    document.documentElement.dataset.analyticsConsent = preference;
    window.dispatchEvent(new CustomEvent("projectroadmap:analytics-consent", {
      detail: { analytics: preference },
    }));
  }

  loadAnalytics() {
    if (!/^G-[A-Z0-9]+$/.test(this.measurementId) || document.querySelector("[data-projectroadmap-analytics]")) {
      return;
    }

    // The tracking library is created only after consent, keeping cacheable
    // HTML and first visits free from optional analytics requests.
    const script = document.createElement("script");
    script.async = true;
    script.dataset.projectroadmapAnalytics = "true";
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(this.measurementId)}`;
    document.head.appendChild(script);

    window.gtag("js", new Date());
    window.gtag("config", this.measurementId, {
      anonymize_ip: true,
      send_page_view: true,
    });
  }

  open(trigger = null) {
    window.clearTimeout(this.hideTimer);
    this.lastTrigger = trigger;
    this.banner.hidden = false;
    this.banner.setAttribute("aria-hidden", "false");

    window.requestAnimationFrame(() => {
      this.banner.classList.add("consent-banner--is-visible");
    });

    if (trigger && this.title) {
      window.setTimeout(() => this.title.focus(), 50);
    }
  }

  close(returnFocus = false) {
    this.banner.classList.remove("consent-banner--is-visible");
    this.banner.setAttribute("aria-hidden", "true");

    this.hideTimer = window.setTimeout(() => {
      this.banner.hidden = true;

      if (returnFocus && this.lastTrigger) {
        this.lastTrigger.focus();
      }
    }, 350);
  }

  handleKeyup(event) {
    if (event.key === "Escape" && !this.banner.hidden) {
      this.savePreference("denied");
    }
  }
}

export default ConsentBanner;
