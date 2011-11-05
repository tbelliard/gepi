#!/bin/bash
 
#enable for loops over items with spaces in their name
IFS=$'\n'
 
for dir in `ls "$1"`
do
  if [ -d "$1/$dir" ]; then
    phpdoc --directory "$1/$dir" --target "$1/build/api/$dir"
    #yay, we get matches!
  fi
done
