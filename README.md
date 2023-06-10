<h1>Ultimate Boat Racing Arcade API Web</h1>

<p>
API Web pour le projet Ultimate Boat Racing Arcade de l'équipe 10
</p>



<h3>Mise en place:</h3>
<ul>
    <li>Un host local pouvant éxécuter un php 8.1 au minimum</li>
    <li>Une base de donnée mysql en local</li>
    <li>Un utilisateur mysql ayant accès en écriture aux bases de données locales</li>
    <li>La racine du host devra se trouver dans le dossier public et non pas à la racine du projet</li>
    <li>Une base de donnée locale devra être créée en exécutant le script SQL trouvable dans /api/database <br> <b>Important:</b> Penser à mettre le script à jour après modifications</li>
    <li>Dans /api/database encore une fois, il faudra créer un fichier nommé "CREDENTIALS.php" tout en majuscule<br>
    il Faudra ensuite le remplir de la manière suivante:<br>
        <ul>
            <li>const HOST = "";</li>
            <li>const DB = "";</li>
            <li>const USER = ""</li>
            <li>const PASS = "";</li>
            <li>const CHARSET = "";</li>
            <li>const SALT = ""</li>
            <li>const TABLE_PREFIX = ""; <br> <b>Important:</b> TABLE_PREFIX doit correspondre avec Createdb.sql</li>
        </ul>
    </li>
</ul>


<h2>Utilisation:</h2>
<p> L'api communique via JSON, sous un modèle REST et à travers les différents signaux html
<br> Si une méthode GET prend des paramètres, il faudra les passer en query string dans l'url
<br> Si un autre type de méthode prends des paramètres, il faudra les passer dans le body JSON de la requête
</p>

<h3>Routes & Méthodes</h3>
<ul>
    <li><b>/login</b> <br>Gestion Utilisateur
        <ul>
            <li>GET : Retourne html view de login.php</li>
            <li>POST : Tentative de connexion
                <br>Paramètres:
                <ul>
                    <li>login</li>
                    <li>password</li>
                </ul>
                Retour:
                <ul>
                    <li>400 - Requête incorrecte, Voir Format Erreurs 400</li>
                    <li>403 - Erreur de connexion (mot de passe ou login incorrects)</li>
                    <li>500 - Erreur Interne, Voir Format Erreurs 500</li>
                    <li>200 - Connexion réussie
                        <ul>
                            <li>token : string 30</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>PUT : Création Utilisateur
                <br>Paramètres:
                <ul>
                    <li>login : string</li>
                    <li>password : string</li>
                </ul>
                Retour:
                <ul>
                    <li>400 - Requête incorrecte, Voir Format Erreurs 400</li>
                    <li>409 - Login déjà utilisé</li>
                    <li>500 - Erreur Interne, Voir Format Erreurs 500</li>
                    <li>200 - Création Utilisateur Réussie</li>
                </ul>
            </li>
            <li>PATCH : Modification Utilisateur
                <br>Paramètres:
                <ul>
                    <li>token : string</li>
                    <li>key : string (username, password, id_code)</li>
                    <li>value : mixed (optionnel si clé est id_code)</li>
                </ul>
                Retour:
                <ul>
                    <li>400 - Requête incorrecte, Voir Format Erreurs 400</li>
                    <li>403 - Token Invalide</li>
                    <li>500 - Erreur Interne, Voir Format Erreurs 500</li>
                    <li>200 - Modification Utilisateur Réussie</li>
                </ul>
            </li>
        </ul>
    </li>
    <li><b>/auth</b> <br> Gestion de l'authentification
        <ul>
            <li>GET : état du token
            <br> Paramètres:
                <ul>
                    <li>token</li>
                </ul>
                Retour:
                <ul>
                    <li>400 : Requête incorrecte, Voir Format Erreurs 400</li>
                    <li>500 : Erreur Interne, Voir Format Erreurs 500</li>
                    <li>200 : Obtention état du token réussi
                        <ul>
                            <li>token : string 30 (le token testé)</li>
                            <li>agent : string ? (l'agent html ayant effectué la requête, chaque utilisateur peut avoir un token par agent)</li>
                            <li>isvalid : bool</li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

<h3>Format Erreurs</h3>
<ul>
    <li>Format Général:
        <ul>
            <li>error : objet</li>
            <ul>
                <li>code : int (code de l'erreur php si applicable)</li>
                <li>message : string (message de débug)</li>
                <li>info : objet (info selon le code de l'erreur)</li>
            </ul>
        </ul>
    </li>
    <li>
    Erreur 400 Format Info :
        <ul>
            <li>parameter : string ? (Paramètre Ayant causé l'erreur)</li>
            <li>case : int 
                <ul>
                    <li>0. Paramètre vide/manquant</li>
                    <li>1. Longueur du paramètre trop grande</li>
                    <li>2. Format du paramètre invalide (ex: invalid email)</li>
                    <li>3. Longueur du paramètre trop petite</li>
                    <li>4. Paramètre devant prendre des valeurs spécifiques possède une valeur erronée (ex: Login Patch Key)</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
    Erreur 500 Format Info :
        <ul>
            <li>step : string ? (étape ayant échoué)</li>
            <li>sqlerror : string ? (message d'erreur sql)</li>
        </ul>
    </li>
</ul>
