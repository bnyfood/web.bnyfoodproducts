/*
  Fix: Webs → Domains menu currently has empty link (shows as #).
  Sets link to webs/domains/domains_list and ensures web_domain table exists.
*/

USE [bnyfoodproducts];
GO

/* 1) table */
IF OBJECT_ID(N'dbo.web_domain', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain (
    web_domain_id   INT IDENTITY(1,1) NOT NULL,
    web_domain_name NVARCHAR(255)     NOT NULL,
    ShopID          INT               NOT NULL,
    CONSTRAINT PK_web_domain PRIMARY KEY CLUSTERED (web_domain_id)
  );
  CREATE UNIQUE NONCLUSTERED INDEX UX_web_domain_shop_name
    ON dbo.web_domain (ShopID, web_domain_name);
END
GO

/* 2) Domains menu link under Webs */
DECLARE @webs_id INT;
DECLARE @domains_id INT;

SELECT TOP 1 @webs_id = menu_id
FROM dbo.menu
WHERE menu_name IN (N'Webs', N'WEBS', N'webs')
ORDER BY menu_id;

SELECT TOP 1 @domains_id = menu_id
FROM dbo.menu
WHERE menu_name = N'Domains'
  AND (
    @webs_id IS NULL
    OR CAST(parent_menu AS VARCHAR(20)) = CAST(@webs_id AS VARCHAR(20))
  )
ORDER BY menu_id;

IF @domains_id IS NULL
BEGIN
  SELECT TOP 1 @domains_id = menu_id
  FROM dbo.menu
  WHERE menu_name = N'Domains'
  ORDER BY menu_id;
END

IF @domains_id IS NULL
BEGIN
  RAISERROR(N'Domains menu row not found. Create it under Webs first.', 16, 1);
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET link = N'webs/domains/domains_list',
      parent_menu = CASE WHEN @webs_id IS NOT NULL THEN CAST(@webs_id AS VARCHAR(20)) ELSE parent_menu END
  WHERE menu_id = @domains_id
     OR (
       menu_name = N'Domains'
       AND (link IS NULL OR LTRIM(RTRIM(link)) IN (N'', N'#', N'#!'))
     );

  PRINT N'Updated Domains menu_id=' + CAST(@domains_id AS NVARCHAR(20)) + N' → webs/domains/domains_list';

  SELECT menu_id, parent_menu, menu_name, link, sort
  FROM dbo.menu
  WHERE menu_id = @domains_id
     OR (@webs_id IS NOT NULL AND (menu_id = @webs_id OR CAST(parent_menu AS VARCHAR(20)) = CAST(@webs_id AS VARCHAR(20))))
  ORDER BY parent_menu, sort, menu_id;
END
GO
