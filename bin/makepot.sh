#!/usr/bin/env bash

# Move to plug-in root
if [[ `pwd` == */bin ]]
then
	pushd ../ > /dev/null
	PLUGIN_RESTORE_DIR=true
else
	PLUGIN_RESTORE_DIR=false
fi

if [ ! -z "${WP_I18N_LIB+xxx}" ] || [ ! -d "$WP_I18N_LIB" ]; then
	WP_I18N_LIB="/usr/lib/wpi18n"
fi

if [ $# -lt 1 ]; then
	PLUGIN_DIR=`pwd`
else
	PLUGIN_DIR="$1"
fi

if [ -z "$2" ]; then
	PLUGIN_TEXT_DOMAIN=""
else
	PLUGIN_TEXT_DOMAIN=$2
fi

if [[ ! $PLUGIN_TEXT_DOMAIN ]]
then
	PLUGIN_TEXT_DOMAIN="wmyc-bogo"
fi

wp i18n make-pot "$PLUGIN_DIR" "$PLUGIN_DIR/lang/$PLUGIN_TEXT_DOMAIN.pot" --slug="woocommerce-myc-bogo" --domain=$PLUGIN_TEXT_DOMAIN --exclude="build,bin,assets,data,.github,.vscode,help,media"

if [ "$PLUGIN_RESTORE_DIR" = true ]
then
	popd > /dev/null
fi