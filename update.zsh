#!/bin/zsh

TRAPZERR() {
  LOG "Recovering..."
  sudo -u www-data php -c ./php.ini src/artisan cataclysm:rebuild $dir.bak
  php src/artisan up
  exit
}

LOG() {
  echo "[LOG] $1"
}

cd $0:A:h

dir=Cataclysm-DDA-master
rm -f master.zip
if [[ -d $dir ]]; then
  [[ ! -d $dir.bak ]] || rm -rdf $dir.bak
  mv -f $dir $dir.bak
fi
LOG "Downloading latest source code..."
curl -LOs https://github.wuyanzheshui.workers.dev/CleverRaven/Cataclysm-DDA/archive/master.zip
LOG "Unzipping..."
unzip -qo master.zip

echo "#define VERSION \"$(date -u +'%Y-%m-%dT%H:%M:%SZ')\"" > $dir/src/version.h
msgfmt $dir/lang/po/zh_CN.po -o locale/zh_CN/LC_MESSAGES/cataclysm-dda.mo
cp -f src/public/diff.json{,.bak}
LOG "Generating diff..."
python3 get_diff.py Cataclysm-DDA-master.bak Cataclysm-DDA-master src/public/diff.json

LOG "Rebuilding database..."
php src/artisan down
# sudo -u www-data php -c ./php.ini src/artisan cache:clear
sudo -u www-data php -c ./php.ini src/artisan cataclysm:rebuild $dir
php src/artisan up

LOG "Generating doc..."
cp doxygen_conf.txt Cataclysm-DDA-master/doxygen_doc/doxygen_conf.txt
pushd Cataclysm-DDA-master
doxygen doxygen_doc/doxygen_conf.txt
popd
if [[ -d src/public/doc ]]; then
  rm -rdf src/public/doc
fi
mv Cataclysm-DDA-master/doxygen_doc/html src/public/doc

# https://juejin.im/entry/5901af2e1b69e60058be2134

# cp locale/zh_CN/LC_MESSAGES/cataclysm-dda.mo /usr/share/locale/zh_CN/LC_MESSAGES/
