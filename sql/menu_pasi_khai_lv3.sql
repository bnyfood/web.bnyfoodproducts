/*
  Accounting menu: one folder ภาษีขาย with a 3rd tier.

  After this script:
    บัญชี
      ใบกำกับภาษี          (unchanged)
      ภาษีขาย               (new folder, menu_id 200)
        กระทบยอดขาย        (was รายงานภาษีขาย, menu_id 43)
        รายงานภาษีขาย      (print / history, menu_id 56 if it already exists)
        ลบข้อมูล API        (admin only, menu_id 203)

  PHP constants already match these IDs.
  Menu cache is 1 hour — log out/in after a cache clear, or wait.
*/

USE [bnyfoodproducts];
GO

DECLARE @account_id VARCHAR(20) = '29';
DECLARE @check_id INT = 43;
DECLARE @folder_id INT = 200;
DECLARE @print_id INT;
DECLARE @delete_id INT = 203;
DECLARE @check_sort INT;
DECLARE @check_icon NVARCHAR(100);
DECLARE @check_show INT;

SELECT
  @check_sort = sort,
  @check_icon = icon,
  @check_show = show_customer
FROM dbo.menu
WHERE menu_id = @check_id;

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @check_id)
BEGIN
  RAISERROR(N'menu_id 43 (รายงานภาษีขาย / Check) was not found.', 16, 1);
END
ELSE
BEGIN

SELECT @print_id = menu_id
FROM dbo.menu
WHERE link LIKE N'%saletaxreport/saletaxreport_history%';

IF @print_id IS NULL SET @print_id = 56;

IF EXISTS (
  SELECT 1 FROM dbo.menu
  WHERE menu_id = @folder_id
    AND ISNULL(menu_name, N'') NOT IN (N'ภาษีขาย', N'')
    AND ISNULL(link, N'') NOT IN (N'', N'#')
)
BEGIN
  RAISERROR(N'menu_id 200 is already used by another menu. Stop and pick a new folder id.', 16, 1);
END
ELSE IF EXISTS (
  SELECT 1 FROM dbo.menu
  WHERE menu_id = @delete_id
    AND ISNULL(link, N'') <> N''
    AND link NOT LIKE N'%saletaxreport_delete%'
)
BEGIN
  RAISERROR(N'menu_id 203 is already used by another menu. Stop and pick a new delete id.', 16, 1);
END
ELSE
BEGIN

/* ----- 1) folder ภาษีขาย under บัญชี ----- */
IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @folder_id)
BEGIN
  SET IDENTITY_INSERT dbo.menu ON;
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
  VALUES (@folder_id, @account_id, N'ภาษีขาย', N'', ISNULL(@check_icon, N''), @check_sort, ISNULL(@check_show, 1));
  SET IDENTITY_INSERT dbo.menu OFF;
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET menu_name = N'ภาษีขาย',
      parent_menu = @account_id,
      link = N'',
      sort = @check_sort
  WHERE menu_id = @folder_id;
END

/* ----- 2) Check becomes lv3 ----- */
UPDATE dbo.menu
SET parent_menu = CAST(@folder_id AS VARCHAR(20)),
    menu_name = N'กระทบยอดขาย',
    link = N'accounting/saletaxreport/saletaxreport_list',
    sort = 1
WHERE menu_id = @check_id;

/* ----- 3) Print / history becomes lv3 ----- */
IF EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @print_id)
BEGIN
  UPDATE dbo.menu
  SET parent_menu = CAST(@folder_id AS VARCHAR(20)),
      menu_name = N'รายงานภาษีขาย',
      link = N'accounting/saletaxreport/saletaxreport_history',
      sort = 2
  WHERE menu_id = @print_id;
END
ELSE
BEGIN
  SET IDENTITY_INSERT dbo.menu ON;
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
  VALUES (@print_id, CAST(@folder_id AS VARCHAR(20)), N'รายงานภาษีขาย', N'accounting/saletaxreport/saletaxreport_history', N'', 2, 1);
  SET IDENTITY_INSERT dbo.menu OFF;
END

/* ----- 4) Delete API (admin page; hide from customer) ----- */
IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @delete_id)
BEGIN
  SET IDENTITY_INSERT dbo.menu ON;
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
  VALUES (@delete_id, CAST(@folder_id AS VARCHAR(20)), N'ลบข้อมูล API', N'accounting/saletaxreport/saletaxreport_delete', N'', 3, 0);
  SET IDENTITY_INSERT dbo.menu OFF;
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = CAST(@folder_id AS VARCHAR(20)),
      menu_name = N'ลบข้อมูล API',
      link = N'accounting/saletaxreport/saletaxreport_delete',
      sort = 3,
      show_customer = 0
  WHERE menu_id = @delete_id;
END

/* ----- 5) Permissions: same groups/modules as Check (43) ----- */
INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, m.menu_id
FROM dbo.groupmapmenu g
CROSS JOIN (SELECT @folder_id AS menu_id UNION ALL SELECT @print_id UNION ALL SELECT @delete_id) m
WHERE g.menu_id = @check_id
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = m.menu_id
  );

IF OBJECT_ID(N'dbo.bny_module_map_menu', N'U') IS NOT NULL
BEGIN
  DECLARE @map_module_id INT;
  DECLARE @map_menu_id INT;
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
  FETCH NEXT FROM map_cursor INTO @map_module_id, @map_menu_id;
  WHILE @@FETCH_STATUS = 0
  BEGIN
    INSERT INTO dbo.bny_module_map_menu (bny_module_id, menu_id)
    VALUES (@map_module_id, @map_menu_id);
    FETCH NEXT FROM map_cursor INTO @map_module_id, @map_menu_id;
  END
  CLOSE map_cursor;
  DEALLOCATE map_cursor;
END

PRINT N'บัญชี → ภาษีขาย folder is ready.';
PRINT N'  200 = ภาษีขาย (folder)';
PRINT N'  43  = กระทบยอดขาย';
PRINT N'  ' + CAST(@print_id AS VARCHAR(10)) + N' = รายงานภาษีขาย (print)';
PRINT N'  203 = ลบข้อมูล API';
PRINT N'Clear menu cache (storage cache model/menus) or wait up to 1 hour.';

SELECT menu_id, parent_menu, menu_name, link, sort, show_customer
FROM dbo.menu
WHERE menu_id IN (@folder_id, @check_id, @print_id, @delete_id)
   OR CAST(parent_menu AS VARCHAR(20)) IN (@account_id, CAST(@folder_id AS VARCHAR(20)))
ORDER BY parent_menu, sort, menu_id;

END
END
GO
