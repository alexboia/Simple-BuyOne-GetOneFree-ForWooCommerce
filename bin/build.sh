#!/usr/bin/env bash

# Move to plug-in root
if [[ `pwd` == */bin ]]
then
	pushd ../ > /dev/null
	PLUGIN_RESTORE_DIR=true
else
	PLUGIN_RESTORE_DIR=false
fi

# Store some stuff for later use
PLUGIN_CDIR=$(pwd)

PLUGIN_BUILD_ROOTDIR="$PLUGIN_CDIR/build"
PLUGIN_BUILD_OUTDIR="$PLUGIN_BUILD_ROOTDIR/output"
PLUGIN_BUILD_TMPDIR="$PLUGIN_BUILD_ROOTDIR/tmp"

PLUGIN_VERSION=$(awk '{IGNORECASE=1}/Version:/{print $NF}' ./wmyc-bogo-plugin-main.php | awk '{gsub(/\s+/,""); print $0}')
PLUGIN_BUILD_NAME="wmyc-bogo.$PLUGIN_VERSION.zip"

# Ensure all output directories exist
ensure_out_dirs() {
	echo "Ensuring output directory structure..."

	if [ ! -d $PLUGIN_BUILD_ROOTDIR ]
	then
		mkdir $PLUGIN_BUILD_ROOTDIR
	fi

	if [ ! -d $PLUGIN_BUILD_OUTDIR ] 
	then
		mkdir $PLUGIN_BUILD_OUTDIR
	fi

	if [ ! -d $PLUGIN_BUILD_TMPDIR ] 
	then
		mkdir $PLUGIN_BUILD_TMPDIR
	fi
}

clean_tmp_dir() {
	echo "Cleaning up temporary directory..."
	rm -rf $PLUGIN_BUILD_TMPDIR/*
	rm -rf $PLUGIN_BUILD_TMPDIR/.htaccess
}

# Clean output directories
clean_out_dirs() {
	echo "Ensuring output directories are clean..."
	rm -rf $PLUGIN_BUILD_OUTDIR/* > /dev/null
	rm -rf $PLUGIN_BUILD_TMPDIR/* > /dev/null
	rm -rf $PLUGIN_BUILD_TMPDIR/.htaccess > /dev/null
}

# Copy over all files
copy_source_files() {
	echo "Copying all files..."
	cp ./readme.txt "$PLUGIN_BUILD_TMPDIR/readme.txt"
	cp ./index.php "$PLUGIN_BUILD_TMPDIR"
	cp ./wmyc-bogo-plugin-*.php "$PLUGIN_BUILD_TMPDIR"
	cp ./.htaccess "$PLUGIN_BUILD_TMPDIR"

	mkdir "$PLUGIN_BUILD_TMPDIR/views" && cp -r ./views/* "$PLUGIN_BUILD_TMPDIR/views"
	mkdir "$PLUGIN_BUILD_TMPDIR/includes" && cp -r ./includes/* "$PLUGIN_BUILD_TMPDIR/includes"
	mkdir "$PLUGIN_BUILD_TMPDIR/lang" && cp -r ./lang/* "$PLUGIN_BUILD_TMPDIR/lang"
}

generate_package() {
	echo "Generating archive..."
	pushd $PLUGIN_BUILD_TMPDIR > /dev/null
	zip -rT $PLUGIN_BUILD_OUTDIR/$PLUGIN_BUILD_NAME ./ > /dev/null
	popd > /dev/null
}

echo "Using version: ${PLUGIN_VERSION}"

ensure_out_dirs
clean_out_dirs
copy_source_files
generate_package
clean_tmp_dir

echo "DONE!"

if [ "$PLUGIN_RESTORE_DIR" = true ]
then
	popd > /dev/null
fi