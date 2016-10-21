#!/bin/sh

# By Paul Ryley, based on work by Mike Jolley ;)
# License: GPLv3

# ----- START EDITING HERE -----

# THE GITHUB ACCESS TOKEN, GENERATE ONE AT: https://github.com/settings/tokens
# GITHUB_ACCESS_TOKEN="TOKEN"

# The slug of your WordPress.org plugin
PLUGIN_SLUG="site-reviews"

# GITHUB user who owns the repo
# GITHUB_REPO_OWNER="geminilabs"

# GITHUB Repository name
# GITHUB_REPO_NAME="site-reviews"

# ----- STOP EDITING HERE -----

set -e
clear

# VARS
ROOT_PATH=$(pwd)"/"
PLUGIN_VERSION=`grep "Version:" $ROOT_PATH$PLUGIN_SLUG.php | awk -F' ' '{print $NF}' | tr -d '\r'`
STABLE_VERSION=`grep "^Stable tag:" ${ROOT_PATH}readme.txt | awk -F' ' '{print $NF}' | tr -d '\r'`
TEMP_GITHUB_REPO=${PLUGIN_SLUG}"-git"
TEMP_SVN_REPO=${PLUGIN_SLUG}"-svn"
SVN_REPO="https://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
# GIT_REPO="git@github.com:"${GITHUB_REPO_OWNER}"/"${GITHUB_REPO_NAME}".git"
DEFAULT_BRANCH="master"

# ASK INFO
echo "--------------------------------------------"
echo "Deploy to WordPress.org SVN                 "
echo "--------------------------------------------"
echo "Plugin version: $PLUGIN_VERSION             "
echo "Stable version: $STABLE_VERSION             "
echo "--------------------------------------------"
echo ""
echo "Before continuing, confirm that you have done the following :)"
echo ""
read -p " - Added a changelog for "${PLUGIN_VERSION}"?"
read -p " - Set stable tag in the readme.txt file to "${PLUGIN_VERSION}"?"
read -p " - Set version in the main file to "${PLUGIN_VERSION}"?"
read -p " - Updated the POT file?"
read -p " - Committed all changes up to GITHUB?"
echo ""
read -p "PRESS [ENTER] TO BEGIN RELEASING "${PLUGIN_VERSION}
clear

# DELETE OLD TEMP DIRS
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO

# CHECKOUT SVN DIR IF NOT EXISTS
if [[ ! -d $TEMP_SVN_REPO ]];
then
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

echo ${BRANCH:-$DEFAULT_BRANCH}
# Switch Branch
echo "Switching to branch"
mkdir -p $ROOT_PATH$TEMP_GITHUB_REPO
git archive ${BRANCH:-$DEFAULT_BRANCH} | tar -x -f - -C $ROOT_PATH$TEMP_GITHUB_REPO || { echo "Unable to archive/copy branch."; exit 1; }

echo ""
read -p "PRESS [ENTER] TO DEPLOY BRANCH "${BRANCH:-$DEFAULT_BRANCH}

# MOVE INTO SVN DIR
cd $ROOT_PATH$TEMP_SVN_REPO

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

# COPY TRUNK TO TAGS/$VERSION
echo "Copying trunk to new tag"
svn copy trunk tags/${VERSION} || { echo "Unable to create tag."; exit 1; }

# DO SVN COMMIT
clear
echo "Showing SVN status"
svn status

# PROMPT USER
echo ""
read -p "PRESS [ENTER] TO COMMIT RELEASE "${VERSION}" TO WORDPRESS.ORG AND GITHUB"
echo ""

# CREATE THE GITHUB RELEASE
# echo "Creating GITHUB release"
# API_JSON=$(printf '{ "tag_name": "v%s","target_commitish": "%s","name": "v%s", "body": "Release of version %s", "draft": false, "prerelease": false }' $VERSION $BRANCH $VERSION $VERSION)
# RESULT=$(curl --data "${API_JSON}" https://api.github.com/repos/${GITHUB_REPO_OWNER}/${GITHUB_REPO_NAME}/releases?access_token=${GITHUB_ACCESS_TOKEN})

# DEPLOY
echo ""
echo "Committing to WordPress.org...this may take a while..."
svn commit -m "Release "${VERSION}", see readme.txt for the changelog." || { echo "Unable to commit."; exit 1; }

# REMOVE THE TEMP DIRS
echo "CLEANING UP"
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO
rm -Rf $ROOT_PATH$TEMP_SVN_REPO

# DONE, BYE
echo "RELEASER DONE :D"
