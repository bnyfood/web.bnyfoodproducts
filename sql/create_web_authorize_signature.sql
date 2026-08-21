/*
  Authorize signature for all documents (tax invoice, credit note, PO).
  Menu: Setting / Account / Authorize signature
  After run: log out/in (menu cache 1 hour) or wait.
*/
USE [bnyfoodproducts];
GO

IF OBJECT_ID(N'dbo.web_authorize_signature', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_authorize_signature (
    web_authorize_signature_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    file_name NVARCHAR(255) NOT NULL,
    file_path NVARCHAR(500) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  );
  -- Latest ID can be updated. Adding a new row freezes documents bound to the previous ID.
END
GO

IF OBJECT_ID(N'dbo.web_document_signature_snapshot', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_document_signature_snapshot (
    web_document_signature_snapshot_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    doc_type VARCHAR(50) NOT NULL,
    doc_code NVARCHAR(100) NOT NULL,
    ref_number NVARCHAR(100) NULL,
    platform INT NULL,
    web_authorize_signature_id INT NULL,
    file_name NVARCHAR(255) NOT NULL,
    file_path NVARCHAR(500) NOT NULL,
    used_at DATETIME NULL
  );
  CREATE UNIQUE INDEX UX_web_document_signature_snapshot_doc
    ON dbo.web_document_signature_snapshot (doc_type, doc_code);
END
GO

DECLARE @menu_id INT = 204;
DECLARE @account_id VARCHAR(20) = '29';
DECLARE @creditnote_id INT = 44;

IF NOT EXISTS (SELECT 1 FROM dbo.menu WHERE menu_id = @menu_id)
BEGIN
  SET IDENTITY_INSERT dbo.menu ON;
  INSERT INTO dbo.menu (menu_id, parent_menu, menu_name, link, icon, sort, show_customer)
  VALUES (
    @menu_id,
    @account_id,
    N'Authorize signature',
    N'accounting/authorize_signature/authorize_signature_form',
    N'',
    20,
    1
  );
  SET IDENTITY_INSERT dbo.menu OFF;
END
ELSE
BEGIN
  UPDATE dbo.menu
  SET parent_menu = @account_id,
      menu_name = N'Authorize signature',
      link = N'accounting/authorize_signature/authorize_signature_form'
  WHERE menu_id = @menu_id;
END
GO

INSERT INTO dbo.groupmapmenu (group_id, menu_id)
SELECT g.group_id, 204
FROM dbo.groupmapmenu g
WHERE g.menu_id = 44
  AND NOT EXISTS (
    SELECT 1 FROM dbo.groupmapmenu x
    WHERE x.group_id = g.group_id AND x.menu_id = 204
  );
GO

IF OBJECT_ID(N'dbo.bny_module_map_menu', N'U') IS NOT NULL
BEGIN
  INSERT INTO dbo.bny_module_map_menu (bny_module_id, menu_id)
  SELECT mm.bny_module_id, 204
  FROM dbo.bny_module_map_menu mm
  WHERE mm.menu_id = 44
    AND NOT EXISTS (
      SELECT 1 FROM dbo.bny_module_map_menu x
      WHERE x.bny_module_id = mm.bny_module_id AND x.menu_id = 204
    );
END
GO
