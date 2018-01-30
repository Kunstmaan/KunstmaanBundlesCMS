# System Requirements

* PHP
    * minimum version is PHP 5.6, but also working great with PHP 7.0 or PHP 7.1.
    * JSON needs to be enabled
    * ctype needs to be enabled
    * curl needs to be enabled
    * pcntl need to be enabled
    * imagick and gd needs to be enabled (if you wish to use the KunstmaanMediaBundle)
    * memcached needs to be enabled
    * you need to have the PHP-XML module installed
    * you need to have at least version 2.6.21 of libxml
    * PHP tokenizer needs to be enabled
    * mbstring functions need to be enabled
    * iconv needs to be enabled
    * POSIX needs to be enabled (only on *nix)
    * Intl needs to be installed with ICU 4+
    * An opcode cache needs to be enabled (APC <= 5.4 or the built in Opcode cache >= 5.5)
    * A userland cache APCu
    * PDO with the MySQL binding enabled
    * pecl_http needs to be enabled
    * php.ini recommended settings
        * short_open_tag = Off
        * magic_quotes_gpc = Off
        * register_globals = Off
        * session.auto_start = Off
        * date.timezone should be configured
* MySQL 5.x (some work on sqlite has been done but is not supported)
* Node.js and NPM with these packages installed globally:
    * uglify-js
    * uglifycss
* A webserver like Apache or Nginx (preferably with a Varnish server in front)
* Imagemagick
* Elasticsearch (1.7.3)
* Memcached (optionally)
