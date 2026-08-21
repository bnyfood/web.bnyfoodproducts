-- Raw platform webhook events (Lazada / Shopee / TikTok). Table is also auto-created by the model.
IF OBJECT_ID('dbo.platform_webhook_event', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.platform_webhook_event (
		event_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		platform VARCHAR(20) NOT NULL,
		event_code VARCHAR(80) NULL,
		shop_id VARCHAR(80) NULL,
		verified BIT NOT NULL CONSTRAINT DF_platform_webhook_event_verified DEFAULT (0),
		remote_ip VARCHAR(45) NULL,
		headers NVARCHAR(MAX) NULL,
		payload NVARCHAR(MAX) NULL,
		cdate DATETIME NOT NULL CONSTRAINT DF_platform_webhook_event_cdate DEFAULT (GETDATE())
	);
	CREATE INDEX IX_platform_webhook_event_plat_date
		ON dbo.platform_webhook_event (platform, cdate);
	CREATE INDEX IX_platform_webhook_event_code
		ON dbo.platform_webhook_event (event_code);
END
GO
