composer-platform-generator
===========================

[![Version](https://img.shields.io/packagist/v/instrumentisto/composer-platform-generator.svg)](https://packagist.org/packages/instrumentisto/composer-platform-generator) ![Composer Version](https://img.shields.io/badge/composer-%5E1.0-informational.svg) [![Build Status](https://travis-ci.org/instrumentisto/composer-platform-generator.svg?branch=master)](https://travis-ci.org/instrumentisto/composer-platform-generator)

[Composer] plugin for auto-generating platform requirements in `composer.json`.

The plugin generates `config.platform` section in `composer.json` file basing on current PHP environment. This is especially useful when [Docker] images are used. Once generated `config.platform` section in runtime image may correctly reused in toolchain images (such as [`composer` Docker image]).




## Usage

```bash
composer global require "instrumentisto/composer-platform-generator"

cd my-project/
composer update-platform-reqs
```


### in-Docker

```bash
cd my-project/

# Vendor and install necessary dev dependencies.
docker run --rm -v "$(pwd)":/app -w /app \
  composer require --dev "composer/composer" \
                         "instrumentisto/composer-platform-generator"
docker run --rm -v "$(pwd)":/app -w /app \
  composer install --ignore-platform-reqs

# Generate config.platform section basing on you runtime image.
docker run --rm -v "$(pwd)":/app -w /app \
  my-project-image \
    vendor/bin/composer update-platform-reqs

# Now you can run the commands bellow without errors
# and with PHP environment considered exactly as you need.
docker run --rm -v "$(pwd)":/app -w /app \
  composer install
docker run --rm -v "$(pwd)":/app -w /app \
  composer update
```




## License

This plugin is [MIT](LICENSE.md) licensed.





[Composer]: https://getcomposer.org
[Docker]: https://www.docker.com
[`composer` Docker image]: https://hub.docker.com/_/composer
