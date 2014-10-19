#!/bin/bash

CASE=$1

if [ -z "$CASE" ]; then
  echo Use {app} case.id
  exit 1
fi

case "$CASE" in
  1)
    php scripts/1-fill-alium.php projects/coolfashion.json data-test/csv/products_total_10082014.csv
    ;;
  
  2)
    php scripts/2-update-alium.php projects/coolfashion.json
    ;;
  
  3)
    php scripts/3-export-merchium.php projects/coolfashion.json
    ;;
  
  4)
    php scripts/4-ali-import.php projects/coolfashion.json data-test/ali-localpages/watch.html 
    ;;

  *)
    echo No such case: "$CASE"
    exit 1
esac