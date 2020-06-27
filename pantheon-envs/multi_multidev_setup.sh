#!/bin/bash

# Exit immediately on errors, and echo commands as they are executed.
set -ex

# Set the $PATH to include the global composer bin directory.
PATH=$PATH:~/.composer/vendor/bin

# Set the $ENV# to ensure the multi-devs are named appropriately. Ex. ENVS="ENV1 ENV2 ENV3"
ENVS="<ENVNAME1> <ENVNAME2> <ENVNAME3>"
SITE="<REPLACE WITH SITE SLUG>"
TEMPPASS="<TEMPPASS1> <TEMPPASS2> <TEMPPASS3>"

# Set the $product to create multi-dev environments for
echo "Which site would like to create multi-devs for followed by [ENTER]:"

read PRODUCT

# Create a new environment for ENVS.
for MDEV in $ENVS
do
	echo "Creating $PRODUCT.$MDEV"
	terminus multidev:create $PRODUCT.live $MDEV
	echo "Connection info for $PRODUCT.$MDEV"
	echo "----------------------------------"
	terminus connection:info $PRODUCT.$MDEV --fields 'SFTP Command' |awk '{ print $6 }' | cut -f 2 -d '@'
	terminus connection:info $PRODUCT.$MDEV --fields 'SFTP Command' |awk '{ print $5 }'
	terminus connection:info $PRODUCT.$MDEV --fields 'SFTP Command' |awk '{ print $6 }' | cut -f 1 -d '@'
        echo "~/code"
    #terminus remote:wp $PRODUCT.$MDEV -- user create '$MDEV_admin' $EMAIL --role=administrator 
done
echo "MultiDevs created"
