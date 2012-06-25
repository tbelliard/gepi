#! /bin/bash
#ce script mets à jour l'index git en convertissant ci besoin les chaine de caracteres
if [  -z "$1" ]||[ -z "$2" ]; then
	echo "Usage: index_convert.sh UTF-8 ISO-8859-1"
    exit 1    
fi
if [ -n "$(git diff)" ]; then
	echo "the working tree has to be clean. aborting"
    exit 1    
fi
git diff --cached > temp.index.diff
awk '
{
if ($0 ~ /^\+/ && $0 !~ /^\+\+\+ b/ ) 
{   test_res = "";
    command_test = "iconv -s -t '"$1"' -f '"$1"//IGNORE' -o temp.iconv.out 2> /dev/null";
    print $0 | command_test;
    close(command_test);
    getline test_res < "temp.iconv.out";

    if (test_res == $0) {
        print $0;
    } else {
        command = "iconv -t '"$1"' -f '"$2"//IGNORE'";
        print $0 | command;
        close(command);
    }
} else
{
    print $0;
}
}' temp.index.diff > temp.index.diff.utf8
if patch -R -p1 < temp.index.diff
then patch -p1 < temp.index.diff.utf8
else
	echo "Unknown error : patch does not apply"
    exit 1
fi
if [ -n "$(git diff)" ];
then echo "Change made to the working tree."
else echo "No change made to the working tree."
fi
rm temp.index.diff
rm temp.index.diff.utf8
rm temp.iconv.out

