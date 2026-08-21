-- Chat learning store (also auto-created by Chat_learn_model / Ai_settings_model).
IF OBJECT_ID('dbo.ai_settings', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.ai_settings (
		settings_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		provider VARCHAR(30) NOT NULL CONSTRAINT DF_ai_settings_provider DEFAULT ('openai'),
		model_name VARCHAR(80) NOT NULL CONSTRAINT DF_ai_settings_model DEFAULT ('gpt-4o-mini'),
		api_key NVARCHAR(500) NULL,
		observe_chat BIT NOT NULL CONSTRAINT DF_ai_settings_observe DEFAULT (1),
		auto_distill BIT NOT NULL CONSTRAINT DF_ai_settings_distill DEFAULT (1),
		updated_at DATETIME NOT NULL CONSTRAINT DF_ai_settings_upd DEFAULT (GETDATE())
	);
	INSERT INTO dbo.ai_settings (provider, model_name) VALUES ('openai', 'gpt-4o-mini');
END
GO

IF OBJECT_ID('dbo.chat_thread', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.chat_thread (
		thread_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		platform VARCHAR(20) NOT NULL,
		platform_conv_id VARCHAR(80) NULL,
		buyer_name NVARCHAR(200) NULL,
		status VARCHAR(20) NOT NULL CONSTRAINT DF_chat_thread_status DEFAULT ('open'),
		last_message_at DATETIME NULL,
		cdate DATETIME NOT NULL CONSTRAINT DF_chat_thread_cdate DEFAULT (GETDATE())
	);
END
GO

IF OBJECT_ID('dbo.chat_message', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.chat_message (
		message_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		thread_id INT NOT NULL,
		direction VARCHAR(10) NOT NULL,
		body NVARCHAR(MAX) NOT NULL,
		sender VARCHAR(20) NOT NULL,
		ai_draft NVARCHAR(MAX) NULL,
		cdate DATETIME NOT NULL CONSTRAINT DF_chat_message_cdate DEFAULT (GETDATE())
	);
END
GO

IF OBJECT_ID('dbo.chat_reply_example', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.chat_reply_example (
		example_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		platform VARCHAR(20) NOT NULL,
		thread_id INT NULL,
		inbound_text NVARCHAR(MAX) NOT NULL,
		outbound_text NVARCHAR(MAX) NOT NULL,
		ai_draft NVARCHAR(MAX) NULL,
		human_edited BIT NOT NULL CONSTRAINT DF_chat_example_edited DEFAULT (0),
		weight SMALLINT NOT NULL CONSTRAINT DF_chat_example_w DEFAULT (1),
		cdate DATETIME NOT NULL CONSTRAINT DF_chat_example_cdate DEFAULT (GETDATE())
	);
END
GO

IF OBJECT_ID('dbo.chat_playbook', 'U') IS NULL
BEGIN
	CREATE TABLE dbo.chat_playbook (
		playbook_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		platform VARCHAR(20) NOT NULL,
		rules_text NVARCHAR(MAX) NULL,
		example_count INT NOT NULL CONSTRAINT DF_chat_playbook_cnt DEFAULT (0),
		updated_at DATETIME NOT NULL CONSTRAINT DF_chat_playbook_upd DEFAULT (GETDATE())
	);
END
GO
