# Electricity Bill Calculator

A web application for calculating and managing electricity bills.

## Features

- User authentication and authorization
- Electricity bill calculation based on usage
- Admin dashboard for user management
- Historical bill tracking
- [Add other key features of your application]

## Local Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer (latest version)
- Node.js v22.x
- MySQL/MariaDB/PostgreSQL database server
- Git

### Installation Steps

1. Clone the repository:

    ```bash
    git clone [your-repository-url]
    cd electricity-bill-calculator
    ```

2. Install PHP dependencies:

    ```bash
    composer install
    ```

3. Install Node.js dependencies:

    ```bash
    npm install
    ```

4. Configure your environment:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. Set up your database credentials in `.env` file:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

6. Set up the database:

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

7. Start the development server:

    ```bash
    php artisan serve
    ```

8. In a separate terminal, compile assets:
    ```bash
    npm run dev
    ```

### Default Admin Access

You can log in to the application using these credentials:

- Email: `superadmin@example.com`
- Password: `abcd1234`

**Note:** It's recommended to change these credentials in production.

## Testing

Run the test suite:

```bash
php artisan test
```

## Deployment

[Add deployment instructions specific to your setup]

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

[Add your license information]

## Support

[Add support contact information or links]
