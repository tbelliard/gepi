#!/bin/bash
git config push.default tracking
if git symbolic-ref HEAD 2>/dev/null != 0;then
echo "Aucune branche, abandon"
exit 1
else
if (git rebase $1 &>/dev/null) && (git push -n &>/dev/null);then
echo 'Rebase effectué';
exit 0
else
git reset --hard ORIG_HEAD
git rebase --abort
echo "Rebase abandonné, la branche publié n'est pas directement devant la branche courante"
exit 1
fi
fi
