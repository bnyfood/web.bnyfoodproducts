-- Daily advertising spend ingested via webhook (and later Ads APIs).
IF OBJECT_ID('dbo.platform_ads_spend', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.platform_ads_spend (
		ads_spend_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		platform VARCHAR(20) NOT NULL,
		spend_date DATE NOT NULL,
		spend DECIMAL(18,2) NOT NULL CONSTRAINT DF_platform_ads_spend_spend DEFAULT (0),
		impressions BIGINT NULL,
		clicks INT NULL,
		conversions INT NULL,
		campaign_id VARCHAR(80) NULL,
		campaign_name NVARCHAR(255) NULL,
		currency VARCHAR(10) NULL CONSTRAINT DF_platform_ads_spend_ccy DEFAULT ('THB'),
		source VARCHAR(30) NULL,
		payload NVARCHAR(MAX) NULL,
		cdate DATETIME NOT NULL CONSTRAINT DF_platform_ads_spend_cdate DEFAULT (GETDATE())
	);
	CREATE INDEX IX_platform_ads_spend_plat_date
		ON dbo.platform_ads_spend (platform, spend_date);
END
GO
