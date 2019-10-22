#!/bin/sh

if [ $# -ne 1 ]; then
	echo "usage: $0 VERSION";
	exit;
fi

# update version and changelog
vim src/readme.txt
vim src/sitemapgenerator.php

# TODO automate version update with sed
# sed -i "s/<version>.*<\/version>/<version>$1<\/version>/g" src/sitemapgenerator.xml

# commit changes and create git tag
git add .
git commit
git push
git tag -a $1 -m "published version $1"
git push origin $1

# build package
./build.sh

# copy to static.marcobeierer.com
cp packages/sitemapgenerator-latest.zip ~/www/websites/static.marcobeierer.com/wordpress-plugins/sitemapgenerator/sitemapgenerator-latest.zip
cp packages/sitemapgenerator-latest.zip ~/www/websites/static.marcobeierer.com/wordpress-plugins/sitemapgenerator/sitemapgenerator-${1}.zip

# push changes to static.marcobeierer.com
push-static.marcobeierer.com.sh

# update svn and publish new version
cp -r src/* .svn/trunk/
cd .svn
svn add trunk/*
svn add trunk/*/* 				# workaround to add files in subdirectories
svn add trunk/*/*/* 			# needs to be changed if we have more than three layers
svn ci -m "synced with git"
svn cp trunk tags/${1}
svn ci -m "published version $1"
cd ..

# TODO delete removed files in svn/trunk
# always use `svn rm filename` for that and commit afterwards
