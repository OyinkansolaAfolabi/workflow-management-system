# Workflow Management System for Non-Crime Related Activity

This project is a web-based Workflow Management System designed for the Yorkshire and Humber Regional Organised Crime Unit to manage support activities with individual or collaborative tasks.

## Table of Contents
- [Getting Started](#getting-started)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Google OpenAuth API Setup](#google-openauth-api-setup)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Getting Started

Follow these instructions to set up the project on your local machine.

## Prerequisites

- PHP 7.4 or higher
- MySQL or MariaDB
- Composer
- A web server like Apache or Nginx

## Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/OyinkansolaAfolabi/workflow-management-system.git
    cd yhrocu-workflow-management
    ```

2. **Install dependencies:**
    ```bash
    composer install
    ```

## Google OpenAuth API Setup

To use Google OpenAuth for user authentication, you need to obtain API keys.

1. Go to the [Google Cloud Console](https://console.cloud.google.com/).

2. Create a new project or select an existing project.

3. Navigate to **APIs & Services > Credentials**.

4. Click on **Create Credentials** and select **OAuth 2.0 Client IDs**.

5. Configure the OAuth consent screen if you haven't done so already.

6. Set the application type to **Web application** and configure the **Authorized redirect URIs** with the URL where your application will handle the OAuth response. For example:
    ```
    http://localhost/yourproject/backend/redirect.php
    ```

7. Click **Create**. You will get a `Client ID` and `Client Secret`. Save these for later.

## Configuration

1. **Create a `config.php` file in the `backend` folder:**

    ```php
    <?php
    $clientId = 'YOUR_CLIENT_ID';
    $clientSecret = 'YOUR_CLIENT_SECRET';
    $redirectUri = 'YOUR_REDIRECT_URI';
    ?>
    ```

2. **Update the database credentials in `db.php` found in the `backend` folder:**

    ```php
    <?php
    $dbHost = 'YOUR_DATABASE_HOST';
    $dbUser = 'YOUR_DATABASE_USER';
    $dbPass = 'YOUR_DATABASE_PASSWORD';
    $dbName = 'YOUR_DATABASE_NAME';

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

## Database Setup

1. **Import the database schema:**

    The database schema can be found in `setup/yhrocu.sql`.

    ```bash
    mysql -u YOUR_DATABASE_USER -p YOUR_DATABASE_NAME < setup/yhrocu.sql
    ```

2. **Verify that the database tables have been created successfully.**

## Usage

1. **Start your web server and navigate to the project URL:**

    ```
    http://localhost/yourproject
    ```

2. **Sign in using the configured Google OpenAuth.**

3. **Use the application to manage tasks, view progress, and utilize the dashboard features.**

## Contributing

1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
