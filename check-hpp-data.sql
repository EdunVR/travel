-- SQL Script untuk Cek Data HPP
-- Jalankan di phpMyAdmin atau MySQL client

-- 1. Cek semua travel packages dan capacity-nya
SELECT 
    id,
    package_code,
    package_name,
    package_type,
    capacity,
    price,
    hpp,
    profit_margin,
    status
FROM travel_packages
ORDER BY id DESC
LIMIT 10;

-- 2. Cek semua HPP calculations
SELECT 
    id,
    id_travel_package,
    flight_cost,
    hotel_cost,
    transportation_cost,
    meal_cost,
    visa_cost,
    guide_cost,
    insurance_cost,
    operational_overhead,
    contingency,
    total_hpp,
    is_locked,
    locked_at
FROM hpp_calculations
ORDER BY id DESC
LIMIT 10;

-- 3. Join packages dengan HPP calculations
SELECT 
    p.id as package_id,
    p.package_code,
    p.package_name,
    p.capacity,
    p.price,
    p.hpp as package_hpp,
    h.flight_cost,
    h.hotel_cost,
    h.transportation_cost,
    h.meal_cost,
    h.visa_cost,
    h.guide_cost,
    h.insurance_cost,
    h.operational_overhead,
    h.contingency,
    h.total_hpp as calculated_hpp,
    h.is_locked,
    -- Perhitungan manual untuk verifikasi
    (h.flight_cost + h.hotel_cost + h.transportation_cost + h.meal_cost + 
     h.visa_cost + h.guide_cost + h.insurance_cost + h.operational_overhead + 
     h.contingency) * p.capacity as manual_total_hpp
FROM travel_packages p
LEFT JOIN hpp_calculations h ON p.id = h.id_travel_package
ORDER BY p.id DESC
LIMIT 10;

-- 4. Cek packages yang TIDAK punya HPP calculation
SELECT 
    id,
    package_code,
    package_name,
    capacity,
    price,
    status
FROM travel_packages
WHERE id NOT IN (SELECT id_travel_package FROM hpp_calculations)
ORDER BY id DESC;

-- 5. Cek packages dengan capacity = 0 atau NULL
SELECT 
    id,
    package_code,
    package_name,
    capacity,
    price,
    status
FROM travel_packages
WHERE capacity IS NULL OR capacity = 0
ORDER BY id DESC;

-- 6. Detail untuk package tertentu (ganti ID sesuai kebutuhan)
-- Uncomment dan ganti ID di bawah ini:
/*
SELECT 
    'Package Info' as section,
    p.id,
    p.package_code,
    p.package_name,
    p.capacity,
    p.price,
    p.hpp,
    p.profit_margin
FROM travel_packages p
WHERE p.id = 1  -- GANTI DENGAN ID PACKAGE YANG BERMASALAH
UNION ALL
SELECT 
    'HPP Calculation' as section,
    h.id,
    CONCAT('Flight: ', h.flight_cost) as info1,
    CONCAT('Hotel: ', h.hotel_cost) as info2,
    CONCAT('Transport: ', h.transportation_cost) as info3,
    CONCAT('Meal: ', h.meal_cost) as info4,
    CONCAT('Total: ', h.total_hpp) as info5,
    IF(h.is_locked, 'LOCKED', 'UNLOCKED') as lock_status
FROM hpp_calculations h
WHERE h.id_travel_package = 1;  -- GANTI DENGAN ID PACKAGE YANG BERMASALAH
*/

-- 7. Insert sample HPP data untuk testing (jika belum ada)
-- Uncomment untuk insert data test:
/*
INSERT INTO hpp_calculations (
    id_travel_package,
    flight_cost,
    hotel_cost,
    transportation_cost,
    meal_cost,
    visa_cost,
    guide_cost,
    insurance_cost,
    operational_overhead,
    contingency,
    total_hpp,
    is_locked,
    created_at,
    updated_at
) VALUES (
    1,  -- GANTI dengan ID package yang ada
    70000,   -- flight_cost per orang
    50000,   -- hotel_cost per orang
    30000,   -- transportation_cost per orang
    40000,   -- meal_cost per orang
    25000,   -- visa_cost per orang
    20000,   -- guide_cost per orang
    15000,   -- insurance_cost per orang
    10000,   -- operational_overhead per orang
    5000,    -- contingency per orang
    1080000, -- total_hpp (untuk capacity 4: 265000 * 4)
    0,       -- is_locked
    NOW(),
    NOW()
);
*/

-- 8. Update capacity package jika NULL atau 0
-- Uncomment untuk update:
/*
UPDATE travel_packages 
SET capacity = 4  -- GANTI dengan capacity yang diinginkan
WHERE id = 1;     -- GANTI dengan ID package yang bermasalah
*/
