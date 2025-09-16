class AuthService {
    constructor() {
        this.baseURL = '/api';
        this.user = null;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
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

    async register(userData) {
        const response = await this.request('/auth/register', {
            method: 'POST',
            body: userData
        });

        if (response.success) {
            this.user = response.user;
        }

        return response;
    }

    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: { email, password }
        });

        if (response.success) {
            this.user = response.user;
        }

        return response;
    }

    async logout() {
        const response = await this.request('/auth/logout', {
            method: 'POST'
        });

        if (response.success) {
            this.user = null;
        }

        return response;
    }

    async getUser() {
        const response = await this.request('/auth/user');
        
        if (response.success) {
            this.user = response.user;
        }

        return response;
    }

    getCurrentUser() {
        return this.user;
    }

    isAuthenticated() {
        return this.user !== null;
    }
}

const authService = new AuthService();
export { authService };
export default authService;
