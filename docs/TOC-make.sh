ls ../grimas/[A-Z]* -d |
sed -n -E 's,(.*/([A-Za-z]*))$,* [\2](\1/\2.php) [(docs)](\1/\2.md),p' \
> TOC.md
