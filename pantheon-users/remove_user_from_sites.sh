#!/bin/bash

USER=$1
TAG=$2
ORG="<enter organization slug>"

# Check to see if SITE is set by command line
if [[ -n "$TAG" ]];
then
	echo "Listing sites for tag: $TAG"
else
	#if not - ask for the SITE name
	read -p "Enter the Pantheon tag: " TAG
	echo "Listing sites for tag: $TAG"
fi

N=0


sitearray=( $(terminus org:site:list $ORG --tag "$TAG" | awk '{ print $1 }' | awk '{if(NR>3)print}' ) )


for i in "${sitearray[@]}"
do
	:
	if [ $i == '-----------------------' ]; then
		exit
	else 
		terminus site:team:remove "$i" "$USER"
	fi

done
