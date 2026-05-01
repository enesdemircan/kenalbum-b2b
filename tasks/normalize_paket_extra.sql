-- Önce mevcut sayıları göster
SELECT customization_category_id AS cat, COUNT(*) AS before_count
FROM customization_pivot_params
WHERE product_id = 7 AND customization_category_id IN (5, 9)
GROUP BY customization_category_id;

-- Paket: aynı param'a sahip duplicate pivot'lari sil (en kucuk id kalsin)
DELETE cpp1 FROM customization_pivot_params cpp1
INNER JOIN customization_pivot_params cpp2 ON
    cpp1.product_id = cpp2.product_id
AND cpp1.params_id = cpp2.params_id
AND cpp1.customization_category_id = cpp2.customization_category_id
AND cpp1.id > cpp2.id
WHERE cpp1.product_id = 7 AND cpp1.customization_category_id = 5;

-- Kalan Paket pivotlari top-level yap
UPDATE customization_pivot_params SET customization_params_ust_id = 0
WHERE product_id = 7 AND customization_category_id = 5;

-- Extra Urun: ayni normalizasyon
DELETE cpp1 FROM customization_pivot_params cpp1
INNER JOIN customization_pivot_params cpp2 ON
    cpp1.product_id = cpp2.product_id
AND cpp1.params_id = cpp2.params_id
AND cpp1.customization_category_id = cpp2.customization_category_id
AND cpp1.id > cpp2.id
WHERE cpp1.product_id = 7 AND cpp1.customization_category_id = 9;

UPDATE customization_pivot_params SET customization_params_ust_id = 0
WHERE product_id = 7 AND customization_category_id = 9;

-- Sonuc
SELECT customization_category_id AS cat, COUNT(*) AS after_count,
       SUM(CASE WHEN customization_params_ust_id = 0 THEN 1 ELSE 0 END) AS top_level_count
FROM customization_pivot_params
WHERE product_id = 7 AND customization_category_id IN (5, 9)
GROUP BY customization_category_id;
