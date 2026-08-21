/*
  Add registrar_link, ssl_link, expire_date to dbo.web_domain
*/
USE [bnyfoodproducts];
GO

IF COL_LENGTH('dbo.web_domain', 'registrar_link') IS NULL
  ALTER TABLE dbo.web_domain ADD registrar_link NVARCHAR(500) NULL;
GO

IF COL_LENGTH('dbo.web_domain', 'ssl_link') IS NULL
  ALTER TABLE dbo.web_domain ADD ssl_link NVARCHAR(500) NULL;
GO

IF COL_LENGTH('dbo.web_domain', 'expire_date') IS NULL
  ALTER TABLE dbo.web_domain ADD expire_date DATE NULL;
GO
