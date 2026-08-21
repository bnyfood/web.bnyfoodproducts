USE [bnyfoodproducts]
GO

/****** Step 2: Add system time columns ******/
ALTER TABLE dbo.web_material_unit
ADD 
  ValidFrom datetime2(7) GENERATED ALWAYS AS ROW START NOT NULL
    CONSTRAINT DF_web_material_unit_ValidFrom DEFAULT SYSUTCDATETIME(),
  ValidTo datetime2(7) GENERATED ALWAYS AS ROW END NOT NULL
    CONSTRAINT DF_web_material_unit_ValidTo DEFAULT CONVERT(datetime2(7), '9999-12-31 23:59:59.9999999'),
  PERIOD FOR SYSTEM_TIME (ValidFrom, ValidTo);
GO

/****** Step 3: Create history table ******/
CREATE TABLE dbo.web_material_unit_history
(
  web_material_unit_id bigint NOT NULL,
  material_unit varchar(50) NULL,
  cdate datetime NULL,
  ValidFrom datetime2(7) NOT NULL,
  ValidTo datetime2(7) NOT NULL
);
GO

/****** Step 4: Enable system versioning ******/
ALTER TABLE dbo.web_material_unit
SET (SYSTEM_VERSIONING = ON (HISTORY_TABLE = dbo.web_material_unit_history));
GO
