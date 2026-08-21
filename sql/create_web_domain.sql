/*
  Storefront domains (multi-site / multi-theme skeleton).
  Used by API: webs/domains/*  →  dbo.web_domain
*/

USE [bnyfoodproducts];
GO

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

  CREATE NONCLUSTERED INDEX IX_web_domain_ShopID
    ON dbo.web_domain (ShopID);

  PRINT N'Created dbo.web_domain';
END
ELSE
BEGIN
  PRINT N'dbo.web_domain already exists';
END
GO

SELECT
  c.name AS column_name,
  t.name AS type_name,
  c.max_length,
  c.is_nullable
FROM sys.columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
WHERE c.object_id = OBJECT_ID(N'dbo.web_domain')
ORDER BY c.column_id;
GO
