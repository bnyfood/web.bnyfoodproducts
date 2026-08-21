/*
  Quick check: is tiktok_prep a TABLE or a VIEW?
  Run in SSMS on bnyfoodproducts and paste the result back if needed.
*/
USE [bnyfoodproducts];
GO

SELECT
  name,
  type_desc,
  CASE type WHEN 'U' THEN 'TABLE' WHEN 'V' THEN 'VIEW' ELSE type_desc END AS kind
FROM sys.objects
WHERE name IN ('tiktok_prep', 'tiktok_prep_api', 'tiktok_data', 'vw_tiktok_prep_check')
ORDER BY name;

-- If VIEW, show definition:
IF OBJECT_ID('dbo.tiktok_prep', 'V') IS NOT NULL
  SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_prep')) AS view_sql;
GO
