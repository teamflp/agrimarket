# PROJET AGRI MARKET
## Description
Ce projet est une application web qui permet de mettre en relation des agriculteurs et des acheteurs de produits agricoles. Les agriculteurs peuvent publier des annonces pour vendre leurs produits et les acheteurs peuvent consulter les annonces et contacter les agriculteurs pour acheter les produits.

## Installation de API Platform
```bash
composer require api
```
Cette commande installe API Platform et ses dépendances.
## Activer la configuration et vérifier le `routes.yaml` :

API Platform ajoute une route qui intercepte tout le prefix /api. Par défaut, cette config est dans `config/routes/api_platform.yaml` :
```bash
api_platform:
  resource: .
  type: api_platform
  prefix: /api
```
## Annoter les entités pour en faire des ressources d’API
2.1. Entités : annotations ou attributs PHP 8+

Exemple simple d’entité Product :
```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource] // <- rend cette classe accessible via l'API
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    // ... Getters & Setters ...
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $url): self
    {
        $this->imageUrl = $url;
        return $this;
    }
}
```
- Le fait de mettre #[ApiResource] (ou @ApiResource si on utilise encore les annotations) indique à API Platform que cette entité doit être exposée en tant que ressource d’API.
- Par défaut, vous aurez les endpoints GET/POST/PUT/DELETE etc. :
  - GET /api/products → Liste paginée
  - GET /api/products/{id} → Détails d’un produit
  - POST /api/products → Créer un produit, etc.

## Fonctionnalités: APIs REST

- Category API
  - GET /categories: Récupérer la liste des catégories
  - POST /categories: Ajouter une nouvelle catégorie
  - GET /categories/{id}: Récupérer une catégorie par son id
  - PUT /categories/{id}: Modifier une catégorie
  - DELETE /categories/{id}: Supprimer une catégorie

- Product API
  - GET /products: Récupérer la liste des produits
  - POST /products: Ajouter un nouveau produit
  - GET /products/{id}: Récupérer un produit par son id
  - PUT /products/{id}: Modifier un produit
  - DELETE /products/{id}: Supprimer un produit

- User API
- GET /users: Récupérer la liste des utilisateurs
- POST /users: Ajouter un nouvel utilisateur
- GET /users/{id}: Récupérer un utilisateur par son id
- PUT /users/{id}: Modifier un utilisateur

Installation de EasyAdmin
```bash
composer require easycorp/easyadmin-bundle:^4
```

## Configuration de EasyAdmin
```bash
php bin/console make:admin:dashboard
```

## Créer un CRUD pour l’entité Product
```bash
php bin/console make:admin:crud
```

