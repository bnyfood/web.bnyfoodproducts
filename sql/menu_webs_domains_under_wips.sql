/*
  Menu: WIPs → Domains
  Link: webs/domains/domains_list
  PHP constant: MENU_WEBS_DOMAINS = 210

  Safe to re-run. Clears nothing; only inserts/updates Domains (+ WIPs if missing).
  Menu cache is ~1 hour — log out/in after cache clear, or wait.
*/

USE [bnyfoodproducts];
GO

DECLARE @wip_id INT;
DECLARE @domains_id INT = 210;
DECLARE @perm_from_id INT = 41; /* clone permissions from Brand if present */
DECLARE @wip_name NVARCHAR(100) = N'WIPs';
DECLARE @domains_name NVARCHAR(100) = N'Domains';
DECLARE @domains_link NVARCHAR(255) = N'webs/domains/domains_list';

/* Prefer existing WIP / WIPs / Webs parent */
SELECT TOP 1 @wip_id = menu_id
FROM dbo.menu
WHERE menu_name IN (N'WIPs', N'WIP', N'Wips', N'wips', N'Webs', N'WEBS')
ORDER BY
  CASE menu_name
    WHEN N'WIPs' THEN 1
    WHEN N'WIP' THEN 2
    WHEN N'Wips' THEN 3
    WHEN N'wips' THEN 4
    ELSE 5
  END,
  menu_id;

IF @wip_id IS NULL
BEGIN
  /* Create top-level WIPs under root */
  SELECT @wip_id = ISNULL(MAX(CAST(menu_id AS INT)), 0) + 1 FROM dbo.menu;
  IF @wip_id < 220 SET @wip_id = 220; /* keep clear of known accounting ids */

  IF EXISTS (
    SELECT 1 FROM dbo.menu
    WHERE menu_id = @wip_id
      AND ISNULL(menu_name, N'') NOT IN (N'WIPs', N'WIP', N'Wips', N'wips', N'')
  )
  BEGIN
    RAISERROR(N'Chosen WIP menu_id is already used. Stop and pick a free id.', 16, 1);
    RETURN;
  END

  IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @wip_id)
  BEGIN
    SET IDENTITY_INSERT dbo.menu ON;
    INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
    VALUES (@wip_id, N'root', @wip_name, N'', N'wb-globe', 90, 1);
    SET IDENTITY_INSERT dbo.menu OFF;
    PRINT N'Created WIPs menu_id=' + CAST(@wip_id AS NVARCHAR(20));
  END
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET menu_name = CASE WHEN menu_name IN (N'WIP', N'Wips', N'wips') THEN @wip_name ELSE menu_name END,
      parent_menu = CASE WHEN ISNULL(parent_menu, N'') IN (N'', N'0') THEN N'root' ELSE parent_menu END
  WHERE menu_id = @wip_id;
  PRINT N'Using existing parent menu_id=' + CAST(@wip_id AS NVARCHAR(20));
END

IF EXISTS (
  SELECT 1 FROM dbo.menu
  WHERE menu_id = @domains_id
    AND ISNULL(link, N'') <> N''
    AND link NOT LIKE N'%webs/domains/%'
)
BEGIN
  RAISERROR(N'menu_id 210 is already used by another menu. Stop and pick a new Domains id.', 16, 1);
  RETURN;
END

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @domains_id)
BEGIN
  SET IDENTITY_INSERT dbo.menu ON;
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
  VALUES (
    @domains_id,
    CAST(@wip_id AS VARCHAR(20)),
    @domains_name,
    @domains_link,
    N'',
    1,
    1
  );
  SET IDENTITY_INSERT dbo.menu OFF;
  PRINT N'Created Domains menu_id=210';
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = CAST(@wip_id AS VARCHAR(20)),
      menu_name = @domains_name,
      link = @domains_link,
      sort = 1,
      show_customer = 1
  WHERE menu_id = @domains_id;
  PRINT N'Updated Domains menu_id=210';
END

/* Also fix any older Domains row pointing elsewhere */
UPDATE dbo.menu
SET parent_menu = CAST(@wip_id AS VARCHAR(20)),
    link = @domains_link
WHERE menu_name = @domains_name
  AND menu_id <> @domains_id
  AND (ISNULL(link, N'') LIKE N'%webs/domains%' OR ISNULL(link, N'') = N'');

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @perm_from_id)
BEGIN
  SELECT TOP 1 @perm_from_id = menu_id
  FROM dbo.menu
  WHERE ISNULL(link, N'') <> N''
    AND menu_id <> @domains_id
  ORDER BY menu_id;
END

INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, @domains_id
FROM dbo.groupmapmenu g
WHERE g.menu_id = @perm_from_id
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = @domains_id
  );

INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, @wip_id
FROM dbo.groupmapmenu g
WHERE g.menu_id = @perm_from_id
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = @wip_id
  );

IF OBJECT_ID(N'dbo.bny_module_map_menu', N'U') IS NOT NULL
BEGIN
  INSERT INTO dbo.bny_module_map_menu (bny_module_id, menu_id)
  SELECT mm.bny_module_id, @domains_id
  FROM dbo.bny_module_map_menu mm
  WHERE mm.menu_id = @perm_from_id
    AND NOT EXISTS (
      SELECT 1 FROM dbo.bny_module_map_menu x
      WHERE x.bny_module_id = mm.bny_module_id AND x.menu_id = @domains_id
    );

  INSERT INTO dbo.bny_module_map_menu (bny_module_id, menu_id)
  SELECT mm.bny_module_id, @wip_id
  FROM dbo.bny_module_map_menu mm
  WHERE mm.menu_id = @perm_from_id
    AND NOT EXISTS (
      SELECT 1 FROM dbo.bny_module_map_menu x
      WHERE x.bny_module_id = mm.bny_module_id AND x.menu_id = @wip_id
    );
END

PRINT N'Done. WIPs → Domains ready.';
PRINT N'Clear menu cache (storage cache model/menus) or wait up to 1 hour.';

SELECT menu_id, parent_menu, menu_name, link, sort, show_customer
FROM dbo.menu
WHERE menu_id IN (@wip_id, @domains_id)
   OR CAST(parent_menu AS VARCHAR(20)) = CAST(@wip_id AS VARCHAR(20))
ORDER BY parent_menu, sort, menu_id;
GO
