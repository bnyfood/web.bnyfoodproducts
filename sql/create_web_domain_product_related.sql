/*
  Domain-scoped product ecosystem (new stack — do not alter old web_products*).
  Pattern per entity:
    live table  -> trigger AFTER INSERT,UPDATE -> history table
    view *_history_lasted = MAX(history_id) per identity
  History is forever (never DELETE history rows). Soft-retire via Status/UPDATE on live.
  ShopID = tenant; web_domain_id = website under that shop.
*/
USE [bnyfoodproducts];
GO

/* =====================================================================
   1) PRODUCT IMAGES  (old: web_product_images)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_product_image', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_image (
    web_domain_product_image_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_model_id INT NULL,
    ordering TINYINT NULL,
    Path NVARCHAR(510) NULL,
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpi_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpi_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_image PRIMARY KEY CLUSTERED (web_domain_product_image_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpi_product ON dbo.web_domain_product_image (web_domain_product_id, ordering);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_image_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_image_history (
    web_domain_product_image_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_image_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_model_id INT NULL,
    ordering TINYINT NULL,
    Path NVARCHAR(510) NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpih_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_image_history PRIMARY KEY CLUSTERED (web_domain_product_image_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpih_img ON dbo.web_domain_product_image_history (web_domain_product_image_id, web_domain_product_image_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_image_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_product_image_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_image_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_image_history h
WHERE h.web_domain_product_image_history_id IN (
  SELECT MAX(h2.web_domain_product_image_history_id) FROM dbo.web_domain_product_image_history h2
  GROUP BY h2.web_domain_product_image_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_image_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_product_image_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_image_trigger ON dbo.web_domain_product_image AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_image_history (
    web_domain_product_image_id, ShopID, web_domain_id, web_domain_product_id,
    web_domain_product_model_id, ordering, Path, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_product_image_id, i.ShopID, i.web_domain_id, i.web_domain_product_id,
         i.web_domain_product_model_id, i.ordering, i.Path, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   2) VARIANT AXIS GROUP  (old: web_product_model_group)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_product_model_group', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_model_group (
    web_domain_product_model_group_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    Name NVARCHAR(255) NOT NULL,
    sort_order INT NOT NULL CONSTRAINT DF_wdpmg_sort DEFAULT (0),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpmg_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpmg_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_model_group PRIMARY KEY CLUSTERED (web_domain_product_model_group_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpmg_product ON dbo.web_domain_product_model_group (web_domain_product_id, sort_order);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_group_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_model_group_history (
    web_domain_product_model_group_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_model_group_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    Name NVARCHAR(255) NOT NULL,
    sort_order INT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpmgh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_model_group_history PRIMARY KEY CLUSTERED (web_domain_product_model_group_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpmgh_g ON dbo.web_domain_product_model_group_history (web_domain_product_model_group_id, web_domain_product_model_group_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_group_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_product_model_group_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_model_group_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_model_group_history h
WHERE h.web_domain_product_model_group_history_id IN (
  SELECT MAX(h2.web_domain_product_model_group_history_id) FROM dbo.web_domain_product_model_group_history h2
  GROUP BY h2.web_domain_product_model_group_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_group_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_product_model_group_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_model_group_trigger ON dbo.web_domain_product_model_group AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_model_group_history (
    web_domain_product_model_group_id, ShopID, web_domain_id, web_domain_product_id,
    Name, sort_order, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_product_model_group_id, i.ShopID, i.web_domain_id, i.web_domain_product_id,
         i.Name, i.sort_order, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   3) VARIANT COMBO + PRICE  (old: web_product_model)
   Links to product history id for hand-checks; also keeps product_id for edit.
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_product_model', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_model (
    web_domain_product_model_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    group1_name NVARCHAR(255) NULL,
    group2_name NVARCHAR(255) NULL,
    title1 NVARCHAR(255) NULL,
    title2 NVARCHAR(255) NULL,
    icon1 NVARCHAR(255) NULL,
    icon2 NVARCHAR(255) NULL,
    price FLOAT NULL,
    quantity INT NULL,
    Status TINYINT NOT NULL CONSTRAINT DF_wdpm_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpm_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpm_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_model PRIMARY KEY CLUSTERED (web_domain_product_model_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpm_product ON dbo.web_domain_product_model (web_domain_product_id);
  CREATE NONCLUSTERED INDEX IX_wdpm_phist ON dbo.web_domain_product_model (web_domain_product_history_id);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_model_history (
    web_domain_product_model_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_model_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    group1_name NVARCHAR(255) NULL,
    group2_name NVARCHAR(255) NULL,
    title1 NVARCHAR(255) NULL,
    title2 NVARCHAR(255) NULL,
    icon1 NVARCHAR(255) NULL,
    icon2 NVARCHAR(255) NULL,
    price FLOAT NULL,
    quantity INT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpmh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_model_history PRIMARY KEY CLUSTERED (web_domain_product_model_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpmh_m ON dbo.web_domain_product_model_history (web_domain_product_model_id, web_domain_product_model_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_product_model_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_model_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_model_history h
WHERE h.web_domain_product_model_history_id IN (
  SELECT MAX(h2.web_domain_product_model_history_id) FROM dbo.web_domain_product_model_history h2
  GROUP BY h2.web_domain_product_model_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_model_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_product_model_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_model_trigger ON dbo.web_domain_product_model AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_model_history (
    web_domain_product_model_id, ShopID, web_domain_id, web_domain_product_id, web_domain_product_history_id,
    group1_name, group2_name, title1, title2, icon1, icon2, price, quantity, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_product_model_id, i.ShopID, i.web_domain_id, i.web_domain_product_id,
         ISNULL(i.web_domain_product_history_id, (
           SELECT TOP 1 l.web_domain_product_history_id
           FROM dbo.web_domain_product_history_lasted l
           WHERE l.web_domain_product_id = i.web_domain_product_id
         )),
         i.group1_name, i.group2_name, i.title1, i.title2, i.icon1, i.icon2,
         i.price, i.quantity, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   4) REMOVED: web_domain_product_map (old product_map_model)
   That was parent_product_id ↔ child product_id (variant family tree).
   BNY model is BOM instead: sellable SKU = bundle of fundamental products.
   Use: web_domain_sku + web_domain_sku_map_product (product + quantity).
   Example: S300 + P15 x1 → SKU "S300P15"; engine = 10 bolts + 1 crank + 4 pistons.
   ===================================================================== */

/* =====================================================================
   5) SKU BUNDLE  (old: web_sku / web_sku_history)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_sku', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku (
    web_domain_sku_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NULL,
    sku_name NVARCHAR(255) NOT NULL,
    sku_value INT NULL,
    temp_key NVARCHAR(255) NULL,
    Status TINYINT NOT NULL CONSTRAINT DF_wdsku_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdsku_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdsku_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku PRIMARY KEY CLUSTERED (web_domain_sku_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdsku_domain ON dbo.web_domain_sku (web_domain_id, ShopID, sku_name);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku_history (
    web_domain_sku_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_sku_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_id INT NULL,
    web_domain_product_history_id BIGINT NULL,
    sku_name NVARCHAR(255) NOT NULL,
    sku_value INT NULL,
    temp_key NVARCHAR(255) NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdskuh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku_history PRIMARY KEY CLUSTERED (web_domain_sku_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdskuh_s ON dbo.web_domain_sku_history (web_domain_sku_id, web_domain_sku_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_sku_history_lasted;
GO
CREATE VIEW dbo.web_domain_sku_history_lasted AS
SELECT h.* FROM dbo.web_domain_sku_history h
WHERE h.web_domain_sku_history_id IN (
  SELECT MAX(h2.web_domain_sku_history_id) FROM dbo.web_domain_sku_history h2
  GROUP BY h2.web_domain_sku_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_sku_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_sku_trigger;
GO
CREATE TRIGGER dbo.web_domain_sku_trigger ON dbo.web_domain_sku AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_sku_history (
    web_domain_sku_id, ShopID, web_domain_id, web_domain_product_id, web_domain_product_history_id,
    sku_name, sku_value, temp_key, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_sku_id, i.ShopID, i.web_domain_id, i.web_domain_product_id,
         CASE WHEN i.web_domain_product_id IS NULL THEN NULL ELSE (
           SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l
           WHERE l.web_domain_product_id = i.web_domain_product_id
         ) END,
         i.sku_name, i.sku_value, i.temp_key, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   6) SKU ↔ PRODUCT COMPONENTS  (old: web_sku_map_product)
   Component link stores product_history_id for cost/hand-check joins.
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_sku_map_product', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku_map_product (
    web_domain_sku_map_product_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_sku_id INT NOT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    quantity INT NOT NULL CONSTRAINT DF_wdskump_qty DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdskump_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdskump_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku_map_product PRIMARY KEY CLUSTERED (web_domain_sku_map_product_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdskump_sku ON dbo.web_domain_sku_map_product (web_domain_sku_id);
  CREATE NONCLUSTERED INDEX IX_wdskump_phist ON dbo.web_domain_sku_map_product (web_domain_product_history_id);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_map_product_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku_map_product_history (
    web_domain_sku_map_product_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_sku_map_product_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_sku_id INT NOT NULL,
    web_domain_sku_history_id BIGINT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    quantity INT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdskumph_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku_map_product_history PRIMARY KEY CLUSTERED (web_domain_sku_map_product_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdskumph_m ON dbo.web_domain_sku_map_product_history (web_domain_sku_map_product_id, web_domain_sku_map_product_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_map_product_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_sku_map_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_sku_map_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_sku_map_product_history h
WHERE h.web_domain_sku_map_product_history_id IN (
  SELECT MAX(h2.web_domain_sku_map_product_history_id) FROM dbo.web_domain_sku_map_product_history h2
  GROUP BY h2.web_domain_sku_map_product_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_sku_map_product_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_sku_map_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_sku_map_product_trigger ON dbo.web_domain_sku_map_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_sku_map_product_history (
    web_domain_sku_map_product_id, ShopID, web_domain_id, web_domain_sku_id, web_domain_sku_history_id,
    web_domain_product_id, web_domain_product_history_id, quantity, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_sku_map_product_id, i.ShopID, i.web_domain_id, i.web_domain_sku_id,
         (SELECT TOP 1 s.web_domain_sku_history_id FROM dbo.web_domain_sku_history_lasted s WHERE s.web_domain_sku_id = i.web_domain_sku_id),
         i.web_domain_product_id,
         ISNULL(i.web_domain_product_history_id, (
           SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l
           WHERE l.web_domain_product_id = i.web_domain_product_id
         )),
         i.quantity, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   7) SKU CATEGORY TREE  (old: web_sku_category)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_sku_category', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku_category (
    web_domain_sku_category_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    parent_id INT NOT NULL CONSTRAINT DF_wdskuc_parent DEFAULT (0),
    web_domain_sku_id INT NULL,
    sku_category_name NVARCHAR(255) NULL,
    sku_category_des NVARCHAR(500) NULL,
    sort_order INT NOT NULL CONSTRAINT DF_wdskuc_sort DEFAULT (0),
    Status TINYINT NOT NULL CONSTRAINT DF_wdskuc_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdskuc_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdskuc_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku_category PRIMARY KEY CLUSTERED (web_domain_sku_category_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdskuc_domain ON dbo.web_domain_sku_category (web_domain_id, parent_id, sort_order);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_category_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_sku_category_history (
    web_domain_sku_category_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_sku_category_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    parent_id INT NOT NULL,
    web_domain_sku_id INT NULL,
    sku_category_name NVARCHAR(255) NULL,
    sku_category_des NVARCHAR(500) NULL,
    sort_order INT NOT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdskuch_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_sku_category_history PRIMARY KEY CLUSTERED (web_domain_sku_category_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdskuch_c ON dbo.web_domain_sku_category_history (web_domain_sku_category_id, web_domain_sku_category_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_category_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_sku_category_history_lasted;
GO
CREATE VIEW dbo.web_domain_sku_category_history_lasted AS
SELECT h.* FROM dbo.web_domain_sku_category_history h
WHERE h.web_domain_sku_category_history_id IN (
  SELECT MAX(h2.web_domain_sku_category_history_id) FROM dbo.web_domain_sku_category_history h2
  GROUP BY h2.web_domain_sku_category_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_sku_category_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_sku_category_trigger;
GO
CREATE TRIGGER dbo.web_domain_sku_category_trigger ON dbo.web_domain_sku_category AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_sku_category_history (
    web_domain_sku_category_id, ShopID, web_domain_id, parent_id, web_domain_sku_id,
    sku_category_name, sku_category_des, sort_order, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_sku_category_id, i.ShopID, i.web_domain_id, i.parent_id, i.web_domain_sku_id,
         i.sku_category_name, i.sku_category_des, i.sort_order, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   8) WHOLESALE LINK  (old: web_wholesale_products)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_wholesale_product', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_wholesale_product (
    web_domain_wholesale_product_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    main_product_id INT NOT NULL,
    product_id INT NOT NULL,
    Qty INT NULL,
    Status TINYINT NOT NULL CONSTRAINT DF_wdwp_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdwp_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdwp_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_wholesale_product PRIMARY KEY CLUSTERED (web_domain_wholesale_product_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdwp_main ON dbo.web_domain_wholesale_product (main_product_id, product_id);
END
GO

IF OBJECT_ID(N'dbo.web_domain_wholesale_product_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_wholesale_product_history (
    web_domain_wholesale_product_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_wholesale_product_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    main_product_id INT NOT NULL,
    product_id INT NOT NULL,
    main_product_history_id BIGINT NULL,
    product_history_id BIGINT NULL,
    Qty INT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdwph_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_wholesale_product_history PRIMARY KEY CLUSTERED (web_domain_wholesale_product_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdwph_w ON dbo.web_domain_wholesale_product_history (web_domain_wholesale_product_id, web_domain_wholesale_product_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_wholesale_product_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_wholesale_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_wholesale_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_wholesale_product_history h
WHERE h.web_domain_wholesale_product_history_id IN (
  SELECT MAX(h2.web_domain_wholesale_product_history_id) FROM dbo.web_domain_wholesale_product_history h2
  GROUP BY h2.web_domain_wholesale_product_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_wholesale_product_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_wholesale_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_wholesale_product_trigger ON dbo.web_domain_wholesale_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_wholesale_product_history (
    web_domain_wholesale_product_id, ShopID, web_domain_id, main_product_id, product_id,
    main_product_history_id, product_history_id, Qty, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_wholesale_product_id, i.ShopID, i.web_domain_id, i.main_product_id, i.product_id,
         (SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l WHERE l.web_domain_product_id = i.main_product_id),
         (SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l WHERE l.web_domain_product_id = i.product_id),
         i.Qty, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* =====================================================================
   9) VOUCHER ↔ PRODUCT  (old: web_voucher_products)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_voucher_product', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_voucher_product (
    web_domain_voucher_product_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    VoucherID BIGINT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    QtyLimit INT NULL,
    Status TINYINT NOT NULL CONSTRAINT DF_wdvp_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdvp_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdvp_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_voucher_product PRIMARY KEY CLUSTERED (web_domain_voucher_product_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdvp_prod ON dbo.web_domain_voucher_product (web_domain_product_id);
END
GO

IF OBJECT_ID(N'dbo.web_domain_voucher_product_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_voucher_product_history (
    web_domain_voucher_product_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_voucher_product_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    VoucherID BIGINT NULL,
    web_domain_product_id INT NOT NULL,
    web_domain_product_history_id BIGINT NULL,
    QtyLimit INT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdvph_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_voucher_product_history PRIMARY KEY CLUSTERED (web_domain_voucher_product_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdvph_v ON dbo.web_domain_voucher_product_history (web_domain_voucher_product_id, web_domain_voucher_product_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_voucher_product_history_lasted', N'V') IS NOT NULL DROP VIEW dbo.web_domain_voucher_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_voucher_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_voucher_product_history h
WHERE h.web_domain_voucher_product_history_id IN (
  SELECT MAX(h2.web_domain_voucher_product_history_id) FROM dbo.web_domain_voucher_product_history h2
  GROUP BY h2.web_domain_voucher_product_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_voucher_product_trigger', N'TR') IS NOT NULL DROP TRIGGER dbo.web_domain_voucher_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_voucher_product_trigger ON dbo.web_domain_voucher_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_voucher_product_history (
    web_domain_voucher_product_id, ShopID, web_domain_id, VoucherID,
    web_domain_product_id, web_domain_product_history_id, QtyLimit, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_voucher_product_id, i.ShopID, i.web_domain_id, i.VoucherID, i.web_domain_product_id,
         ISNULL(i.web_domain_product_history_id, (
           SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l
           WHERE l.web_domain_product_id = i.web_domain_product_id
         )),
         i.QtyLimit, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO
