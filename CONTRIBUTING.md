<ul>
<li>Branche [main] est le produit fini,
  ses versions sont autant de pull depuis prototype</li>

<li>Branche [prototype] est le produit semo-fonctionnel
  ses sous versions sont autant de pull depuis diverses branches de developpement</li>
</ul>
<h2>Pour contribuer :</h2>
<ul>

Cloner depuis Prototype. Créer sa branche :
> initiales_nomDeFonction
  

  Commit depuis cette branche : 
>[nom de fonction / d'entité] initiales : titre de commit
fonctionnalités ajoutée 
eventuels TODO restants

_Une méthode fonctionnelle, un twig modifié, une nouvelle entitée = un commit_<br>
_Fin de journée / fonction peu être pull-requested = un push_<br>
_Plus de modification à apporter = push puis suppression de la sous-branche de dev_<br>


Pull-request : 
Lorsqu'une branche de dev est fonctionnelle (hors tests) créer une pull request 
>[prototype] <- [MA_branche]

                
Merge dans [main] :
Lorsque les test ont validé prototype, et que toutes les fonctionnalités attendu à cette itération sont validées, pull depuis [prototype]
