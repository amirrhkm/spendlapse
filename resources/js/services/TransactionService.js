import axios from 'axios';

class TransactionService {
    constructor() {
        this.baseURL = '/api/transactions';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this.api = axios.create({
            baseURL: this.baseURL,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
            },
            withCredentials: true,
        });
    }

    async uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await this.api.post('/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
            },
        });

        return response.data;
    }

    async getSummary(year = null, month = null) {
        const params = {};
        if (year) params.year = year;
        if (month) params.month = month;

        const response = await this.api.get('/summary', { params });
        return response.data;
    }

    async getAllTransactions() {
        const response = await this.api.get('/');
        return response.data;
    }
}

export const transactionService = new TransactionService();
