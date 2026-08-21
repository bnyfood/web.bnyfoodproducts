/*
  Sales folder + unified chat, and Chat AI under existing Settings.

  After this script:
    การขาย (230)
      แชท (231)             ai/inbox
      คู่มือการตอบ (232)    ai/playbook
    การตั้งค่า (1, existing)
      ระบบแชท (233)         ai/settings

  PHP constants: MENU_SALES 230, MENU_SALES_CHAT 231,
  MENU_SALES_PLAYBOOK 232, MENU_CONFIG_CHAT 233.

  Menu cache is 1 hour — log out/in after a cache clear, or wait.
*/

USE [bnyfoodproducts];
GO

DECLARE @sales_id INT = 230;
DECLARE @inbox_id INT = 231;
DECLARE @playbook_id INT = 232;
DECLARE @chat_set_id INT = 233;
DECLARE @dash_id INT = 26;
DECLARE @settings_id VARCHAR(20) = '1';

IF EXISTS (
  SELECT 1 FROM dbo.menu
  WHERE menu_id IN (@sales_id, @inbox_id, @playbook_id, @chat_set_id)
    AND ISNULL(link, N'') NOT IN (N'', N'#', N'ai/inbox', N'ai/playbook', N'ai/settings')
    AND ISNULL(menu_name, N'') NOT IN (N'การขาย', N'แชท', N'กล่องแชท', N'คู่มือการตอบ', N'ระบบแชท')
)
BEGIN
  RAISERROR(N'menu_id 230-233 is already used by another menu. Stop and pick new ids.', 16, 1);
END
ELSE
BEGIN

SET IDENTITY_INSERT dbo.menu ON;

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @sales_id)
BEGIN
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, menu_name_en, link, icon, sort, show_customer, cdate)
  VALUES (@sales_id, N'root', N'การขาย', N'Sales', N'#', N'wb-chat', 2, 0, GETDATE());
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = N'root',
      menu_name = N'การขาย',
      menu_name_en = N'Sales',
      link = N'#',
      icon = N'wb-chat',
      sort = 2,
      show_customer = 0
  WHERE menu_id = @sales_id;
END

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @inbox_id)
BEGIN
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, menu_name_en, link, icon, sort, show_customer, cdate)
  VALUES (@inbox_id, CAST(@sales_id AS VARCHAR(20)), N'แชท', N'Chat', N'ai/inbox', N'wb-inbox', 1, 0, GETDATE());
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = CAST(@sales_id AS VARCHAR(20)),
      menu_name = N'แชท',
      menu_name_en = N'Chat',
      link = N'ai/inbox',
      icon = N'wb-inbox',
      sort = 1,
      show_customer = 0
  WHERE menu_id = @inbox_id;
END

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @playbook_id)
BEGIN
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, menu_name_en, link, icon, sort, show_customer, cdate)
  VALUES (@playbook_id, CAST(@sales_id AS VARCHAR(20)), N'คู่มือการตอบ', N'Reply playbook', N'ai/playbook', N'wb-book', 2, 0, GETDATE());
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = CAST(@sales_id AS VARCHAR(20)),
      menu_name = N'คู่มือการตอบ',
      menu_name_en = N'Reply playbook',
      link = N'ai/playbook',
      icon = N'wb-book',
      sort = 2,
      show_customer = 0
  WHERE menu_id = @playbook_id;
END

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @chat_set_id)
BEGIN
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, menu_name_en, link, icon, sort, show_customer, cdate)
  VALUES (@chat_set_id, @settings_id, N'ระบบแชท', N'Chat AI', N'ai/settings', N'wb-cloud', 10, 0, GETDATE());
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = @settings_id,
      menu_name = N'ระบบแชท',
      menu_name_en = N'Chat AI',
      link = N'ai/settings',
      icon = N'wb-cloud',
      sort = 10,
      show_customer = 0
  WHERE menu_id = @chat_set_id;
END

SET IDENTITY_INSERT dbo.menu OFF;

INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, m.menu_id
FROM dbo.groupmapmenu g
CROSS JOIN (SELECT @sales_id AS menu_id UNION ALL SELECT @inbox_id UNION ALL SELECT @playbook_id) m
WHERE g.menu_id = @dash_id
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = m.menu_id
  );

INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, @chat_set_id
FROM dbo.groupmapmenu g
WHERE g.menu_id = 1
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = @chat_set_id
  );

IF OBJECT_ID(N'dbo.bny_module_map_menu', N'U') IS NOT NULL
BEGIN
  DECLARE @map_module_id INT;
  DECLARE @map_menu_id INT;
  DECLARE map_cursor CURSOR LOCAL FAST_FORWARD FOR
  SELECT mm.bny_module_id, m.menu_id
  FROM dbo.bny_module_map_menu mm
  CROSS JOIN (
    SELECT @sales_id AS menu_id UNION ALL SELECT @inbox_id UNION ALL SELECT @playbook_id UNION ALL SELECT @chat_set_id
  ) m
  WHERE mm.menu_id IN (@dash_id, 1)
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

PRINT N'Sales + Chat menus are ready.';
PRINT N'  230 = การขาย (folder)';
PRINT N'  231 = แชท';
PRINT N'  232 = คู่มือการตอบ';
PRINT N'  233 = ระบบแชท (under การตั้งค่า)';
PRINT N'Clear menu cache (storage cache model/menus) or wait up to 1 hour.';

SELECT menu_id, parent_menu, menu_name, menu_name_en, link, icon, sort, show_customer
FROM dbo.menu
WHERE menu_id IN (@sales_id, @inbox_id, @playbook_id, @chat_set_id, 1)
   OR CAST(parent_menu AS VARCHAR(20)) IN (N'root', CAST(@sales_id AS VARCHAR(20)), N'1')
ORDER BY CASE WHEN CAST(parent_menu AS VARCHAR(20)) = N'root' THEN 0 ELSE 1 END, parent_menu, sort, menu_id;

END
GO
