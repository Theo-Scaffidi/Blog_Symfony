# Blog_Symfony

## Installation du projet
```
composer install  
```

```
npm i  
```


```
npm run build
```

## Modification du .env
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/blog"
```

## Ajouter la base de donnée
```
php bin/console doctrine:database:create
```


```
php bin/console make:migration
```


```
php bin/console doctrine:migrations:migrate
```

## Lancer le serveur
```
symfony server:start
```
