# Contributing to Advanced Country Field

First off, thank you for considering contributing to Advanced Country Field! It's people like you that make the Drupal community great.

## Code of Conduct

This project adheres to the [Drupal Code of Conduct](https://www.drupal.org/dcoc). By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as much detail as possible:

- **Clear and descriptive title**
- **Drupal version** and PHP version
- **Module version**
- **Step-by-step reproduction instructions**
- **Expected vs actual behavior**
- **Relevant screenshots** if applicable
- **Environment details** (OS, browser, etc.)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Clear and descriptive title**
- **Detailed description of the enhancement**
- **Use cases** and motivation
- **Possible implementation** approach
- **Examples** if applicable

### Pull Requests

- Fill in the required template
- Do not include issue numbers in the PR title
- Include screenshots and animated GIFs if applicable
- Include thoughtfully-worded, well-structured tests
- Document new code based on Drupal standards
- End all files with a newline
- Avoid platform-dependent code
- Write tests for new features

## Development Process

### Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR-USERNAME/advanced-country-field.git`
3. Create a branch: `git checkout -b feature/your-feature-name`

### Setting Up Development Environment

1. Install Drupal using [Docker4Drupal](https://github.com/wodby/docker4drupal) or another method
2. Install dependencies:
   ```bash
   composer install
   ```
3. Enable the module:
   ```bash
   drush en advanced_country_field -y
   ```

### Coding Standards

#### PHP

- Follow [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards)
- Run PHPCS:
  ```bash
  vendor/bin/phpcs --standard=Drupal,DrupalPractice advanced_country_field
  ```
- Fix auto-fixable issues:
  ```bash
  vendor/bin/phpcbf --standard=Drupal,DrupalPractice advanced_country_field
  ```

#### JavaScript

- Follow [Drupal JavaScript Standards](https://www.drupal.org/docs/develop/standards/coding-standards/javascript-coding-standards)
- Use ES6+ features where supported
- Ensure compatibility across browsers
- Add comments for complex logic

#### CSS

- Follow [Drupal CSS Standards](https://www.drupal.org/docs/develop/standards/css/css-coding-standards)
- Use consistent naming conventions
- Avoid `!important` unless necessary
- Ensure responsive design

### Testing

#### Unit Tests

```bash
# Run module tests
vendor/bin/phpunit --testsuite unit

# Run all tests
vendor/bin/phpunit
```

#### Functional Tests

```bash
# Run Drupal tests
drush test-run advanced_country_field
```

#### Code Quality

```bash
# PHPStan static analysis
vendor/bin/phpstan analyze

# PHPCS coding standards
vendor/bin/phpcs --standard=Drupal,DrupalPractice .

# ESLint for JavaScript
npm run lint
```

### Documentation

- Document all functions with PHPDoc
- Follow [Drupal Documentation Standards](https://www.drupal.org/docs/develop/documenting-your-project)
- Update README.md for user-facing changes
- Update this CONTRIBUTING.md for process changes

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples:**

```
feat(widget): add multi-select support

Added multi-select widget type with custom JavaScript
for flag display in select options.

Closes #123

fix(formatter): correct flag size calculation

Fixed issue where flag dimensions were not applied
correctly in certain view modes.

Fixes #456

docs(readme): update installation instructions

Added step-by-step guide for manual installation.
```

### Submitting Pull Requests

1. Update tests for new features
2. Ensure all tests pass
3. Update documentation
4. Keep the PR focused on one thing
5. Write clear commit messages
6. Request review from maintainers

### Review Process

- All submissions require review
- Address feedback promptly
- Keep discussions constructive
- Be patient with reviewers

## Project Structure

```
advanced_country_field/
├── src/
│   ├── Plugin/
│   │   ├── Field/
│   │   │   ├── FieldType/        # Field type plugin
│   │   │   ├── FieldWidget/      # Widget plugin
│   │   │   └── FieldFormatter/   # Formatter plugin
│   ├── Form/                     # Configuration forms
│   ├── Service/                  # Services
│   └── Controller/               # Controllers
├── css/                          # Stylesheets
├── js/                           # JavaScript
├── templates/                    # Twig templates
├── config/                       # Configuration schemas
│   ├── install/                  # Default configuration
│   └── schema/                   # Configuration schemas
└── tests/                        # Tests (planned)
```

## Community

- **GitHub Issues**: https://github.com/ilyas2017/advanced-country-field/issues
- **Drupal.org**: https://www.drupal.org/project/advanced_country_field

## Recognition

Contributors will be listed in:
- README.md credits section
- Release notes
- Drupal.org project page

## Questions?

Feel free to open an issue for any questions about contributing!

Thank you for contributing to Advanced Country Field! 🎉

