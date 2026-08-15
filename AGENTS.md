## UniWAMP Environment Detection (Required)

This project uses an existing UniWAMP installation.

**Before running any command or suggesting any setup:**

1. Read the file:
   ```
   config/uniwamp.json
   ```
2. This file is the single source of truth for the local development environment.
3. Use the paths defined in this file for:
   - PHP executable
   - Composer
   - MySQL/MariaDB
   - Apache
   - Node.js
   - npm
   - Any other binaries
4. Never hardcode executable paths.
5. Never install another PHP/WAMP stack if `config/uniwamp.json` exists.
6. If a required binary is missing from `uniwamp.json`, ask the user before making assumptions.
7. Always execute commands using the binaries specified in `config/uniwamp.json`.

### Environment Rules

- ❌ Do NOT install XAMPP, Laragon, WAMPServer, Docker, Herd, or any other local stack.
- ❌ Do NOT download another PHP version.
- ❌ Do NOT assume `php`, `composer`, or `mysql` are available in the system PATH.
- ✅ Always read `config/uniwamp.json` first.
- ✅ Reuse the existing UniWAMP installation.
- ✅ Treat `config/uniwamp.json` as the authoritative configuration for the development environment.