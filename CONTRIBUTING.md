# Contributing to filalaunch/saaskit

Thanks for wanting to contribute. See [README.md](./README.md) first for what this project is, what's built, and what's still planned — this file only covers *how* to contribute, not what the project does.

## Before you start

- **Small, focused PRs get merged faster.** If you're fixing a bug or adding a small improvement, just open a PR. If you're planning something larger (a new AI provider driver, a new branding font-pair, a structural change), open an issue first to discuss it — saves you from building something that doesn't fit the project's direction.
- **Stay inside the two features this repo covers:** the AI Usage & BYOK Manager and White-Label Branding (Preset Mode). This repo is a focused teaser piece of the larger [FilaLaunch](https://filalaunch.com) project, not the place for unrelated modules — those live in the full kit.

## How to contribute

1. **Fork** this repository to your own GitHub account.
2. **Clone your fork** and create a branch off `main` with a descriptive name (e.g. `fix/byok-key-validation`, `feat/mistral-provider-driver`).
3. Make your changes, following the conventions already in the codebase (see below).
4. Commit with a clear message describing *what* changed and *why*, not just *what file*.
5. Push to your fork and **open a pull request** against `filalaunch/saaskit`'s `main` branch.
6. Fill in the PR description: what it does, why, and how you tested it.

## Adding a new AI provider driver

If you're extending `AIProviderDriver` to support a new provider, keep the interface shape identical to the existing drivers (`chat()`, `embed()`, `listModels()`, `validateKey()`) — that consistency is what makes this a genuine drop-in module rather than a pile of special cases. Don't add provider-specific methods to the shared contract; provider quirks belong inside that provider's own driver class.

## Code style

- Follow the existing formatting in the codebase (Laravel Pint defaults). Run `./vendor/bin/pint` before committing if you have it available.
- No commented-out code, no debug `dd()`/`dump()` calls left in commits.
- Keep migrations additive where possible — don't rewrite existing migration files that have already been merged.

## Review & merge process

All pull requests are reviewed manually before merging — there is no auto-merge on this repo. This means:
- Your PR won't merge itself, even if checks pass — a maintainer reviews and approves it first.
- You may get review comments asking for changes before it's merged — this is normal, not a rejection.
- Response time may vary; this is currently maintained by one person.

## Reporting bugs / requesting features

Open a GitHub Issue. For bugs, include: what you expected, what actually happened, and steps to reproduce. For feature requests, explain the use case, not just the feature — it helps decide whether it fits this repo or belongs in the full FilaLaunch kit instead.

## License

By contributing, you agree that your contributions will be licensed under this project's [MIT License](./LICENSE).
