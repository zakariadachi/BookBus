# Base de Données BookBus

## 📋 Description

Base de données complète pour le système de réservation de bus BookBus, inspirée de **marKoub.ma** avec des données réalistes du Maroc.

## 🗂️ Structure de la Base de Données

### Tables (8 au total)

1. **users** - Utilisateurs (clients et administrateurs)
2. **bus_companies** - Compagnies de bus marocaines
3. **buses** - Véhicules avec capacités et équipements
4. **routes** - Trajets entre villes marocaines
5. **trips** - Voyages programmés avec tarifs
6. **bookings** - Réservations des clients
7. **payments** - Paiements et transactions
8. **seats** - Sièges réservés

## 🚌 Compagnies Incluses

Les compagnies de bus marocaines les plus connues:

- **CTM** - Leader du transport au Maroc
- **Supratours** - Filiale de l'ONCF
- **SATAS** - Compagnie historique
- **Pullman du Sud** - Spécialisée Sud du Maroc
- **Ghazala** - Région Nord
- **Trans Ghazala** - Liaisons Fès
- **Nejme Chamal** - Tanger et environs
- **Stareo** - Marrakech et région

## 🗺️ Trajets Principaux

### Depuis Casablanca

- Casablanca → Rabat (87 km) - **45 DH**
- Casablanca → Marrakech (241 km) - **75-85 DH**
- Casablanca → Fès (298 km) - **95-100 DH**
- Casablanca → Tanger (338 km) - **110-120 DH**
- Casablanca → Agadir (508 km) - **140-150 DH**
- Casablanca → Essaouira (372 km) - **110 DH**

### Autres Trajets Populaires

- Marrakech → Agadir - **90 DH**
- Marrakech → Essaouira - **70 DH**
- Rabat → Fès - **85 DH**
- Rabat → Tanger - **95 DH**
- Fès → Tanger - **100 DH**

## 💾 Installation

### Option 1: Ligne de commande MySQL

```bash
# Se connecter à MySQL
mysql -u root -p

# Exécuter le script
source C:/Users/safiy/OneDrive/Desktop/BookBus/database/bookbus_database.sql
```

### Option 2: phpMyAdmin

1. Ouvrir phpMyAdmin
2. Cliquer sur "Importer"
3. Sélectionner le fichier `bookbus_database.sql`
4. Cliquer sur "Exécuter"

### Option 3: MySQL Workbench

1. Ouvrir MySQL Workbench
2. File → Run SQL Script
3. Sélectionner `bookbus_database.sql`
4. Exécuter

## 📊 Données Incluses

### Utilisateurs (10 au total)

- 2 Administrateurs
- 8 Clients

**Mot de passe par défaut pour tous:** `password`
(Hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`)

### Voyages

- **40+ voyages** programmés sur 7 jours
- Horaires variés (matin, après-midi, nuit)
- Tarifs réalistes basés sur la distance

### Réservations

- 8 réservations exemples
- Différents statuts (confirmé, en attente)
- Différentes méthodes de paiement

## 🔍 Vues Créées

### 1. available_trips_view

Affiche tous les voyages disponibles avec détails complets:

```sql
SELECT * FROM available_trips_view;
```

### 2. bookings_details_view

Affiche toutes les réservations avec informations complètes:

```sql
SELECT * FROM bookings_details_view;
```

## 📝 Requêtes Utiles

### Rechercher des voyages

```sql
-- Voyages Casablanca → Marrakech
SELECT * FROM available_trips_view
WHERE departure_city = 'Casablanca'
  AND arrival_city = 'Marrakech'
  AND DATE(departure_time) = '2026-01-27';
```

### Voir les réservations d'un utilisateur

```sql
SELECT * FROM bookings_details_view
WHERE user_email = 'mohammed.alami@gmail.com';
```

### Statistiques par compagnie

```sql
SELECT
    bc.name AS compagnie,
    COUNT(t.id) AS nombre_voyages,
    SUM(b.total_seats) AS capacite_totale
FROM bus_companies bc
JOIN buses b ON bc.id = b.bus_company_id
JOIN trips t ON b.id = t.bus_id
GROUP BY bc.name
ORDER BY nombre_voyages DESC;
```

### Revenus par jour

```sql
SELECT
    DATE(created_at) AS date,
    COUNT(*) AS nombre_reservations,
    SUM(total_price) AS revenus_total
FROM bookings
WHERE status = 'confirmed'
GROUP BY DATE(created_at);
```

## 🔐 Comptes de Test

### Administrateur

- **Email:** admin@bookbus.ma
- **Mot de passe:** password
- **Téléphone:** 0612345678

### Client

- **Email:** mohammed.alami@gmail.com
- **Mot de passe:** password
- **Téléphone:** 0661234567

## 🛠️ Intégration avec Laravel

### Configuration .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookbus
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### Migrations Laravel

Les migrations Laravel seront créées dans le prochain brief. Cette base de données SQL peut être utilisée pour:

- Développement et tests rapides
- Comprendre la structure
- Générer les migrations Laravel

## ✅ Validation

Après l'installation, vérifiez:

```sql
-- Vérifier les tables
SHOW TABLES;

-- Compter les enregistrements
SELECT 'Users' AS table_name, COUNT(*) AS count FROM users
UNION ALL
SELECT 'Companies', COUNT(*) FROM bus_companies
UNION ALL
SELECT 'Buses', COUNT(*) FROM buses
UNION ALL
SELECT 'Routes', COUNT(*) FROM routes
UNION ALL
SELECT 'Trips', COUNT(*) FROM trips
UNION ALL
SELECT 'Bookings', COUNT(*) FROM bookings;
```

**Résultat attendu:**

- Users: 10
- Companies: 8
- Buses: 15
- Routes: 29
- Trips: 40+
- Bookings: 8

## 📌 Notes Importantes

1. **Dates dynamiques:** Les voyages utilisent `DATE_ADD(NOW(), INTERVAL X DAY)` pour avoir des dates futures
2. **Sièges disponibles:** Mis à jour automatiquement après chaque réservation
3. **Index:** Optimisés pour les recherches fréquentes
4. **Contraintes:** Foreign keys pour l'intégrité référentielle
5. **Encodage:** UTF8MB4 pour supporter les caractères arabes et émojis

## 🚀 Prochaines Étapes

1. Installer Laravel
2. Configurer la connexion à cette base de données
3. Créer les Models Eloquent correspondants
4. Créer les migrations Laravel (optionnel, la BDD existe déjà)
5. Créer les Seeders Laravel pour régénérer les données

---

**Créé pour le projet BookBus - Brief 1**
_Base de données inspirée de marKoub.ma_
