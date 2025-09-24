# Developer Guide

This file provides guidance when working with code in this repository.

## Repository Overview

This repository contains automation tools for managing WordPress sites on the Pantheon hosting platform. It is currently in migration from bash scripts to a modern Symfony Console PHP CLI application (`bin/pantheon-cli`).

**Current Structure:**
- **Legacy bash scripts**: Original shell scripts (being deprecated)
- **src/Command/**: Symfony Console command classes  
- **bin/pantheon-cli**: Main PHP CLI entrypoint
- **MIGRATION_PLAN.md**: Detailed migration roadmap

## Architecture & Dependencies

**PHP CLI Application:**
- **Framework**: Symfony Console 6.4+ for command structure and I/O
- **Process Management**: Symfony Process component for Terminus CLI calls
- **PHP Requirements**: PHP 8.1+
- **External Dependency**: Terminus CLI (Pantheon's official CLI tool)

**Legacy Scripts** (deprecated):
- Bash scripts that directly call Terminus with shell processing
- Being replaced by structured PHP commands with better error handling

## Key Commands

**Development Setup:**
```bash
# Install PHP dependencies
composer install

# Make CLI executable
chmod +x bin/pantheon-cli

# Run the CLI
./bin/pantheon-cli list
```

**Available PHP Commands:**
```bash
# Authenticate with Pantheon
./bin/pantheon-cli auth:login --token=<machine-token>

# Create multidev environments  
./bin/pantheon-cli multidev:create <site-slug> --envs "env1 env2 env3"

# Get help for any command
./bin/pantheon-cli <command> --help
```

**Legacy Script Usage** (deprecated):
```bash
# Execute legacy scripts directly (being phased out)
./script-name.sh <param1> <param2> <param3>
```

## Command Mapping (Migration Status)

### ✅ pantheon-envs/multi_multidev_setup.sh → multidev:create
- **Legacy**: `./pantheon-envs/multi_multidev_setup.sh` (requires manual variable editing)
- **New**: `./bin/pantheon-cli multidev:create <site-slug> --envs "env1 env2 env3"`
- **Improvements**: JSON output, structured error handling, no manual configuration needed

### 🔄 pantheon-users/remove_user_from_sites.sh → user:remove (planned)
- **Legacy**: `./pantheon-users/remove_user_from_sites.sh <user> <tag> <org>`
- **New**: `./bin/pantheon-cli user:remove <user-email> <org> --tag <tag>` (not yet implemented)

### 🔄 wordpress-plugins/delete_plugin_from_all_environments.sh → plugin:remove (planned)
- **Legacy**: Manual execution with hardcoded logic
- **New**: `./bin/pantheon-cli plugin:remove <site> <plugin-name>` (not yet implemented)

### 🔄 wordpress-plugins/multidev_update_plugins.sh → plugin:update-multidev (planned)  
- **Legacy**: Interactive script with manual site input
- **New**: `./bin/pantheon-cli plugin:update-multidev <site>` (not yet implemented)

## Configuration Requirements

**For PHP CLI:**
- PHP 8.1+ with composer installed
- Terminus CLI in PATH
- Authentication: Set `PANTHEON_MACHINE_TOKEN` env var or use `auth:login` command

**For Legacy Scripts** (deprecated):
- Terminus CLI in PATH (usually `~/.composer/vendor/bin`)
- Manual variable editing required for some scripts
- Assumes existing Terminus authentication

## Testing Commands

```bash
# Test Terminus availability
terminus --version

# Test PHP CLI
./bin/pantheon-cli list

# Test authentication
./bin/pantheon-cli auth:login
```