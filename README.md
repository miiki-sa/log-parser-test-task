# Log Parser Test Task

This is a Symfony-based application designed to process and analyze log files from multiple services. The application uses a message queue (Redis) for asynchronous processing of log entries and provides an API for querying the processed logs.

## Requirements

- Docker
- GNU Make
- PHP 8.4

## Installation

1. Clone the repository

2. Install dependencies and start the application:
```bash
make install
```

This command will:
- Build and start Docker containers
- Install PHP dependencies
- Run database migrations
- Start the Redis message queue worker

## Usage

### Processing Log Files

To process a log file, use the following command:

```bash
make dispatch
```

### Message Queue Worker

The application uses Redis as a message queue for asynchronous log processing. To start the worker:

```bash
make start-consumer
```

The worker will:
- Process log entries asynchronously
- Store them in the database
- Handle any errors during processing

### API Endpoints

The application provides a REST API for querying processed logs. API documentation is available at:
```
http://localhost:8888/api/doc
```

## Testing

Run the test suite:

```bash
make test
```

This will execute:
- PHPUnit tests

## Development

### Available Make Commands

- `make install` - Install dependencies and start the application
- `make test` - Run all tests and code quality checks
- `make sh` - Open a shell in the PHP container
- `make clean` - Remove all containers and untracked files
- `make start` - Start the application containers
- `make stop` - Stop the application containers
- `make restart` - Restart the application containers

### Database Management

- Run migrations:
```bash
make install-migrations
```

## Log Format

The application expects log entries in the following format:
```
service-name - - [DD/MMM/YYYY:HH:mm:ss +ZZZZ] "HTTP_METHOD /path HTTP_VERSION" STATUS_CODE
```

Example:
```
user-service - - [10/Oct/2023:13:55:36 +0000] "GET /api/v1/users HTTP/1.1" 200
```

## License

This project is proprietary software. All rights reserved.