---
title: "Code source"
weight: 25
type: 'docs'
# bookFlatSection: false
# bookToc: true
# bookHidden: false
# bookCollapseSection: false
# bookComments: true
---


# Accéder aux sources de Gepi

Les sources de Gepi sont hébergées sur Github. Le dépôt officiel est accessible à l'adresse https://github.com/tbelliard/gepi.

Pour récupérer les sources de Gepi, vous pouvez vous connecter sur cette interface, ou bien cloner le dépôt à l'aide d'un client Git, à partir de l'URL git://github.com/tbelliard/gepi.git (accessible en lecture seule).

## Organisation du code

Le code est organisé en différentes branches : la branche master contient le code le plus récent, avec les développements en cours (elle ne doit donc pas être utilisée en production, car potentiellement instable et pouvant induire des dysfonctionnements ou corruptions de données) ; ensuite pour chaque version stable (1.7.4, 1.6.0, etc.) une branche est créée, où sont apportées les corrections de bugs au fur et à mesure, si nécessaire.

**master**

La branche master contient la version de développement sur laquelle sont intégrées les nouvelles fonctionnalités. D'une manière générale, le master est toujours à peu près fonctionnel. La seule contrainte est de forcer la mise à jour lorsqu'il n'y a pas eu de changement de version Gepi dans le code.

**branches par version**

A chaque sortie d'une nouvelle version de Gepi, une branche est créée sur la base du numéro de version (par exemple 'release-1.7.4'). Leur utilité principale est le gel des fonctionnalités pour la sortie d'une nouvelle version stable et le suivi des corrections de bugs lorsque la nouvelle version a été diffusée.

Par exemple lors du gel des fonctionnalités de la 1.7.4, une nouvelle branche est créée (release-1.7.4) qui contient le code de la 1.7.4. Aucune nouvelle fonctionnalité n'est ajoutée à cette branche, qui n'est modifiée que dans le cas de corrections de bugs.

Ainsi, lorsque la 1.7.4-stable est diffusée et que les développeurs travaillent déjà sur la version suivante avec de nouvelles fonctionnalités, si un bug est découvert (sur la 1.7.4) il sera corrigé sur la branche release-1.7.4 (et sur le master, le cas échéant).

Enfin, une nouvelle branche peut aussi être créée dans le cas d'un développement qui rendrait trop instable (ou inutilisable) le trunk. Dans ce cas, la branche sera préfixée 'dev', et ne sera que temporaire. Une fois le développement stabilisé, il est réintégré au master et la branche est supprimée.
