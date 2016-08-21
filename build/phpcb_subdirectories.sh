#!/bin/bash
 
 
#enable for loops over items with spaces in their name
IFS=$'\n'
 
for dir in `ls "$1"`
do
  if [ -d "$1/$dir" ]; then
    phpcb --log "$1/build/logs" --source "$1/$dir" --output "$1/build/code-browser/$dir"
    #yay, we get matches!
  fi
done
