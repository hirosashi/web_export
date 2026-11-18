---
name: wordpress-xampp-troubleshooting
description: Restore and troubleshoot an existing WordPress backup on Windows XAMPP. Use for local database imports, broken media after migration, and PHP 8 warnings or deprecations in themes.
---

# WordPress XAMPP Troubleshooting

## Establish the local target

- Confirm the Apache virtual host, document root, URL base path, PHP version, database name, and WordPress table prefix. A backup can contain multiple installations or table sets. Match each site's files, options, and content before selecting a prefix.
- Use a local database for edits and tests. Inspect copied configuration for production database connections, scheduled jobs, and outbound integrations before serving the site or exercising forms. Preserve access controls in the requested environment.
- For dumps too large for phpMyAdmin, use the local MySQL or MariaDB CLI against the confirmed test database. Diagnose client compatibility or packet-size errors from the first failing statement. Make any dump conversion on a copy and check the import exit status and required tables before reporting success. A late failure can leave a partially imported schema.
- Align the local WordPress home and site URLs, rewrite base, and root-relative asset paths with the virtual host. Use a serialization-aware WordPress tool when replacing URLs in serialized data.

## Restore missing media

- Check the actual page response and resolve image URLs against the page URL. Include responsive and lazy-loaded sources when present. Compare representative failures with filesystem paths, and distinguish missing files from URL, encoding, or Apache routing problems.
- Resume large media copies without purging the destination. For Robocopy, inspect its summary and treat exit codes of 8 or higher as failures. Limit restoration to the needed files where possible.
- For mojibake, confirm that reversing the suspected encoding reproduces the expected filename and that the source image matches. Preserve the original backup and avoid overwriting an existing file under the corrected name. Verify the restored URL returns image content, since a WordPress rewrite can return HTML with status 200.

## Patch PHP 8 issues

- Before editing, create any requested dated sibling copies, for example `template.php.YYYYMMDD`. Check the copies match the originals and preserve existing backups. Keep source backups out of deployed artifacts and prevent HTTP access to backups under a document root.
- Trace display flags to their intended assignments. Provide a default for an unset flag only when the resulting UI behavior matches the requirement.
- Normalize fractional array keys explicitly. Infer rounding from existing behavior and confirm the normalized key exists in the lookup table. Compare results at representative boundaries.
- Normalize numeric metadata before calculating totals and averages. Decide whether invalid ratings count as zero or are omitted, and use the same population for the count and average. Handle an empty population without division.
- For optional POST fields, distinguish missing and empty values as the form requires. For scalar text fields, reject array-shaped values before escaping. Apply the framework's unslashing rules where applicable and escape at the output context. Verify relay templates without submitting to external services.

## Verify and report

- Compare the edited files with the dated copies. Preserve encoding and line endings. Run `php -l` with the local PHP executable.
- Exercise the affected routes and data cases, such as an outlet detail, a fractional input, a search with missing ratings, or an empty POST. Check new log entries rather than relying on the homepage status alone. State any route or integration that could not be exercised.
- Keep reusable instructions and synthetic examples free of credentials, database dumps, private URLs, and copied client source.
