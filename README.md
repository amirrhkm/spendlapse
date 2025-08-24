# SpendLapse - Bank Statement Analyzer

A modern Laravel + Vue.js application for analyzing bank statements with a dark theme interface.

## Features

- 📁 **CSV Upload**: Parse bank statement CSV files
- 💰 **Financial Summary**: Calculate money in, money out, and final balance
- 🌙 **Dark Theme**: Modern, minimalist dark interface
- 📊 **Monthly Analytics**: View summary by month/year
- ☁️ **Serverless Ready**: Optimized for serverless deployment

## Tech Stack

- **Backend**: Laravel 12 with SOLID principles
- **Frontend**: Vue.js 3 with Tailwind CSS
- **Database**: SQLite (serverless compatible)
- **Design**: Modern dark theme with minimalist UI

## Installation

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer

### Local Development

1. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

3. **Build assets:**
   ```bash
   npm run build
   ```

4. **Start development server:**
   ```bash
   php artisan serve
   npm run dev  # In another terminal
   ```

5. **Visit:** `http://localhost:8000`

## Usage

1. **Upload CSV**: Click "Upload Bank Statement" and select your CSV file
2. **View Summary**: See total money in/out and final balance
3. **Monthly View**: Summary is automatically categorized by month

### CSV Format
Expected CSV structure:
```csv
Date,Transaction Details,Money In,Money Out,Balance
"21-Aug-2025","DESCRIPTION","","MYR 10.00","MYR 1000.00"
```

## Serverless Deployment

### Vercel
1. Install Vercel CLI: `npm i -g vercel`
2. Deploy: `vercel --prod`

### Other Platforms
The application is configured with:
- SQLite database (serverless compatible)
- Stateless design
- Environment-based configuration

## Architecture

### SOLID Principles Implementation

- **Single Responsibility**: Each service handles one concern
- **Open/Closed**: Interfaces allow extension without modification
- **Liskov Substitution**: All implementations follow contracts
- **Interface Segregation**: Focused, specific interfaces
- **Dependency Inversion**: Depends on abstractions, not concretions

### Key Components

```
app/
├── Contracts/               # Interfaces
│   ├── TransactionParserInterface.php
│   └── TransactionRepositoryInterface.php
├── Services/               # Business logic
│   ├── TransactionService.php
│   └── CsvTransactionParser.php
├── Repositories/           # Data access
│   └── TransactionRepository.php
└── Models/                # Data models
    └── Transaction.php
```

## API Endpoints

- `POST /api/transactions/upload` - Upload CSV file
- `GET /api/transactions/summary` - Get monthly summary
- `GET /api/transactions` - Get all transactions

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make changes following SOLID principles
4. Add tests for new functionality
5. Submit a pull request

## License

MIT License