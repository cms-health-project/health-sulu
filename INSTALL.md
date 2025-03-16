# HealthSuluBundle Installation Guide

This document provides instructions for installing and configuring the HealthSuluBundle for your Sulu project.

## Prerequisites

- A working Sulu CMS installation (Symfony 7.2.x)
- Composer

## Installation Steps

### 1. Install the Bundle

Install the bundle using Composer:

```bash
composer config repositories.health-sulu vcs https://github.com/cms-health-project/health-sulu
composer require cms-health-project/health-sulu:dev-main
```

### 2. Enable the Bundle

Add the bundle to your `config/bundles.php` file:

```php
return [
    // ...existing bundles
    YourVendor\HealthSuluBundle\HealthSuluBundle::class => ['all' => true],
];
```

### 3. Configure the Route

Create a new file `config/routes/sulu_health.yaml` (or add to an existing routing file):

```yaml
sulu_health.status:
    path: /health/status
    defaults:
        _controller: YourVendor\HealthSuluBundle\Controller\HealthCheckController::healthStatus
        _requestAnalyzer: false
```

### 4. Configure the Bundle (Optional)

If you need to customize the bundle configuration, create a `config/packages/sulu_health.yaml` file:

```yaml
health_sulu:
    # Your configuration options here
    access_token: yourtokenheremin16chars
```

### 5. Clear the Cache

```bash
bin/console cache:clear
```

## Usage

Once installed, you can access the health status endpoint at the path you configured in
`config/routes/sulu_health.yaml`.