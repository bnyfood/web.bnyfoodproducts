USE [bnyfoodproducts]
GO

/****** Step 1: Add system time columns ******/
ALTER TABLE dbo.web_bny_gift
ADD 
  ValidFrom datetime2(7) GENERATED ALWAYS AS ROW START NOT NULL
    CONSTRAINT DF_web_bny_gift_ValidFrom DEFAULT SYSUTCDATETIME(),
  ValidTo datetime2(7) GENERATED ALWAYS AS ROW END NOT NULL
    CONSTRAINT DF_web_bny_gift_ValidTo DEFAULT CONVERT(datetime2(7), '9999-12-31 23:59:59.9999999'),
  PERIOD FOR SYSTEM_TIME (ValidFrom, ValidTo);
GO

/****** Step 2: Create history table ******/
CREATE TABLE dbo.web_bny_gift_history
(
  web_bny_gift_id bigint NOT NULL,
  web_bny_gift_pic varchar(255) NULL,
  web_bny_gift_detail text NULL,
  web_bny_gift_now int NULL,
  ValidFrom datetime2(7) NOT NULL,
  ValidTo datetime2(7) NOT NULL
);
GO

/****** Step 3: Enable system versioning ******/
ALTER TABLE dbo.web_bny_gift
SET (SYSTEM_VERSIONING = ON (HISTORY_TABLE = dbo.web_bny_gift_history));
GO
