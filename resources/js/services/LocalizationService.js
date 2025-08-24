import en from '../locales/en.json';

class LocalizationService {
  constructor() {
    this.currentLocale = 'en';
    this.locales = {
      en
    };
  }

  /**
   * Get text by key path (e.g., 'dashboard.title' returns 'SpendLapse')
   * @param {string} key - The key path to the text
   * @param {Object} params - Parameters to replace in the text
   * @returns {string} The localized text
   */
  t(key, params = {}) {
    const keys = key.split('.');
    let value = this.locales[this.currentLocale];
    
    for (const k of keys) {
      if (value && typeof value === 'object' && value[k] !== undefined) {
        value = value[k];
      } else {
        console.warn(`Localization key not found: ${key}`);
        return key; // Return the key if not found
      }
    }
    
    // Replace parameters in the text
    if (typeof value === 'string' && Object.keys(params).length > 0) {
      return value.replace(/\{(\w+)\}/g, (match, paramName) => {
        return params[paramName] !== undefined ? params[paramName] : match;
      });
    }
    
    return value;
  }

  /**
   * Set the current locale
   * @param {string} locale - The locale to set
   */
  setLocale(locale) {
    if (this.locales[locale]) {
      this.currentLocale = locale;
    } else {
      console.warn(`Locale not found: ${locale}`);
    }
  }

  /**
   * Get the current locale
   * @returns {string} The current locale
   */
  getCurrentLocale() {
    return this.currentLocale;
  }

  /**
   * Get available locales
   * @returns {Array} Array of available locale codes
   */
  getAvailableLocales() {
    return Object.keys(this.locales);
  }

  /**
   * Add a new locale
   * @param {string} locale - The locale code
   * @param {Object} translations - The translation object
   */
  addLocale(locale, translations) {
    this.locales[locale] = translations;
  }
}

// Create and export a singleton instance
const localizationService = new LocalizationService();
export default localizationService;
