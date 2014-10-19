#!/bin/bash

cd $(dirname $(readlink -f $0))

DOC_DIR='/var/www/vds/data/www/man.pushorigin.ru/alium/'


if [ ! -d "$DOC_DIR" ]; then
  mkdir "$DOC_DIR"
  chmod 755 "$DOC_DIR"
fi

~/bin/phpdoc -d scripts/ -t "$DOC_DIR"
