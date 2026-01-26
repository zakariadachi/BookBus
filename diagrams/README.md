# Diagrammes UML BookBus

Ce dossier contient tous les diagrammes UML du projet BookBus au format PlantUML.

## Fichiers disponibles

### 1. Diagramme de Classes (`class_diagram.puml`)

Représente les 8 classes principales du système avec leurs attributs, méthodes et relations.

**Classes incluses:**

- User
- BusCompany
- Route
- Bus
- Trip
- Booking
- Payment
- Seat

### 2. Diagramme de Cas d'Utilisation (`usecase_diagram.puml`)

Montre les interactions entre les acteurs (Client, Administrateur) et le système.

**Acteurs:**

- Client (8 cas d'utilisation)
- Administrateur (7 cas d'utilisation)

### 3. Diagramme ERD (`erd_diagram.puml`)

Schéma de base de données avec les 8 tables et leurs relations.

**Tables:**

- users
- bus_companies
- buses
- routes
- trips
- bookings
- payments
- seats

## Comment visualiser les diagrammes

### Option 1: PlantUML Online

1. Allez sur http://www.plantuml.com/plantuml/uml/
2. Copiez le contenu d'un fichier `.puml`
3. Cliquez sur "Submit" pour générer le diagramme

### Option 2: Extension VS Code

1. Installez l'extension "PlantUML" dans VS Code
2. Ouvrez un fichier `.puml`
3. Appuyez sur `Alt+D` pour prévisualiser

### Option 3: Ligne de commande

```bash
# Installer PlantUML
# Nécessite Java

# Générer une image PNG
java -jar plantuml.jar class_diagram.puml

# Générer tous les diagrammes
java -jar plantuml.jar *.puml
```

## Intégration dans DOCUMENTATION.md

Ces diagrammes doivent être inclus dans votre fichier `DOCUMENTATION.md` sous forme d'images générées ou de code PlantUML.

**Exemple d'inclusion:**

```markdown
### Diagramme de Classes

![Diagramme de Classes](diagrams/class_diagram.png)
```

## Notes

- Les diagrammes sont conformes aux exigences du brief:
  - ✅ Minimum 5 tables (8 tables créées)
  - ✅ 2 acteurs avec 3-4 cas d'utilisation chacun
  - ✅ 5 classes minimum avec attributs/méthodes (8 classes créées)
- Format PlantUML pour faciliter les modifications
- Relations clairement définies avec cardinalités
