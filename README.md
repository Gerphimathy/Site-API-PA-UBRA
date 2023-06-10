<h1>Uballers test de recrutement</h1>

<p>
Voici le code pour le test de recrutement que j'ai passé pour la société uballers. <br>
Le test est compris d'une partie js, css-html et php, et i vise à construire une page de connexion fonctionelle.

</p>

<h3>Temps passé:</h3>
php : 3-4 heures<br>
js  : 2 heures<br>
html - css : 1-2 heures<br>

<h3>Mise en place:</h3>

<p>Afin d'être mis en place, cette démo aura besoin de:</p>

<ul>
<li>Un host local pouvant éxécuter un php 8.1 au minimum</li>
<li>Une base de donnée mysql en local</li>
<li>Un utilisateur mysql ayant accès en écriture au base de données locales</li>
<li>La racine du host devra se trouver dans le dossier public et non pas à la racine du projet</li>
<li>Une base de donnée locale devra être créée en éxécutant le script SQL trouvable dans /api/database</li>
<li>Dans /api/database encore une fois, il faudra créer un fichier nommé "CREDENTIALS.php" tout en majuscule<br>
il Faudra ensuite le remplir de la manière suivante:<br>
<ul>
<li>const HOST = "localhost";//TODO possibilité de changer</li>
<li>const DB = "uballers";//TODO possibilité de changer</li>
<li>const USER = //TODO utilisateur SQL local</li>
<li>const PASS = //TODO mot de passe de l'utilisateur SQL local;</li>
<li>const CHARSET = "utf8";</li>
<li>const SALT = //TODO ajouter une clé de sallage;</li>
</ul>
</li>

</ul>