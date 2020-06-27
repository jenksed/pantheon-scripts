#!/bin/bash

SITES="<add pantheon site slugs seperated  by a comma>"
ORG="<enter organization slug>"
PLUGIN="<enter the plugin name>"

# Set the $PATH to include the global composer bin directory.
export PATH="$PATH:$HOME/terminus/vendor/bin"

# loop through SITES array and delete plugins
for i in ${SITES}
do
		terminus wp "$i.live" -- plugin delete $PLUGIN
		terminus wp "$i.test" -- plugin delete $PLUGIN
		terminus wp "$i.dev" -- plugin delete $PLUGIN
done
