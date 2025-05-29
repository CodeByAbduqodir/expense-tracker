# Expense Tracker

A simple and intuitive web application to manage your income and expenses.

## Features

* Add income and expense transactions with categories, payment types, and dates
* View your current balance in real-time
* Enjoy a simple and clean UI for easy navigation
* Deploy with Docker for seamless setup
* Automate with CI/CD pipeline using GitHub Actions

## Tech Stack

* Backend: Laravel (PHP)
* Frontend: Blade templates with basic CSS
* Database: PostgreSQL
* Containerization: Docker & Docker Compose
* CI/CD: GitHub Actions

## Installation

### Prerequisites

* Docker and Docker Compose installed on your machine
* Git installed
* Composer installed (for dependency management)

### Steps

1. Clone the repository:

    git clone https://github.com/CodeByAbduqodir/expense-tracker.git
    cd expense-tracker

2. Set up environment:

    * Copy the example .env file:

        cp .env.example .env

    * Update .env with your database credentials (if needed):

        DB_CONNECTION=pgsql
        DB_HOST=db
        DB_PORT=5432
        DB_DATABASE=expense_tracker
        DB_USERNAME=postgres
        DB_PASSWORD=secret

    * Generate an application key:

        docker-compose up -d
        docker-compose exec app php artisan key:generate

3. Install dependencies:

    composer install --no-dev --optimize-autoloader

4. Run migrations:

    docker-compose exec app php artisan migrate

5. Start the application:

    docker-compose up -d --build

6. Access the app:

    Open http://localhost:8080 in your browser


## Contributing

Feel free to fork this repository, create a feature branch, and submit a pull request. All contributions are welcome!

## License

This project is licensed under the MIT License.

## Contact

Got questions? Reach out to me at alrgmw@gmail.com