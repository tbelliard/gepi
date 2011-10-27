#!/bin/bash
# $1 le nom de la base de départ
# $2 le nom de la base d'arrivée
if [ $# -lt 3 ]
then 
echo "Il faut une base de depart et une base cible"
exit 1
fi

if [ $1 == '-f' ]
	then 
	if [ $# -ne 3 ]
		then
		echo "Il faut une base de depart et une base cible"
		exit 1
	fi
	db1=$2
	db2=$3
	./mysql -u root -e "drop database IF EXISTS $db2"
	else
	db1=$1
	db2=$2
	
fi

dbquery=$(./mysql -u root -e "show databases like '$db1'";)
if [ ${#dbquery} == 0 ]
then 
echo "la base de depart n'existe pas, operation annule"
exit 1
fi


dbquery=$(./mysql -u root -e "show databases like '$db2'";)
if [ ${#dbquery} != 0 ]
then 
echo "la base cible existe deja, operation annulee"
exit 1
fi

./mysql -u root -e "CREATE DATABASE $db2 CHARACTER SET latin1 COLLATE latin1_swedish_ci"
./mysqldump -u root $db1 | ./mysql -u root $db2
./mysql -u root -e "GRANT ALL ON $db2.* to gepi@localhost"
