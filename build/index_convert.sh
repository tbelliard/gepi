#! /bin/bash
if [  -z "$1" ]||[ -z "$2" ]; then
	echo "Usage: index_convert.sh ISO-8859-1 UTF-8"
    exit 1    
fi
if [ -n "$(git diff)" ]; then
	echo "the working tree has to be clean. aborting"
    exit 1    
fi
git diff --cached > index.diff
awk '
{
if ($0 ~ /^\+/ && $0 !~ /^\+\+\+ b/ ) 
{
    command = "iconv -f '"$1"' -t '"$2"'";
    print $0 | command;
    close(command);
} else  
{
    print $0}
}' index.diff > index.diff.utf8
if patch -R -p1 < index.diff
then patch -p1 < index.diff.utf8
else
	echo "Unknown error : patch does not apply"
    exit 1
fi
if [ -n "$(git diff)" ];
then echo "Change made to the working tree."
else echo "No change made to the working tree."
fi
rm index.diff
rm index.diff.utf8
