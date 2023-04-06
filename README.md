# File Cache
[![Software license][ico-license]](README.md)
[![Build][ico-build-7.4]][link-build]
[![Build][ico-build-8.0]][link-build]
[![Coverage][ico-codecov]][link-codecov]

[ico-license]: https://img.shields.io/github/license/nrk/predis.svg?style=flat-square
[ico-build-7.4]: https://github.com/micronative/file-cache/actions/workflows/php-7.4.yml/badge.svg
[ico-build-8.0]: https://github.com/micronative/file-cache/actions/workflows/php-8.0.yml/badge.svg
[ico-codecov]: https://codecov.io/gh/micronative/file-cache/branch/master/graph/badge.svg

[link-build]: https://github.com/micronative/file-cache/actions
[link-codecov]: https://codecov.io/gh/micronative/file-cache

A simple file based cache engine that implements the Psr cache interfaces

## Configuration

composer.json
<pre>
"require": {
        "micronative/file-cache": "^1.0.0"
},
"repositories": [
    { "type": "vcs", "url": "https://github.com/micronative/file-cache" }
],
</pre>

Run
<pre>
composer require micronative/file-cache:1.0.0
</pre>
