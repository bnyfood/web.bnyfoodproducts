/*
  SaaS rental customer (ShopID = tenant) + trial domain (not BNY-branded).
  web_shop remains the legacy ShopID registry; web_saas_customer stores rental profile.
*/
USE [bnyfoodproducts];
GO

IF OBJECT_ID(N'dbo.web_saas_customer', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_saas_customer (
    ShopID BIGINT NOT NULL,                          -- SaaS tenant / customer id (= web_shop.ShopID)
    company_name NVARCHAR(200) NOT NULL,
    display_name NVARCHAR(200) NULL,
    contact_name NVARCHAR(150) NULL,
    email NVARCHAR(150) NULL,
    phone NVARCHAR(50) NULL,
    tax_id NVARCHAR(20) NULL,
    address_line NVARCHAR(255) NULL,
    province NVARCHAR(100) NULL,
    zipcode NVARCHAR(10) NULL,
    plan_code NVARCHAR(50) NULL,                     -- e.g. TRIAL, BASIC, PRO
    is_trial BIT NOT NULL CONSTRAINT DF_wsc_trial DEFAULT (1),
    is_active BIT NOT NULL CONSTRAINT DF_wsc_active DEFAULT (1),
    trial_expires_at DATETIME NULL,
    notes NVARCHAR(MAX) NULL,
    cdate DATETIME NOT NULL CONSTRAINT DF_wsc_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wsc_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_saas_customer PRIMARY KEY CLUSTERED (ShopID)
  );
END
GO

/* Seed trial SaaS customer + shop + domain (no BNY Food in domain name) */
DECLARE @shop_id BIGINT;
DECLARE @domain_name NVARCHAR(100) = N'erpsaas.demo';

IF NOT EXISTS (SELECT 1 FROM dbo.web_shop WHERE ShopName = N'ERP SaaS Trial')
BEGIN
  -- allocate ShopID above current max (web_shop.ShopID is IDENTITY — use IDENTITY_INSERT)
  SELECT @shop_id = ISNULL(MAX(ShopID), 0) + 1 FROM dbo.web_shop;
  IF @shop_id < 20000 SET @shop_id = 20001; -- keep trial SaaS ids in a clear range

  SET IDENTITY_INSERT dbo.web_shop ON;
  INSERT INTO dbo.web_shop (ShopID, ShopName, domain, URL_home, ip, customer_code)
  VALUES (@shop_id, N'ERP SaaS Trial', @domain_name, N'https://erpsaas.demo', NULL, N'SAAS_TRIAL');
  SET IDENTITY_INSERT dbo.web_shop OFF;

  INSERT INTO dbo.web_saas_customer (
    ShopID, company_name, display_name, contact_name, email, phone,
    plan_code, is_trial, is_active, trial_expires_at, notes
  )
  VALUES (
    @shop_id,
    N'ERP SaaS Trial Co., Ltd.',
    N'ERP SaaS Trial',
    N'Trial Admin',
    N'trial@erpsaas.demo',
    N'0800000000',
    N'TRIAL',
    1,
    1,
    DATEADD(DAY, 30, GETDATE()),
    N'Trial tenant for SaaS rental — domain is not BNY-branded.'
  );
END
ELSE
BEGIN
  SELECT @shop_id = ShopID FROM dbo.web_shop WHERE ShopName = N'ERP SaaS Trial';
END

IF @shop_id IS NULL
  SELECT @shop_id = ShopID FROM dbo.web_saas_customer WHERE company_name = N'ERP SaaS Trial Co., Ltd.';

IF @shop_id IS NULL SET @shop_id = 20001;

IF NOT EXISTS (SELECT 1 FROM dbo.web_domain WHERE web_domain_name = @domain_name)
BEGIN
  INSERT INTO dbo.web_domain (web_domain_name, ShopID, cdate)
  VALUES (@domain_name, @shop_id, GETDATE());
END
ELSE
BEGIN
  UPDATE dbo.web_domain SET ShopID = @shop_id WHERE web_domain_name = @domain_name;
END
GO
