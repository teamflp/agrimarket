# PROJET AGRI MARKET
## Description
Ce projet est une application web qui permet de mettre en relation des agriculteurs et des acheteurs de produits agricoles. Les agriculteurs peuvent publier des annonces pour vendre leurs produits et les acheteurs peuvent consulter les annonces et contacter les agriculteurs pour acheter les produits.

## Fonctionnalités: APIs REST

- Category API
  - GET /categories: Récupérer la liste des catégories
  - POST /categories: Ajouter une nouvelle catégorie
  - GET /categories/{id}: Récupérer une catégorie par son id
  - PUT /categories/{id}: Modifier une catégorie
  - DELETE /categories/{id}: Supprimer une catégorie

Utilisation: de l'API Category
- GET /categories
- POST /categories
- GET /categories/{id}
- PUT /categories/{id}
Exemple d'utilisation de l'API Category:
```bash
curl -X GET http://localhost:8080/categories
```