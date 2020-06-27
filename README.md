# Pantheon Scripts Collection!

This repository contains various scripts I created to make tasks easier while managing WordPress sites on the Pantheon platform.
[Pantheon User Related](https://github.com/jenksed/pantheon-scripts#user-scripts-platform-users)
[WordPress Related](https://github.com/jenksed/pantheon-scripts#wordpress-related)

# User scripts (platform users)

These scripts relate to managing users on the Pantheon hosting platform. 

## ./pantheon-users/remove_user_from_sites.sh
This script will help with removing users who have been granted granular access to sites hosted on Pantheon.
It takes 3 arguments:
* USER
* TAG
* ORG

### Usage
`./remove_user_from_site.sh <user> <tag> <org>
`

# WordPress Related
## ./wordpress-plugin-related/remove_plugin_from_sites.sh
This script will help with removing a plugin from all 3 environments for a given site. 
### Config
### Usage
`./remove_plugin_from_sites.sh 
`
## ./wordpress-plugin-related/multi_dev_update_plugins.sh
This script will help with updating a plugin across all existing WordPress multi-dev environments. 
