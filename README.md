# RNCP Symfony Project

Welcome to the RNCP Symfony Project. Follow these steps to get started.

## Getting Started

### 1. Clone the Symfony project
```sh
git clone https://github.com/medkhaled/RNCP
cd RNCP
```

### 2. Ensure Composer is installed
```sh
composer --version
```

### 3. Install the dependencies
```sh
composer install
```

### 4. Modify the `.env` file
Open the `.env` file and update the necessary configuration settings, such as database connection details and other environment variables.

### 5.Create the Database:
```sh
php bin/console doctrine:database:create
```
### 6.Load the Fixtures:
```sh
php bin/console doctrine:fixtures:load
```

## Additional Information

- For detailed documentation, refer to the [Symfony Documentation](https://symfony.com/doc/current/index.html).
- For issues and contributions, please refer to the [GitHub repository](https://github.com/medkhaled/RNCP).
```