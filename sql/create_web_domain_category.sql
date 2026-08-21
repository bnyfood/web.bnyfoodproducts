/*
  Domain-scoped product categories (max 3 tiers via parent_id).
  Each web_domain has its own category tree.
*/
USE [bnyfoodproducts];
GO

IF OBJECT_ID(N'dbo.web_domain_category', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_category (
    web_domain_category_id INT IDENTITY(1,1) NOT NULL,
    web_domain_id INT NOT NULL,
    ShopID INT NOT NULL,
    Title NVARCHAR(255) NOT NULL,
    Description NVARCHAR(MAX) NULL,
    parent_id INT NOT NULL CONSTRAINT DF_web_domain_category_parent DEFAULT (0),
    sort_order INT NOT NULL CONSTRAINT DF_web_domain_category_sort DEFAULT (0),
    thumbnail NVARCHAR(255) NULL,
    is_visible BIT NOT NULL CONSTRAINT DF_wdc_visible DEFAULT (1),
    seo_title NVARCHAR(255) NULL,
    seo_description NVARCHAR(MAX) NULL,
    seo_keywords NVARCHAR(500) NULL,
    seo_slug NVARCHAR(255) NULL,
    cdate DATETIME NOT NULL CONSTRAINT DF_web_domain_category_cdate DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_category PRIMARY KEY CLUSTERED (web_domain_category_id)
  );
  CREATE NONCLUSTERED INDEX IX_web_domain_category_domain
    ON dbo.web_domain_category (web_domain_id, parent_id, sort_order);
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'thumbnail') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD thumbnail NVARCHAR(255) NULL;
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'is_visible') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD is_visible BIT NOT NULL CONSTRAINT DF_wdc_visible DEFAULT (1);
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'seo_title') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD seo_title NVARCHAR(255) NULL;
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'seo_description') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD seo_description NVARCHAR(MAX) NULL;
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'seo_keywords') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD seo_keywords NVARCHAR(500) NULL;
END
GO

IF COL_LENGTH(N'dbo.web_domain_category', N'seo_slug') IS NULL
BEGIN
  ALTER TABLE dbo.web_domain_category ADD seo_slug NVARCHAR(255) NULL;
END
GO

UPDATE dbo.menu
SET link = N'webs/products/category/manage'
WHERE menu_id = 10064;
GO

UPDATE dbo.menu
SET link = N''
WHERE menu_id = 10060;
GO
