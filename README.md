# Tsugi Docker

Docker container for [Tsugi](https://www.tsugi.org/) - a Learning Tools Interoperability (LTI) platform for building and hosting educational tools.

## Features

- **Complete Tsugi Installation**: PHP 8.4, Apache, MariaDB all-in-one container
- **Developer Ready**: Git, Node.js, npm, and Composer pre-installed
- **Automatic Setup**: Database creation, user setup, and schema initialization
- **Environment Configurable**: All settings via environment variables
- **Multi-Platform**: Builds for both AMD64 and ARM64 architectures
- **GitHub Actions CI/CD**: Automatic builds and publishing to GitHub Container Registry

## Quick Start

### Using Docker Compose (Recommended)

```bash
# Clone this repository
git clone https://github.com/your-org/tsugi-docker.git
cd tsugi-docker

# Start Tsugi
docker compose up -d

# Access Tsugi at http://localhost:8080/tsugi
```

### Using Docker Run

```bash
# Build the image
docker build -t tsugi .

# Run the container
docker run -d \
  --name tsugi \
  -p 8080:80 \
  -p 3306:3306 \
  -e TSUGI_SERVICENAME="My Tsugi" \
  -e TSUGI_ADMIN_PASSWORD="secure-password" \
  tsugi
```

### Using Pre-built Image from GitHub Container Registry

```bash
docker pull ghcr.io/your-org/tsugi-docker:latest

docker run -d \
  --name tsugi \
  -p 8080:80 \
  -e TSUGI_ADMIN_PASSWORD="secure-password" \
  ghcr.io/your-org/tsugi-docker:latest
```

## First Time Setup

1. Start the container using one of the methods above
2. Navigate to http://localhost:8080/tsugi
3. Click on **Admin** in the navigation
4. Enter the admin password (default: `admin`)
5. The database tables will be created automatically
6. Start using Tsugi!

## Configuration

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `TSUGI_DB_HOST` | `127.0.0.1` | Database host |
| `TSUGI_DB_PORT` | `3306` | Database port |
| `TSUGI_DB_NAME` | `tsugi` | Database name |
| `TSUGI_DB_USER` | `ltiuser` | Database username |
| `TSUGI_DB_PASSWORD` | `ltipassword` | Database password |
| `TSUGI_SERVICENAME` | `TSUGI` | Service name displayed in UI |
| `TSUGI_WWWROOT` | `http://localhost:8080/tsugi` | Base URL for Tsugi |
| `TSUGI_ADMIN_PASSWORD` | `admin` | Admin password (plaintext or SHA256 hash) |
| `TSUGI_DEVELOPER_MODE` | `true` | Enable developer features |
| `TSUGI_TIMEZONE` | `UTC` | Server timezone |
| `TSUGI_COOKIE_SECRET` | auto-generated | Cookie encryption secret |
| `TSUGI_SESSION_SALT` | auto-generated | Session ID salt |
| `TSUGI_MAIL_SECRET` | auto-generated | Mail operation secret |

### Ports

| Port | Service |
|------|---------|
| 80 | HTTP (Apache) |
| 443 | HTTPS (Apache) |
| 3306 | MySQL/MariaDB |

### Volumes

For data persistence, mount these volumes:

```yaml
volumes:
  - tsugi_mysql_data:/var/lib/mysql    # Database files
  - tsugi_blobs:/tsugi-data            # Uploaded files/blobs
```

### Custom Configuration

For advanced configuration, mount a custom `config.php`:

```bash
docker run -d \
  --name tsugi \
  -p 8080:80 \
  -v ./my-config.php:/var/www/html/tsugi/config.php:ro \
  tsugi
```

See `config-template.php` for all available configuration options.

## Development

### Building Locally

```bash
# Build the image
docker build -t tsugi:dev .

# Run with development settings
docker run -d \
  --name tsugi-dev \
  -p 8080:80 \
  -e TSUGI_DEVELOPER_MODE=true \
  tsugi:dev
```

### Accessing the Container

```bash
# Get a shell inside the container
docker exec -it tsugi bash

# View logs
docker logs -f tsugi

# Check Git is available
docker exec -it tsugi git --version
```

### Using Git Inside Container

Git is installed and available at the command line:

```bash
docker exec -it tsugi bash
cd /var/www/html/tsugi
git status
git pull
```

## Production Deployment

For production deployments:

1. **Change all default passwords and secrets**
2. **Set `TSUGI_DEVELOPER_MODE=false`**
3. **Configure proper SSL/TLS** (use a reverse proxy like nginx or Traefik)
4. **Use persistent volumes** for database and blob storage
5. **Set appropriate resource limits**

### Production Example

```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  tsugi:
    image: ghcr.io/your-org/tsugi-docker:latest
    environment:
      TSUGI_DB_PASSWORD: "${DB_PASSWORD}"
      TSUGI_ADMIN_PASSWORD: "${ADMIN_PASSWORD}"
      TSUGI_DEVELOPER_MODE: "false"
      TSUGI_WWWROOT: "https://tsugi.example.com/tsugi"
      TSUGI_COOKIE_SECRET: "${COOKIE_SECRET}"
      TSUGI_SESSION_SALT: "${SESSION_SALT}"
      TSUGI_MAIL_SECRET: "${MAIL_SECRET}"
    volumes:
      - tsugi_mysql_data:/var/lib/mysql
      - tsugi_blobs:/tsugi-data
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
```

## GitHub Actions

This repository includes a GitHub Actions workflow that:

1. Builds the Docker image on push to `main`/`master`
2. Publishes to GitHub Container Registry (ghcr.io)
3. Supports multi-platform builds (AMD64, ARM64)
4. Tags with version numbers, SHA, and `latest`
5. Scans for security vulnerabilities with Trivy

### Triggering a Build

- **Automatic**: Push to `main` or `master` branch
- **Manual**: Use the "Run workflow" button in GitHub Actions
- **Release**: Create a tag starting with `v` (e.g., `v1.0.0`)

## Architecture

```
┌─────────────────────────────────────────────────┐
│                  Docker Container                │
│  ┌─────────────────────────────────────────────┐│
│  │              Ubuntu 24.04                    ││
│  │  ┌─────────┐ ┌─────────┐ ┌───────────────┐ ││
│  │  │ Apache  │ │ PHP 8.4 │ │   MariaDB     │ ││
│  │  │  :80    │ │         │ │    :3306      │ ││
│  │  └────┬────┘ └────┬────┘ └───────┬───────┘ ││
│  │       │           │              │         ││
│  │       └───────────┼──────────────┘         ││
│  │                   │                         ││
│  │           ┌───────┴───────┐                ││
│  │           │     Tsugi     │                ││
│  │           │  /var/www/html│                ││
│  │           └───────────────┘                ││
│  └─────────────────────────────────────────────┘│
└─────────────────────────────────────────────────┘
```

## Troubleshooting

### Container won't start

```bash
# Check logs
docker logs tsugi

# Check if ports are in use
lsof -i :8080
lsof -i :3306
```

### Database connection issues

```bash
# Connect to MariaDB inside container
docker exec -it tsugi mysql -u ltiuser -pltipassword tsugi

# Check MariaDB status
docker exec -it tsugi service mariadb status
```

### Reset everything

```bash
# Stop and remove container
docker compose down -v

# Rebuild and start fresh
docker compose up --build -d
```

## License

Tsugi is licensed under the Apache 2.0 License. See the [Tsugi repository](https://github.com/tsugiproject/tsugi) for details.

## Resources

- [Tsugi Project](https://www.tsugi.org/)
- [Tsugi GitHub](https://github.com/tsugiproject/tsugi)
- [Tsugi Documentation](http://do1.dr-chuck.com/tsugi/phpdoc/)
- [LTI Specification](https://www.imsglobal.org/activity/learning-tools-interoperability)
