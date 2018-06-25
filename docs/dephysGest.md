# DephyGest 
## BackEnd ( Laravel )

### Modèles
##### Introduction
En Laravel chaque modèle doit :

- Représenter une table de la base de données
- Hériter directement du Eloquent Model "Illuminate\Database\Eloquent\Model" .

Dans un projet comme celui ci il y aura sûrement besoin qu'un modèle hérite d'un autre modèle, exemple : les modèls Personne et Entreprise sont les deux des Contacts mais avec quelques attributs et méthodes différentes, donc dans un monde idéal on aurait pu faire : 
```php
class Contact extends Illuminate\Database\Eloquent\Model
{
public $nom;
}
class Personne extends Contact
{
}
class Entreprise extends Contact
{
}
//  Et Donc on aurait pu faire un truc comme ça :
$personne = new Personne();
$personne->nom = 'some name';
```
Mais là il y a un petit problème : Laravel ne permet pas qu'un modèle hérite d'un autre modèle.

##### Solution
Utiliser les relations de Eloquent et redéfinir quelques méthodes sur nos modèles afin de simuler un héritage

##### Explications

Pour régler ce problème dans ce cas de figure, c'est à dire simuler que Personne et Entreprise hérite de Contact, on aura besoin de 2 choses : 

- Un moyen d'accèder à partir d'une instance de Contact à l'instace de Personne (ou d'Entreprise) si elle existe.
- Un moyen d'accèder à partir de d'une instance de Personne (ou de Entreprise) à l'instance de Contact qui lui correspond.

Une fois qu'on aurra ces deux méthodes, nommons les méthode_1 et méthode_2 resp,
ça sera facile de simuler un héritage, on va prendre 2 cas d'utilisations pour voir comment ça pourrait marcher : 
Premier cas : on veut afficher une instance de Contact mais on sait pas si ça représente juste un contact ou ça représente une instance de Personne (ou encore une instance de Entreprise). Sachant que l'affichage diffère entre une instance contact et une instance de Personne ( cette dernière contiendra plus d'information à afficher )
```php
// on va créer une instance de Contact à partir de la base de données
$contact = Contact::first();
if (méthode_1($contact) instanceof Personne)
{
    //On affiche une instance de Personne
}else if ( méthode_1($contact) instanceof Entreprise)
{
    //On affiche une instance d'Entreprise
}else
{
    // On affiche une instance de Contact
}
```

Deuxième cas : On reprendra l'exemple du tout début où on voulait modifier l'attribut nom d'une instance de Personne mais qui appartenait à Contact.

```php
$personne = new Personne();
méthode_2($personne)->nom = 'some name';
// sachant que méthode_2 retourne une instance de contact qui correspond à l'instance de la personne
```


##### Un peu plus technique 

La méthode_1 : Pour accéder de la classe parent aux classes héritantes, On utilisera Laravel Eloquent relationships. Si une classe à qu'une seule classe qui hérite d'elle, on utilisera oneToOne Relationship, si elle a plusieurs classes qui héritent on utilisera Polymorphic Relationship.

[Laravel Eloquent Relationships Documentation](https://laravel.com/docs/5.5/eloquent-relationships)

La méthode_2 :  Elle consiste à redéfinir les méthodes utilisés pour retrouver la valeur d'un attribut, la modifier, enregistrer ou supprimer le modèle de la base de données. on s'assurera que sur ces méthodes redifinies on traite le fait que cette classe hérite  d'une autre classe  (c'est ce qu'on essaie de faire au moins ), par exemple si on essaie d'accéder à un attribut $nom d'une instance de Personne, on vérifiera si l'attribut existe chez cette instance ou sur l'instance de Contact.
Ces fonction redéfinies sont : 
```php

    public function save(array $options = []);
    public function __set($key, $value);
    public function __get($key);
    public function delete();
    
```

Aussi afin de pouvoir avoir toujours accès à l'instance de la classe mère ( Contact dans notre exemple), on va garder dans notre classe fille un attribut qui contiendera l'instance de la classe mère ($contact dans notre exemple). Dernière chose avant de faire n'importe quelle opérations de celle qu'on vient de mentionner en haut il faudra s'assurer que l'instance de la classe mère correspond à ce qui se trouve dans la base de données, c'est pour ceci qu'on à la méthode "boot{nom_de_l'instance_de_la_classe_mère}()" - par exemple  " bootContact (); " -  qui mettera à jour l'instance de la classe mère à partir de la base de données.





 

Dans ce qui suit 

Admin

Architecture 
Problème d’héritage

# Factures

* Une réduction se fait techniquement sur le prix HT, mais on devrait quand même laisser la possibilité à l'utilisateur de faire une réduction sur le montant total, par pourcentage ou par un montant (on s'occupera de calculer le HT en back-end)
* Les écheances d'une facture ne doivent pas être modifiées.
* Les écheances doivent être par mois ( ex chaque 1er du mois)

### Notes
J'ai ajouté une règle de validation 
```php 
    "array.*.objectField_A: greater_than_or_equal_field:array.*.objectField_B" 
```
Qui vérifie pour chaque objet contenu dans le tableau (array), si objectField_A >= objectField_B. Dans le cas où 'objectField_B' n'existe pas dans l'objet, c'est validé automatiquement.
Le code pour cette règle se trouve dans : `app\Providers\AppServiceProvider.php`

## Modélisation d'une réduction sur un produit (DocumentEntry) ou sur la somme totale d'un document
On modélise la réduction sur un prooduit (resp un document) par 3 informations : 
* ``double reduction`` : indique le taux de réduction
* ``boolean reductionHT``: vrai si la réduction dois s'appliquer sur le prix HT de ce produit (resp de ce document), faux si elle doit s'appliquer sur le prix TTC du produit (resp du document).
* ``boolean reductionParPourcentage`` : vrai si la réducton est un pourcentage, faux si la réduction est un montant à déduire.

## Gestion des réductions
### Réductions invalides
* Si une réduction sur un produit est supérieur au prix du produit alors la création de la facture ne se fait pas.
* Si une réduction sur la totalité de la facture est supérieur au montant de la facture alors cette dernière n'est pas créée.
### Réduction sur la totalilté d'une facture avec des taux TVA différents
Appliquer une réduction sur la totalité d'une facture avec des taux TVA différents peut être compliqué, exemple :
| Produit                 | Prix HT        | TVA   | Prix TTC  |
| ----------------------- |:---------------| :-----|:----------|
| **1 x** Pizza 4-Fromages| 10.00 €        | 10%   | 11.00 €   | 
| **1 x** Tacos           | 05.00 €        | 10%   | 05.50 €   |
| **1 x** Coca Zero 1L    | 03.00 €        | 20%   | 03.60 €   |
| **Total**               | 18.00 €        |  --   | 20.10 €   |

Si on voudrait appliquer une réduction de 5€ sur la valeur totale HT de la facture, on ne pourrait pas déduire cette reduction directement du prix total HT, car on ne saurait plus comment calculer le prix total TTC du fait qu'on saurait pas quel taux de TVA appliquer.


#### Solution
Il faut grouper les produits par taux de TVA comme ceci:
| Groupes                 | Prix HT        | valeur TVA| Prix TTC  |
| ----------------------- |:---------------| :---------|:----------|
| TVA 10%                 | 15.00 €        | 01.50 €   | 16.50 €   | 
| TVA 20%                 | 03.00 €        | 00.60 €   | 03.60 €   |
| **Total**               | 18.00 €        |  --       | 20.10 €   |

De là on applique la réduction sur les groupes de TVA, Si le montant de la réduction dépasse la valeur du premier groupe, alors on applique ce qu'il reste de la réduction sur le prochain groupe et ainsi de suite, comme ceci:
| Groupes                 | Prix HT        | valeur TVA| Prix TTC  |
| ----------------------- |:---------------| :---------|:----------|
| Reduction Totale HT     | 05.00 €        | --        | --        |
| TVA 10%                 | 15.00 €        | --        | --        |
| ---Avec Réduction       | 10.00 €        | 01.00 €   | 11.00 €   |
| TVA 20%                 | 03.00 €        | 00.60 €   | 03.60 €   |
| ---Pas de Réduction     | --             | --        | --        |
| **Total**               | 18.00 €        |  --       | 14.60 €   |

#### Remarques:
Si on applique une réduction sur le montant HT (resp le montant TTC) sur une facture avec des produits ayants des taux TVA différents, alors on ne peut pas prédire le montant TTC (resp le montant HT) de la facture, car cela dépend des groupes (de produits, groupés par taux de TVA) sur les quels va être appliquée la réduction.

# New Features!

  - Import a HTML file and watch it magically convert to Markdown
  - Drag and drop images (requires your Dropbox account be linked)


You can also:
  - Import and save files from GitHub, Dropbox, Google Drive and One Drive
  - Drag and drop markdown and HTML files into Dillinger
  - Export documents as Markdown, HTML and PDF

Markdown is a lightweight markup language based on the formatting conventions that people naturally use in email.  As [John Gruber] writes on the [Markdown site][df1]

> The overriding design goal for Markdown's
> formatting syntax is to make it as readable
> as possible. The idea is that a
> Markdown-formatted document should be
> publishable as-is, as plain text, without
> looking like it's been marked up with tags
> or formatting instructions.

This text you see here is *actually* written in Markdown! To get a feel for Markdown's syntax, type some text into the left window and watch the results in the right.

### Tech

Dillinger uses a number of open source projects to work properly:

* [AngularJS] - HTML enhanced for web apps!
* [Ace Editor] - awesome web-based text editor
* [markdown-it] - Markdown parser done right. Fast and easy to extend.
* [Twitter Bootstrap] - great UI boilerplate for modern web apps
* [node.js] - evented I/O for the backend
* [Express] - fast node.js network app framework [@tjholowaychuk]
* [Gulp] - the streaming build system
* [Breakdance](http://breakdance.io) - HTML to Markdown converter
* [jQuery] - duh

And of course Dillinger itself is open source with a [public repository][dill]
 on GitHub.

### Installation

Dillinger requires [Node.js](https://nodejs.org/) v4+ to run.

Install the dependencies and devDependencies and start the server.

```sh
$ cd dillinger
$ npm install -d
$ node app
```

For production environments...

```sh
$ npm install --production
$ NODE_ENV=production node app
```

### Plugins

Dillinger is currently extended with the following plugins. Instructions on how to use them in your own application are linked below.

| Plugin | README |
| ------ | ------ |
| Dropbox | [plugins/dropbox/README.md][PlDb] |
| Github | [plugins/github/README.md][PlGh] |
| Google Drive | [plugins/googledrive/README.md][PlGd] |
| OneDrive | [plugins/onedrive/README.md][PlOd] |
| Medium | [plugins/medium/README.md][PlMe] |
| Google Analytics | [plugins/googleanalytics/README.md][PlGa] |


### Development

Want to contribute? Great!

Dillinger uses Gulp + Webpack for fast developing.
Make a change in your file and instantanously see your updates!

Open your favorite Terminal and run these commands.

First Tab:
```sh
$ node app
```

Second Tab:
```sh
$ gulp watch
```

(optional) Third:
```sh
$ karma test
```
#### Building for source
For production release:
```sh
$ gulp build --prod
```
Generating pre-built zip archives for distribution:
```sh
$ gulp build dist --prod
```
### Docker
Dillinger is very easy to install and deploy in a Docker container.

By default, the Docker will expose port 8080, so change this within the Dockerfile if necessary. When ready, simply use the Dockerfile to build the image.

```sh
cd dillinger
docker build -t joemccann/dillinger:${package.json.version}
```
This will create the dillinger image and pull in the necessary dependencies. Be sure to swap out `${package.json.version}` with the actual version of Dillinger.

Once done, run the Docker image and map the port to whatever you wish on your host. In this example, we simply map port 8000 of the host to port 8080 of the Docker (or whatever port was exposed in the Dockerfile):

```sh
docker run -d -p 8000:8080 --restart="always" <youruser>/dillinger:${package.json.version}
```

Verify the deployment by navigating to your server address in your preferred browser.

```sh
127.0.0.1:8000
```

#### Kubernetes + Google Cloud

See [KUBERNETES.md](https://github.com/joemccann/dillinger/blob/master/KUBERNETES.md)


### Todos

 - Write MORE Tests
 - Add Night Mode

License
----

MIT


**Free Software, Hell Yeah!**

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)


   [dill]: <https://github.com/joemccann/dillinger>
   [git-repo-url]: <https://github.com/joemccann/dillinger.git>
   [john gruber]: <http://daringfireball.net>
   [df1]: <http://daringfireball.net/projects/markdown/>
   [markdown-it]: <https://github.com/markdown-it/markdown-it>
   [Ace Editor]: <http://ace.ajax.org>
   [node.js]: <http://nodejs.org>
   [Twitter Bootstrap]: <http://twitter.github.com/bootstrap/>
   [jQuery]: <http://jquery.com>
   [@tjholowaychuk]: <http://twitter.com/tjholowaychuk>
   [express]: <http://expressjs.com>
   [AngularJS]: <http://angularjs.org>
   [Gulp]: <http://gulpjs.com>

   [PlDb]: <https://github.com/joemccann/dillinger/tree/master/plugins/dropbox/README.md>
   [PlGh]: <https://github.com/joemccann/dillinger/tree/master/plugins/github/README.md>
   [PlGd]: <https://github.com/joemccann/dillinger/tree/master/plugins/googledrive/README.md>
   [PlOd]: <https://github.com/joemccann/dillinger/tree/master/plugins/onedrive/README.md>
   [PlMe]: <https://github.com/joemccann/dillinger/tree/master/plugins/medium/README.md>
   [PlGa]: <https://github.com/RahulHP/dillinger/blob/master/plugins/googleanalytics/README.md>
