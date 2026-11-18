# Development

<!-- cspell:ignore phpunit uams -->

Written against v1.4.1.

The theme ships no Composer dependencies. `composer.json` exists only for the development tools: Pest with Brain Monkey for the tests, and Pint, Rector and PHPStan for the code checks.

## Setting up

```sh
git clone https://github.com/UAMS-Web/news-uams.git
cd news-uams
composer install
```

PHP 8.4 is the development version (`config.platform.php`); the theme supports 8.2 and later.

## Running the tests and checks

| Command | What it does |
| --- | --- |
| `composer test` | The Pest unit suite. It needs no WordPress and no database: WordPress functions are mocked with Brain Monkey. |
| `composer test:pint` | Checks formatting without changing files (`composer pint` applies it). |
| `composer test:refactor` | Previews Rector without changing files (`composer refactor` applies it). |
| `composer stan` | PHPStan, with the WordPress extension and stubs. |
| `composer mutate` | Pest mutation testing over the first-party PHP files. Needs a coverage driver. |

The same checks run in CI on every pull request, from the shared job chain in `UAMS-Web/uams-ci-workflows`, plus a lint of every shipped file under PHP 8.2. The `CI` check must pass before a pull request can merge.

**Note:** a new first-party PHP file at the theme root is added in three places in the same change: the `mutate-paths` input in `.github/workflows/ci.yml`, the `--path` list of the `mutate` script in `composer.json`, and the `<source>` list in `phpunit.xml`. A file the theme runs also needs a `-export-ignore` line in `.gitattributes`, or the download leaves it out.

## Making a change

Every change starts as an issue, is built on a branch from `master` linked to that issue, and merges through a pull request whose test plan has been run. The full path is in [CONTRIBUTING.md](https://github.com/UAMS-Web/news-uams/blob/master/CONTRIBUTING.md).

## Changing these pages

The pages live in `docs/wiki/` and are published by the *Publish wiki* workflow when a release is published, or when the workflow is run by hand. Each publish replaces the whole wiki with the folder, so a page edited in the browser is overwritten. Link between pages with relative links that keep the `.md` extension, such as `[Installation](Installation.md)`.
