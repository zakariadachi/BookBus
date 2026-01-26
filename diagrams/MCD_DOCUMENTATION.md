# MCD - Modèle Conceptuel de Données BookBus

## 📊 Vue d'ensemble

Le MCD de BookBus comprend **8 entités** et **7 associations** selon la notation Merise.

---

## 🔷 ENTITÉS

### 1. UTILISATEUR

**Identifiant:** id_utilisateur

**Attributs:**

- nom (texte, obligatoire)
- email (texte, obligatoire, unique)
- mot_de_passe (texte, obligatoire)
- telephone (texte, obligatoire)
- role (texte, obligatoire) : 'client' ou 'admin'
- date_verification_email (date)
- jeton_souvenir (texte)
- date_creation (date)
- date_modification (date)

---

### 2. COMPAGNIE_BUS

**Identifiant:** id_compagnie

**Attributs:**

- nom (texte, obligatoire) : Ex: CTM, Supratours, SATAS
- logo (texte)
- telephone (texte, obligatoire)
- email (texte, obligatoire)
- adresse (texte)
- date_creation (date)
- date_modification (date)

---

### 3. BUS

**Identifiant:** id_bus

**Attributs:**

- numero_immatriculation (texte, obligatoire, unique)
- modele (texte, obligatoire) : Ex: Mercedes-Benz Tourismo
- nombre_sieges_total (entier, obligatoire)
- disposition_sieges (JSON)
- equipements (JSON) : WiFi, AC, WC, USB, etc.
- date_creation (date)
- date_modification (date)

---

### 4. TRAJET

**Identifiant:** id_trajet

**Attributs:**

- ville_depart (texte, obligatoire) : Ex: Casablanca
- ville_arrivee (texte, obligatoire) : Ex: Marrakech
- distance_km (decimal, obligatoire)
- duree_minutes (entier, obligatoire)
- date_creation (date)
- date_modification (date)

**Exemples:**

- Casablanca → Marrakech : 241 km, 180 min
- Casablanca → Fès : 298 km, 240 min
- Casablanca → Tanger : 338 km, 300 min

---

### 5. VOYAGE

**Identifiant:** id_voyage

**Attributs:**

- heure_depart (date/heure, obligatoire)
- heure_arrivee (date/heure, obligatoire)
- prix (decimal, obligatoire) : En dirhams (DH)
- statut (texte, obligatoire) : scheduled, in_progress, completed, cancelled
- sieges_disponibles (entier, obligatoire)
- date_creation (date)
- date_modification (date)

**Note:** Un voyage est une instance d'un trajet à une date/heure spécifique.

---

### 6. RESERVATION

**Identifiant:** id_reservation

**Attributs:**

- reference_reservation (texte, obligatoire, unique) : Format BB-YYYY-XXX
- nom_passager (texte, obligatoire)
- telephone_passager (texte, obligatoire)
- nombre_sieges (entier, obligatoire)
- prix_total (decimal, obligatoire)
- statut (texte, obligatoire) : pending, confirmed, cancelled, completed
- date_creation (date)
- date_modification (date)

---

### 7. PAIEMENT

**Identifiant:** id_paiement

**Attributs:**

- montant (decimal, obligatoire)
- methode_paiement (texte, obligatoire) : cash, card, mobile_money
- numero_transaction (texte)
- statut (texte, obligatoire) : pending, completed, failed, refunded
- date_paiement (date)
- date_creation (date)
- date_modification (date)

---

### 8. SIEGE

**Identifiant:** id_siege

**Attributs:**

- numero_siege (texte, obligatoire) : Ex: A12, B5, C8
- type_siege (texte, obligatoire) : standard, vip
- date_creation (date)
- date_modification (date)

---

## 🔗 ASSOCIATIONS

### 1. POSSEDER

**Entre:** COMPAGNIE_BUS et BUS

**Cardinalités:**

- Une COMPAGNIE_BUS possède 0 ou plusieurs BUS (0,N)
- Un BUS appartient à une seule COMPAGNIE_BUS (1,1)

**Signification:** Chaque bus est la propriété d'une compagnie de transport.

---

### 2. EFFECTUER

**Entre:** BUS et VOYAGE

**Cardinalités:**

- Un BUS effectue 0 ou plusieurs VOYAGE (0,N)
- Un VOYAGE est effectué par un seul BUS (1,1)

**Signification:** Chaque voyage est réalisé par un bus spécifique.

---

### 3. CONCERNER

**Entre:** TRAJET et VOYAGE

**Cardinalités:**

- Un TRAJET concerne 0 ou plusieurs VOYAGE (0,N)
- Un VOYAGE concerne un seul TRAJET (1,1)

**Signification:** Chaque voyage suit un trajet défini (ville départ → ville arrivée).

---

### 4. FAIRE

**Entre:** UTILISATEUR et RESERVATION

**Cardinalités:**

- Un UTILISATEUR fait 0 ou plusieurs RESERVATION (0,N)
- Une RESERVATION est faite par un seul UTILISATEUR (1,1)

**Signification:** Les utilisateurs peuvent effectuer plusieurs réservations.

---

### 5. RESERVER

**Entre:** VOYAGE et RESERVATION

**Cardinalités:**

- Un VOYAGE reçoit 0 ou plusieurs RESERVATION (0,N)
- Une RESERVATION concerne un seul VOYAGE (1,1)

**Signification:** Chaque réservation est liée à un voyage spécifique.

---

### 6. PAYER

**Entre:** RESERVATION et PAIEMENT

**Cardinalités:**

- Une RESERVATION nécessite un seul PAIEMENT (1,1)
- Un PAIEMENT concerne une seule RESERVATION (1,1)

**Signification:** Relation 1:1 - Chaque réservation a exactement un paiement.

---

### 7. OCCUPER

**Entre:** RESERVATION et SIEGE

**Cardinalités:**

- Une RESERVATION contient 1 ou plusieurs SIEGE (1,N)
- Un SIEGE appartient à une seule RESERVATION (1,1)

**Signification:** Une réservation peut contenir plusieurs sièges (réservation de groupe).

---

## 📐 Schéma Textuel

```
COMPAGNIE_BUS (1,1)----POSSEDER----(0,N) BUS
                                           |
                                           |
                                    EFFECTUER
                                           |
                                           |
                                        (0,N)
                                           |
TRAJET (1,1)----CONCERNER----(0,N) VOYAGE (1,1)----RESERVER----(0,N) RESERVATION
                                                                         |
                                                                         |
                                                                      PAYER
                                                                         |
                                                                         |
                                                                      (1,1)
                                                                         |
                                                                    PAIEMENT

UTILISATEUR (1,1)----FAIRE----(0,N) RESERVATION (1,1)----OCCUPER----(1,N) SIEGE
```

---

## 🎯 Règles de Gestion

1. **RG1:** Une compagnie de bus peut posséder plusieurs bus.
2. **RG2:** Un bus appartient à une seule compagnie.
3. **RG3:** Un bus peut effectuer plusieurs voyages (à des dates différentes).
4. **RG4:** Un voyage est effectué par un seul bus.
5. **RG5:** Un trajet peut avoir plusieurs voyages (instances à différentes dates/heures).
6. **RG6:** Un voyage suit un seul trajet.
7. **RG7:** Un utilisateur peut faire plusieurs réservations.
8. **RG8:** Une réservation est faite par un seul utilisateur.
9. **RG9:** Un voyage peut avoir plusieurs réservations.
10. **RG10:** Une réservation concerne un seul voyage.
11. **RG11:** Une réservation nécessite exactement un paiement.
12. **RG12:** Un paiement est lié à une seule réservation.
13. **RG13:** Une réservation contient au moins un siège (peut être plusieurs pour réservation de groupe).
14. **RG14:** Un siège est lié à une seule réservation.
15. **RG15:** Le nombre de sièges disponibles dans un voyage diminue à chaque réservation.
16. **RG16:** Une réservation ne peut pas dépasser le nombre de sièges disponibles.

---

## 📊 Contraintes d'Intégrité

### Contraintes d'entité:

- Tous les identifiants sont uniques et obligatoires
- Email utilisateur unique
- Numéro d'immatriculation bus unique
- Référence réservation unique

### Contraintes référentielles:

- Toutes les clés étrangères doivent référencer des entités existantes
- Suppression en cascade pour maintenir l'intégrité

### Contraintes métier:

- Prix > 0
- Nombre de sièges > 0
- Sièges disponibles ≥ 0
- Sièges disponibles ≤ Nombre total de sièges
- Heure d'arrivée > Heure de départ
- Distance > 0
- Durée > 0

---

## 🔄 Passage MCD → MLD (Modèle Logique de Données)

Le passage du MCD au MLD se fait selon les règles Merise:

### Relations 1:N

Les associations **POSSEDER**, **EFFECTUER**, **CONCERNER**, **FAIRE**, **RESERVER**, et **OCCUPER** deviennent des clés étrangères dans l'entité côté N.

**Exemple:**

- POSSEDER → Clé étrangère `id_compagnie` dans BUS
- EFFECTUER → Clé étrangère `id_bus` dans VOYAGE
- CONCERNER → Clé étrangère `id_trajet` dans VOYAGE

### Relation 1:1

L'association **PAYER** devient une clé étrangère dans PAIEMENT.

**Résultat:**

- PAYER → Clé étrangère `id_reservation` dans PAIEMENT

---

## 📝 Notes

- **Notation:** Merise française
- **Format:** PlantUML pour génération automatique
- **Conformité:** Respecte les exigences du brief (minimum 5 tables, relations claires)
- **Extensibilité:** Le modèle peut être étendu pour ajouter des fonctionnalités (avis, promotions, etc.)

---

**Créé pour:** Projet BookBus - Brief 1  
**Date:** 26/01/2026  
**Version:** 1.0
