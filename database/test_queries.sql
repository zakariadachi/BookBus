-- ============================================
-- REQUÊTES DE TEST - BookBus
-- Tester les tarifs et les voyages
-- ============================================

-- ============================================
-- 1. RECHERCHER DES VOYAGES PAR TRAJET
-- ============================================

-- Exemple 1: Tous les voyages Casablanca → Marrakech
SELECT 
    t.id AS voyage_id,
    r.departure_city AS ville_depart,
    r.arrival_city AS ville_arrivee,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS heure_depart,
    DATE_FORMAT(t.arrival_time, '%d/%m/%Y %H:%i') AS heure_arrivee,
    t.price AS tarif_dh,
    t.available_seats AS sieges_disponibles,
    bc.name AS compagnie,
    b.model AS modele_bus,
    t.status AS statut
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE r.departure_city = 'Casablanca' 
  AND r.arrival_city = 'Marrakech'
  AND t.status = 'scheduled'
ORDER BY t.departure_time;

-- ============================================
-- 2. RECHERCHER VOYAGES PAR DATE
-- ============================================

-- Exemple 2: Voyages disponibles demain
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    DATE_FORMAT(t.departure_time, '%H:%i') AS heure,
    t.price AS tarif,
    t.available_seats AS sieges,
    bc.name AS compagnie
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE DATE(t.departure_time) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
  AND t.status = 'scheduled'
  AND t.available_seats > 0
ORDER BY r.departure_city, t.departure_time;

-- ============================================
-- 3. COMPARER LES TARIFS PAR TRAJET
-- ============================================

-- Exemple 3: Comparaison des tarifs pour un même trajet
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    MIN(t.price) AS tarif_min,
    MAX(t.price) AS tarif_max,
    AVG(t.price) AS tarif_moyen,
    COUNT(t.id) AS nombre_voyages
FROM trips t
JOIN routes r ON t.route_id = r.id
WHERE r.departure_city = 'Casablanca' 
  AND r.arrival_city = 'Fès'
  AND t.status = 'scheduled'
GROUP BY r.departure_city, r.arrival_city;

-- ============================================
-- 4. VOYAGES LES MOINS CHERS
-- ============================================

-- Exemple 4: Top 10 des voyages les moins chers
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    t.price AS tarif,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS depart_le,
    t.available_seats AS sieges,
    bc.name AS compagnie
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE t.status = 'scheduled'
  AND t.available_seats > 0
ORDER BY t.price ASC
LIMIT 10;

-- ============================================
-- 5. VOYAGES PAR COMPAGNIE
-- ============================================

-- Exemple 5: Tous les voyages CTM disponibles
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS heure_depart,
    t.price AS tarif,
    t.available_seats AS sieges,
    b.model AS bus
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE bc.name = 'CTM'
  AND t.status = 'scheduled'
  AND t.available_seats > 0
ORDER BY t.departure_time;

-- ============================================
-- 6. RECHERCHE AVANCÉE AVEC FILTRES
-- ============================================

-- Exemple 6: Recherche avec critères multiples
-- Casablanca → Marrakech, prix max 85 DH, au moins 5 sièges
SELECT 
    DATE_FORMAT(t.departure_time, '%d/%m/%Y') AS date,
    DATE_FORMAT(t.departure_time, '%H:%i') AS heure,
    t.price AS tarif,
    t.available_seats AS sieges,
    bc.name AS compagnie,
    b.model AS bus,
    CONCAT(FLOOR(r.duration_minutes / 60), 'h', 
           LPAD(r.duration_minutes % 60, 2, '0')) AS duree
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE r.departure_city = 'Casablanca'
  AND r.arrival_city = 'Marrakech'
  AND t.price <= 85.00
  AND t.available_seats >= 5
  AND t.status = 'scheduled'
ORDER BY t.price ASC, t.departure_time ASC;

-- ============================================
-- 7. STATISTIQUES DES TARIFS PAR DISTANCE
-- ============================================

-- Exemple 7: Analyse tarif/kilomètre
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    r.distance_km AS distance,
    AVG(t.price) AS tarif_moyen,
    ROUND(AVG(t.price) / r.distance_km, 2) AS prix_par_km,
    COUNT(t.id) AS nb_voyages
FROM trips t
JOIN routes r ON t.route_id = r.id
WHERE t.status = 'scheduled'
GROUP BY r.id, r.departure_city, r.arrival_city, r.distance_km
ORDER BY r.distance_km DESC;

-- ============================================
-- 8. VOYAGES DE NUIT (après 20h)
-- ============================================

-- Exemple 8: Voyages de nuit disponibles
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS depart_le,
    DATE_FORMAT(t.arrival_time, '%d/%m/%Y %H:%i') AS arrivee_le,
    t.price AS tarif,
    t.available_seats AS sieges,
    bc.name AS compagnie
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE HOUR(t.departure_time) >= 20
  AND t.status = 'scheduled'
  AND t.available_seats > 0
ORDER BY t.departure_time;

-- ============================================
-- 9. DÉTAILS COMPLETS D'UN VOYAGE SPÉCIFIQUE
-- ============================================

-- Exemple 9: Détails complets du voyage ID 1
SELECT 
    t.id AS voyage_id,
    r.departure_city AS ville_depart,
    r.arrival_city AS ville_arrivee,
    r.distance_km AS distance,
    CONCAT(FLOOR(r.duration_minutes / 60), 'h', 
           LPAD(r.duration_minutes % 60, 2, '0')) AS duree,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y à %H:%i') AS depart,
    DATE_FORMAT(t.arrival_time, '%d/%m/%Y à %H:%i') AS arrivee,
    t.price AS tarif,
    t.available_seats AS sieges_disponibles,
    b.total_seats AS capacite_totale,
    ROUND((t.available_seats / b.total_seats) * 100, 0) AS pourcentage_dispo,
    bc.name AS compagnie,
    bc.phone AS tel_compagnie,
    b.registration_number AS immatriculation,
    b.model AS modele,
    b.amenities AS equipements,
    t.status AS statut
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE t.id = 1;

-- ============================================
-- 10. MEILLEURS TARIFS PAR DESTINATION
-- ============================================

-- Exemple 10: Meilleur tarif pour chaque destination depuis Casablanca
SELECT 
    r.arrival_city AS destination,
    MIN(t.price) AS meilleur_tarif,
    r.distance_km AS distance,
    COUNT(t.id) AS nb_voyages_disponibles
FROM trips t
JOIN routes r ON t.route_id = r.id
WHERE r.departure_city = 'Casablanca'
  AND t.status = 'scheduled'
  AND t.available_seats > 0
GROUP BY r.arrival_city, r.distance_km
ORDER BY MIN(t.price) ASC;

-- ============================================
-- 11. VOYAGES AVEC ÉQUIPEMENTS SPÉCIFIQUES
-- ============================================

-- Exemple 11: Voyages avec WiFi et WC
SELECT 
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS heure_depart,
    t.price AS tarif,
    t.available_seats AS sieges,
    bc.name AS compagnie,
    b.amenities AS equipements
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE JSON_CONTAINS(b.amenities, '"WiFi"')
  AND JSON_CONTAINS(b.amenities, '"WC"')
  AND t.status = 'scheduled'
  AND t.available_seats > 0
ORDER BY t.price ASC;

-- ============================================
-- 12. TAUX DE REMPLISSAGE PAR VOYAGE
-- ============================================

-- Exemple 12: Voyages avec leur taux de réservation
SELECT 
    t.id AS voyage_id,
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    DATE_FORMAT(t.departure_time, '%d/%m/%Y %H:%i') AS date_heure,
    b.total_seats AS capacite,
    t.available_seats AS sieges_libres,
    (b.total_seats - t.available_seats) AS sieges_reserves,
    ROUND(((b.total_seats - t.available_seats) / b.total_seats) * 100, 1) AS taux_remplissage_pct,
    t.price AS tarif,
    bc.name AS compagnie
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE t.status = 'scheduled'
ORDER BY taux_remplissage_pct DESC;

-- ============================================
-- 13. REVENUS POTENTIELS PAR VOYAGE
-- ============================================

-- Exemple 13: Calcul des revenus par voyage
SELECT 
    t.id AS voyage_id,
    r.departure_city AS depart,
    r.arrival_city AS arrivee,
    bc.name AS compagnie,
    t.price AS tarif_unitaire,
    b.total_seats AS capacite,
    (b.total_seats - t.available_seats) AS sieges_vendus,
    t.price * (b.total_seats - t.available_seats) AS revenus_actuels,
    t.price * b.total_seats AS revenus_max_possible,
    ROUND(((b.total_seats - t.available_seats) / b.total_seats) * 100, 1) AS taux_vente_pct
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE t.status = 'scheduled'
ORDER BY revenus_actuels DESC;

-- ============================================
-- 14. UTILISATION DE LA VUE available_trips_view
-- ============================================

-- Exemple 14: Utiliser la vue prédéfinie
SELECT 
    departure_city AS depart,
    arrival_city AS arrivee,
    DATE_FORMAT(departure_time, '%d/%m %H:%i') AS depart_le,
    price AS tarif,
    available_seats AS sieges,
    company_name AS compagnie
FROM available_trips_view
WHERE departure_city = 'Casablanca'
  AND available_seats >= 10
ORDER BY price ASC;

-- ============================================
-- FIN DES REQUÊTES DE TEST
-- ============================================

-- Note: Remplacez les valeurs selon vos besoins:
-- - Villes: 'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', etc.
-- - Compagnies: 'CTM', 'Supratours', 'SATAS', 'Pullman du Sud', etc.
-- - Dates: Utilisez DATE_ADD(CURDATE(), INTERVAL X DAY) pour des dates futures
