-- Nest รายงานใบลดหนี้ (menu_id 45) under ใบลดหนี้ (menu_id 44).
-- Same pattern as ภาษีขาย children under menu_id 200.

UPDATE dbo.menu
SET parent_menu = '44',
    sort = 1
WHERE menu_id = 45
  AND parent_menu = '29';
