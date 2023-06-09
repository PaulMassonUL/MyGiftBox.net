# MyGiftBox.net

- Groupe : MASSON Paul, SEILER Mathis

## Installation

Suivez ces étapes pour installer MyGiftBox :

1. Cloner le projet
   - Naviguer dans le dossier de son choix et effectuer la commande `git clone https://github.com/PaulMassonUL/MyGiftBox.net.git`.

2. Installer les dépendances 
   - Naviguer dans le dossier `src/` de l'appli et de l'api pour y effectuer
   la commande `composer install` (Où se situe les fichiers `composer.json`).

3. Configuration:
   - Copier le fichier `.env.dist` situé à la racine du projet et le renommer en `.env`.
   - Ouvrir le fichier `.env` et mettre à jour les paramètres de configuration
   des identifiants de connexion à la base de données.
   - Naviquer dans le dossier `src/conf/` de l'appli et de l'api.
   - Copier les fichiers de configuration `.dist` et les renommer en retirant le `.dist`.
   - Ouvrir les fichiers de configuration et compléter les paramètres des identifiants
   de connexion à la base de données précédemment saisis dans le fichier `.env`.

4. Lancer les services docker :
   - Naviguer à la racine du projet et effectuer la commande `docker compose up -d`.
   - L'application est désormais accessible à l'adresse `http://localhost:8000`.
   - L'api est accessible à l'adresse `http://localhost:8001/api`.

## Fonctionnalitées
### Catalogue des prestations :
- [x] Afficher la liste des prestations (Paul)
- [x] Afficher le détail d'une prestation (Paul)
- [x] Liste de prestations par catégories (Paul)
- [x] Liste de catégories (Mathis)
- [x] [ETENDU] Tri par prix (Paul)
### Création du coffret :
- [x] Création d'un coffret vide (Paul)
- [x] Ajout de prestations dans le coffret (Paul)
- [x] Créer un coffret prérempli à partir d'une box (Paul)
- [ ] [ETENDU] Créer un coffret prérempli à partir d'une box affichée
### Inscription, authentification :
- [x] Signin (Paul)
- [x] Register (Paul)
- [x] Accéder à ses coffrets (Paul)
### URL d'accès :
- [x] Génération de l'URL d'accès (Paul)
- [x] Accès au coffret (Paul)
### API :
- [x] Api : liste des prestations (Mathis)
- [x] Api : liste des catégories (Paul)
- [X] Api : liste des prestation d'une catégorie (Mathis)
- [x] Api : accès à un coffret (Paul)
### Gestion / modification du coffret :
- [x] Affichage d'un coffret (Mathis)
- [x] Validation d'un coffret (Mathis)
- [x] Paiement d'un coffret (Mathis)
- [x] Modification d'un coffret : suppression de prestations (Mathis)
- [x] [ETENDU] Modification d'un coffret : modification des quantités (Mathis)