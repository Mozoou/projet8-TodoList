ToDoList
========

Projet n°8 du parcours Openclassrooms "Développeur d'application PHP/Symfony" :
Améliorer une application existant de ToDo & Co

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Pré-requis
- PHP 8.2 ou supérieur
- Symfony 6.2
- Symfony CLI
- Composer
- Serveur web (Apache, MySQL, PHP)
- Visual Studio Code, PHPStorm, SublimText, ...

## Installation
Pour installer le projet, suivez les étapes suivantes :

1. Cloner le repository
```bash
  git clone https://github.com/Mozoou/P8_ToDoList.git
```
2. Accèder au répertoire du projet :
```bash
  cd P8_ToDoList
```
3. Installer les dépendances :
```bash
  composer install
```

4. Configurer la base de données dans le fichier `.env.local` ou `.env` à la racine du projet :
```
DATABASE_URL="mysql://db_user:db_password@db_host/db_name?serverVersion=8&charset=utf8mb4"
```
6. Créer la base de données :
```bash
  symfony console doctrine:database:create
```
6. Créer les tables de la base de données :
```bash
  symfony console doctrine:migrations:migrate
```
7. Ajouter les données fictives:
```bash
  symfony console doctrine:fixtures:load
```

8. Pour lancer le projet :
```bash
  symfony serve -d
```
## Tests unitaires et fonctionnels

1. Configurer la base de données test dans le fichier `.env.test` à la racine du projet si précédemment vous avez créé un fichier `.env.local`
```
DATABASE_URL="mysql://db_user:db_password@db_host/db_name?serverVersion=8&charset=utf8mb4"
```

2. Créer la base de données :
```bash
  symfony console doctrine:database:create --env=test
```

3. Créer les tables de la base de données :
```bash
  symfony console doctrine:migrations:migrate --env=test
```

4. Ajouter les données fictives:
```bash
  symfony console doctrine:fixtures:load --env=test
```

5. Pour lancer les tests :
```bash
  vendor/bin/phpunit
```

Le rapport de couverture de code se situe dans le dossier ***public/coverage***

## Utilisation
Pour tester le site, vous pouvez vous connecter avec les comptes utilisateur suivants :


```
ROLE_USER :
identifiant: user@example.com
mot de passe: user

ROLE_ADMIN :
identifiant: admin@example.com
mot de passe: admin
```

## Documentations

Un rapport d'audit de performance ainsi que la documentation technique sur le système d'authentification se situe dans le dossier ***docs***
