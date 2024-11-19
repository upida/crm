## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repository.git
   ```
2. Navigate to the project directory:
   ```bash
   cd crm
   ```
3. Install the dependencies:
   ```bash
   composer install
   ```
4. Set up the environment variables:
   ```bash
   cp .env.example .env
   ```
5. Create a new database for the application:
   ```bash
   php artisan migrate
   ```
6. Create a new user for the application:
   ```bash
   php artisan db:seed
   ```
7. Start the application:
   ```bash
   php artisan serve
   ```

## Testing

1. Set up the environment variables:
   ```bash
   cp .env .env.testing
   ```

2. Run the tests:

   ```bash
   php artisan test
   ```

## API Documentation

The API documentation can be found at [https://documenter.getpostman.com/view/26560144/2sAYBREtXt](https://documenter.getpostman.com/view/26560144/2sAYBREtXt).

