# WordPress with Docker, MySQL, and Redis

This project sets up a WordPress environment using Docker Compose, including MySQL for the database and Redis for object caching.

## Prerequisites

- Docker and Docker Compose installed on your machine.

## Getting Started

1.  **Clone the repository** (if you haven't already) or navigate to the directory containing `docker-compose.yml`.

2.  **Start the services**:
    ```bash
    docker-compose up -d
    ```

3.  **Access WordPress**:
    Open your browser and verify the site is running at `http://localhost:8000`.

4.  **Finish the WordPress Installation**:
    Follow the on-screen instructions to set up your WordPress site.

## Configuring Redis Cache

To enable Redis Object Cache:

1.  Log in to your WordPress Admin Dashboard (`http://localhost:8000/wp-admin`).
2.  Go to **Plugins > Add New**.
3.  Search for **Redis Object Cache** and install it.
4.  Activate the plugin.
5.  Go to **Settings > Redis** and click **Enable Object Cache**.

The `wp-config.php` has already been pre-configured via the `WORDPRESS_CONFIG_EXTRA` environment variable to look for the Redis container at host `redis`.

## Services

-   **WordPress**: The main application running on port 8000.
-   **MySQL**: Database server.
-   **Redis**: In-memory data structure store for caching.
