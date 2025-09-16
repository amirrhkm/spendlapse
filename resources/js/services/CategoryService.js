class CategoryService {
    constructor() {
        this.baseURL = '/api';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
            },
            credentials: 'same-origin',
            ...options
        };

        if (options.body && typeof options.body === 'object') {
            config.body = JSON.stringify(options.body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    async getCategories() {
        return await this.request('/categories');
    }

    async createCategory(categoryData) {
        return await this.request('/categories', {
            method: 'POST',
            body: categoryData
        });
    }

    async deleteCategory(id) {
        return await this.request(`/categories/${id}`, {
            method: 'DELETE'
        });
    }

    async migrateFromLocalStorage() {
        const savedPrefixes = localStorage.getItem('customPrefixes');
        if (!savedPrefixes) {
            return { success: true, migrated_count: 0 };
        }

        try {
            const categories = JSON.parse(savedPrefixes);
            const response = await this.request('/categories/migrate', {
                method: 'POST',
                body: { categories }
            });

            // Clear localStorage after successful migration
            if (response.success) {
                localStorage.removeItem('customPrefixes');
            }

            return response;
        } catch (error) {
            console.error('Migration failed:', error);
            return { success: false, message: 'Migration failed' };
        }
    }
}

// Create and export a singleton instance
const categoryService = new CategoryService();
export { categoryService };
export default categoryService;
