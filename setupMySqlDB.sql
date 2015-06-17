--if tables exist, then:: DROP TABLE `application`, `appstatus`, `serverreporting`;

CREATE TABLE AppStatus(
	APPID int NOT NULL,
	Company varchar(5) NULL,
	Environment varchar(50) NULL,
	Status varchar(50) NULL,
	TargetDate date NULL,
	ActualDate date NULL,
	PlanningComplete tinyint NULL,
	DesignComplete tinyint NULL
);

CREATE TABLE ServerReporting(
	APPID int NOT NULL,
	ServerName varchar(100) NOT NULL,
	HostName varchar(100) NULL,
	ServerType varchar(50) NULL,
	ServerStatus varchar(50) NULL,
	OSName varchar(100) NULL,
	AppCIName varchar(100) NULL,
	DeviceFunction varchar(50) NULL,
	Site varchar(50) NULL,
	Cell varchar(20) NULL,
	Pod varchar(20) NULL,
	Environment varchar(50) NULL,
	Company varchar(10) NULL,
	ServerOrService varchar(100) NULL,
	Status varchar(100) NULL,
	PlanDate date NULL,
	ActualDate date NULL,
	ExcludeDate date NULL
);


CREATE TABLE Application(
	APPID int NOT NULL,
	ApplicationName varchar(100) NULL,
	ApplicationStatus varchar(50) NULL,
	ITOwnerGroup varchar(100) NULL,
	BusOwnerGroup varchar(100) NULL,
	Criticality varchar(50) NULL,
	ApplicationCIName varchar(100) NULL,
	Alias varchar(100) NULL,
	Description varchar(1000) NULL,
	Note varchar(1000) NULL,
	smpfctnApproachId varchar(50) NULL,
	dataCenterStdztn_flg bit NULL,
	CIFunctions varchar(500) NULL,
	confidentiality varchar(50) NULL,
	finCloseCritical_flg bit NULL,
	ITOwners varchar(300) NULL,
	BusOwners varchar(300) NULL,
	SupportOwners varchar(300) NULL,
	TechDevLead varchar(300) NULL,
	SupportOwnerGroup varchar(100) NULL,
	AppCommonName varchar(500) NULL
);


------------------------------ DATA LOADING ---------------
TRUNCATE `application`;
TRUNCATE `appstatus`;
TRUNCATE `serverreporting`;

LOAD DATA INFILE "C:/xampp/htdocs/josh/AppStatusData.csv" 
INTO TABLE AppStatus
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE "C:/xampp/htdocs/josh/ApplicationData.csv" 
INTO TABLE Application
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE "C:/xampp/htdocs/josh/ServerStatusData.csv" 
INTO TABLE ServerReporting
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

