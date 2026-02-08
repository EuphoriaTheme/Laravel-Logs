# Laravel Logs (Blueprint Addon)
View and download Pterodactyl's Laravel log files from the Blueprint admin UI (root admins only).

## What This Addon Does
- Adds an admin page that lists `storage/logs/laravel-*.log` files.
- Lets you select a log file and view recent lines in the browser.
- Provides a download button for the selected log file.
- Validates paths to prevent reading files outside `storage/logs`.

## Compatibility
- Blueprint Framework on Pterodactyl Panel
- Target: `beta-2024-12` (see `conf.yml`)

## Installation / Development Guides
Follow the official Blueprint guides for installing addons and developing extensions:
`https://blueprint.zip/guides`

Uninstall (as shown in the admin view):
`blueprint -remove laravellogs`

## How It Works (Repo Layout)
- `conf.yml`: Blueprint addon manifest (metadata, target version, entrypoints).
- `routes/web.php`: Web router registering the admin routes for viewing/downloading logs.
- `admin/Controller.php`: Admin controller that enforces root-admin access and log-path validation.
- `admin/view.blade.php`: Admin UI (file dropdown, log output, download button).

## Customization (Theme/UX)
- UI markup/styling: `admin/view.blade.php`
- Number of displayed lines: `admin/Controller.php` (search for `slice(-1000)`)

## Contributing
This repo is shared so the community can help improve and extend the addon, not because it's abandoned.
Where it helps, the code includes comments explaining non-obvious behavior; keep comments high-signal.

### Pull Request Requirements
- Clearly state what's been added/updated and why.
- Include images or a short video of it working/in action (especially for UI changes).
- Keep changes focused and avoid unrelated formatting-only churn.
- Keep credits/attribution intact (see `LICENSE`).

## License
Source-available. Redistribution and resale (original or modified) are not permitted, and original credits must be kept within the addon.
See `LICENSE` for the full terms.
