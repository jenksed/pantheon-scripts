# Migration Plan: Shell Scripts to Symfony Console CLI

## Overview
This document outlines the migration plan from bash scripts to Symfony Console PHP commands.

## Completed Migrations

### ✅ pantheon-envs/multi_multidev_setup.sh → multidev:create
- **Command**: `bin/pantheon-cli multidev:create <product> --envs "env1 env2 env3"`
- **Features Added**:
  - JSON output format option (`--format=json`)
  - Table output with structured connection info
  - Error handling and validation
  - Interactive environment input if not provided
  - Structured JSON parsing of Terminus output

## Planned Migrations

### 🔄 pantheon-users/remove_user_from_sites.sh → user:remove
**Original functionality**: Remove a user from all sites in an organization with a specific tag.

**New command structure**:
```bash
bin/pantheon-cli user:remove <user-email> <org> --tag <tag>
```

**Implementation notes**:
- Use `terminus org:site:list <org> --tag <tag> --format=json` for structured site list
- Loop through sites in PHP instead of bash array processing
- Add `--dry-run` option to preview changes
- Output table showing success/failure for each site

### 🔄 wordpress-plugins/delete_plugin_from_all_environments.sh → plugin:remove
**Original functionality**: Remove a WordPress plugin from dev, test, and live environments.

**New command structure**:
```bash
bin/pantheon-cli plugin:remove <site> <plugin-name> [--environments "dev,test,live"]
```

**Implementation notes**:
- Default environments: dev, test, live
- Allow custom environment list
- Use `terminus remote:wp <site>.<env> -- plugin delete <plugin>`
- Add confirmation prompt unless `--force` is used
- Show progress and results in table format

### 🔄 wordpress-plugins/multidev_update_plugins.sh → plugin:update-multidev
**Original functionality**: Create multidev environment and update all plugins.

**New command structure**:
```bash
bin/pantheon-cli plugin:update-multidev <site> [--env-name <name>]
```

**Implementation notes**:
- Generate environment name with current month-year if not provided
- Set SFTP connection mode before updates
- Update all plugins and WordPress core
- Clear cache after updates
- Combine with multidev creation logic

## Additional Commands to Add

### 🆕 auth:login
**Purpose**: Authenticate with Pantheon using machine token.
```bash
bin/pantheon-cli auth:login [--token <token>]
```

### 🆕 site:list
**Purpose**: List all sites in an organization.
```bash
bin/pantheon-cli site:list <org> [--tag <tag>]
```

### 🆕 env:info
**Purpose**: Get environment information.
```bash
bin/pantheon-cli env:info <site>.<env>
```

## Migration Steps

1. **Phase 1** (✅ Complete): 
   - Set up Symfony Console project
   - Implement `multidev:create` command
   - Add `auth:login` command

2. **Phase 2** (Next):
   - Implement `user:remove` command
   - Test against existing script functionality
   - Mark original script as deprecated

3. **Phase 3**:
   - Implement `plugin:remove` command
   - Implement `plugin:update-multidev` command
   - Add comprehensive testing

4. **Phase 4**:
   - Delete original shell scripts
   - Update documentation
   - Final testing and cleanup

## Benefits of Migration

- **Better Error Handling**: Structured exception handling vs bash error checking
- **JSON Output**: Machine-readable output for automation
- **Input Validation**: Argument validation and type checking
- **Progress Indicators**: Better user feedback during long operations
- **Extensibility**: Easy to add new commands and shared functionality
- **Testing**: Unit testable PHP code vs shell script testing
- **Cross-platform**: PHP runs consistently across environments

## Breaking Changes

- Command syntax changes (documented above)
- Output format changes (now supports both table and JSON)
- Environment variable expectations (PANTHEON_MACHINE_TOKEN)
- Error exit codes may differ

## Backward Compatibility

During migration phase, both shell scripts and PHP commands will coexist. Original scripts will be marked as deprecated with warnings pointing to new commands.