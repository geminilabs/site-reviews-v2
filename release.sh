#!/bin/sh
# By Paul Ryley, based on work by Mike Jolley
# License: GPLv3

# ----- START EDITING HERE -----

DEFAULT_GIT_BRANCH="master"

PLUGIN_SLUG="site-reviews"

# ----- STOP EDITING HERE -----

set -e
clear

# VARS
ROOT_PATH=$(pwd)"/"
PLUGIN_VERSION=`grep "Version:" $ROOT_PATH$PLUGIN_SLUG.php | awk -F' ' '{print $NF}' | tr -d '\r'`
STABLE_VERSION=`grep "^Stable tag:" ${ROOT_PATH}readme.txt | awk -F' ' '{print $NF}' | tr -d '\r'`
SVN_REPO="https://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
TEMP_GITHUB_REPO=${PLUGIN_SLUG}"-git"
TEMP_SVN_REPO=${PLUGIN_SLUG}"-svn"

# ASK INFO
echo "--------------------------------------------"
echo "Deploy to WordPress.org SVN                 "
echo "--------------------------------------------"
echo "Plugin version: $PLUGIN_VERSION             "
echo "Stable version: $STABLE_VERSION             "
echo "--------------------------------------------"
echo ""

if [[ "$PLUGIN_VERSION" != "$STABLE_VERSION" && "$STABLE_VERSION" != "trunk" ]]; then
	echo "Version mismatch. Exiting..."
	echo ""
	exit 1;
else
	echo "Before continuing, confirm that you have done the following:"
fi

echo ""
read -p " - Updated the changelog for "${PLUGIN_VERSION}" and appended it to readme.txt?"
read -p " - Set stable tag in the readme.txt file to "${PLUGIN_VERSION}"?"
read -p " - Set version in the main file to "${PLUGIN_VERSION}"?"
read -p " - Updated the POT file?"
read -p " - Committed all changes to the master branch on GITHUB?"
echo ""
read -p "PRESS [ENTER] TO BEGIN RELEASING "${PLUGIN_VERSION}
clear

# DELETE OLD TEMP DIRS
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO

# CHECKOUT SVN DIR IF NOT EXISTS
if [[ ! -d $TEMP_SVN_REPO ]]; then
	echo "Checking out WordPress.org plugin repository"
	svn checkout $SVN_REPO $TEMP_SVN_REPO || { echo "Unable to checkout repo."; exit 1; }
fi

# LIST BRANCHES
clear
git fetch origin
echo "WHICH BRANCH DO YOU WISH TO DEPLOY?"
git branch -r || { echo "Unable to list branches."; exit 1; }
echo ""
read -p "origin/" BRANCH

echo ${BRANCH:-$DEFAULT_GIT_BRANCH}
# Switch Branch
echo "Switching to branch"
mkdir -p $ROOT_PATH$TEMP_GITHUB_REPO
git archive ${BRANCH:-$DEFAULT_GIT_BRANCH} | tar -x -f - -C $ROOT_PATH$TEMP_GITHUB_REPO || { echo "Unable to archive/copy branch."; exit 1; }

echo ""
read -p "PRESS [ENTER] TO DEPLOY BRANCH "${BRANCH:-$DEFAULT_GIT_BRANCH}

# MOVE INTO SVN DIR
cd $ROOT_PATH$TEMP_SVN_REPO

# COPY ASSETS to SVN DIR
cp $ROOT_PATH/src/assets/* ./assets/

# UPDATE SVN
echo "Updating SVN"
svn update || { echo "Unable to update SVN."; exit 1; }

# DELETE TRUNK
echo "Replacing trunk"
rm -Rf trunk/

# COPY GIT DIR TO TRUNK
cp -R $ROOT_PATH$TEMP_GITHUB_REPO trunk/

# DO THE ADD ALL NOT KNOWN FILES UNIX COMMAND
svn add --force * --auto-props --parents --depth infinity -q

# DO THE REMOVE ALL DELETED FILES UNIX COMMAND
MISSING_PATHS=$( svn status | sed -e '/^!/!d' -e 's/^!//' )

# iterate over filepaths
for MISSING_PATH in $MISSING_PATHS; do
    svn rm --force "$MISSING_PATH"
done

# COPY TRUNK TO TAGS/$PLUGIN_VERSION
echo "Copying trunk to new tag"
svn copy trunk tags/${PLUGIN_VERSION} || { echo "Unable to create tag."; exit 1; }

# DO SVN COMMIT
clear
echo "Showing SVN status"
svn status

# PROMPT USER
echo ""
read -p "PRESS [ENTER] TO COMMIT RELEASE "${PLUGIN_VERSION}" TO WORDPRESS.ORG SVN"
echo ""

# DEPLOY
echo ""
echo "Committing to WordPress.org...this may take a while."
svn commit -m "Release "${PLUGIN_VERSION}", see readme.txt for the changelog." || { echo "Unable to commit."; exit 1; }

# REMOVE THE TEMP DIRS
echo "CLEANING UP"
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO
rm -Rf $ROOT_PATH$TEMP_SVN_REPO

# DONE, BYE
echo "All DONE"
