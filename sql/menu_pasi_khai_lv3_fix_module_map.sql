/*
  Finish menu_pasi_khai_lv3.sql
  The first script already created the menus and group permissions.
  This only fills bny_module_map_menu one row at a time
  (the table trigger cannot handle a multi-row INSERT).
*/

USE [bnyfoodproducts];
GO

DECLARE @check_id INT = 43;
DECLARE @folder_id INT = 200;
DECLARE @print_id INT;
DECLARE @delete_id INT = 203;
DECLARE @bny_module_id INT;
DECLARE @menu_id INT;

SELECT @print_id = menu_id
FROM dbo.menu
WHERE link LIKE N'%saletaxreport/saletaxreport_history%';

IF @print_id IS NULL SET @print_id = 56;

DECLARE map_cursor CURSOR LOCAL FAST_FORWARD FOR
SELECT mm.bny_module_id, m.menu_id
FROM dbo.bny_module_map_menu mm
CROSS JOIN (SELECT @folder_id AS menu_id UNION ALL SELECT @print_id UNION ALL SELECT @delete_id) m
WHERE mm.menu_id = @check_id
  AND NOT EXISTS (
    SELECT 1 FROM dbo.bny_module_map_menu x
    WHERE x.bny_module_id = mm.bny_module_id AND x.menu_id = m.menu_id
  );

OPEN map_cursor;
FETCH NEXT FROM map_cursor INTO @bny_module_id, @menu_id;

WHILE @@FETCH_STATUS = 0
BEGIN
  INSERT INTO dbo.bny_module_map_menu (bny_module_id, menu_id)
  VALUES (@bny_module_id, @menu_id);

  FETCH NEXT FROM map_cursor INTO @bny_module_id, @menu_id;
END

CLOSE map_cursor;
DEALLOCATE map_cursor;

PRINT N'Module map rows added one-by-one.';

SELECT menu_id, parent_menu, menu_name, link, sort, show_customer
FROM dbo.menu
WHERE menu_id IN (@folder_id, @check_id, @print_id, @delete_id)
   OR CAST(parent_menu AS VARCHAR(20)) IN (N'29', CAST(@folder_id AS VARCHAR(20)))
ORDER BY parent_menu, sort, menu_id;
GO
