git clone git@github.com:cadot-eu/base.git app1 //app1 répertoire de destination
cd app1
cat README.md //pour voir le mode d'emploi
runsite
sudo chown www-data: public -R                                                                                    
    git submodule init
    git submodule update
    dbash                                                                                                             
    composer install && yarn install //installation des dépendances                                                                                  
    sc d:s:c     //création de la bd (elle est configurée dans le .env d'office sqlite à ouvrir avec sqlitebrowser)                                                                                                     
    sc d:f:l -n  // création des fixtures ( définis dans AdminFixture)   le -n permet de pas demander de confirmation                                                                                                      
    yarn watch 
    exit
    sudo chown www-data: var -R

visiter le site par localhost:...
l'admin par localhost:.../admin m@cadot.eu **

créer une entité par exemple pour un menu
sc m:e plat
nom puis 3 fois la touche entréé
prix integer et 1 fois entrée
description text et 1 fois entrée
une fois entrée
sc d:s:u --force on met à jour la base de donnée car on a créé une nouvelle entitée

sc crud:generate plat --force //crudmick va créer la partie admin pour gérer les plats

on peut accéder par admin/plat
on voit que description est juste un texte, on veut un éditeur plus sympa et on veut que prix soit un champ prix et que mis à jour soit caché

on ouvre src/Entity/Plat.php
//on a les codes à mettre pour crudmick dans src/Command/base/README.md

pour le prix on ajoute la partie /** ... */
    #[ORM\Column(type: 'integer')]
    /**
     * money
     */
    private $prix;

pour la description on peut mettre 
- simple
- simplelanguage
- normal
- full

exemple:
    #[ORM\Column(type: 'text')]
    /**
     * normal
     * OPT:{"required":false} 
     */
    private $description;

pour cacher on met dans id
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    /**
     * tpl:no_updated
     */
    private $id;

ensuite on regénère les templates, controller, form pour plat
sc crud:generate plat --force

tu peux retourner sur ta page /admin/plat
et créer, modifier tes plats.

Ensuite dans ton homeController
tu peux récupérer tes plats en modificant la méthode index
   #[Route('/', name: 'home_index')]
    public function index(Request $request, FixtureHelper $fixtureHelper, PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findAll();
        /* ------------------------------- simulateurs ------------------------------ */
        return $this->render('home/index.html.twig', ['plats' => $plats, 'testhelper' => $fixtureHelper::generate('phrase')]);
    }
puis pour les voir tu vas dans ton twig templates/home/index.html.twig
{{TBdd(plats)}} ou 	{% for plat in plats %}
		{{plat.id~':'~plat.nom}}
	{% endfor %}
