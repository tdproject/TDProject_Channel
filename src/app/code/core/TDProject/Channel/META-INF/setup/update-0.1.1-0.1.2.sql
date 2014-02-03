CREATE TABLE `channel` (
	`channel_id` int(10) NOT NULL, 
	`name` varchar(255) NOT NULL, 
	`alias` varchar(25) NOT NULL, 
	`summary` text(16)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ENGINE=MyISAM;

ALTER TABLE `channel` ADD CONSTRAINT channel_pk PRIMARY KEY (`channel_id`); 
ALTER TABLE `channel` CHANGE channel_id `channel_id` int(10) AUTO_INCREMENT;

CREATE TABLE `category` (
    `category_id` int(10) NOT NULL, 
    `name` varchar(255) NOT NULL, 
    `alias` varchar(25) NOT NULL, 
    `description` text(16)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ENGINE=MyISAM;

ALTER TABLE `category` ADD CONSTRAINT category_pk PRIMARY KEY (`category_id`); 
ALTER TABLE `category` CHANGE category_id `category_id` int(10) AUTO_INCREMENT;

ALTER TABLE `category` ADD UNIQUE `category_uidx_01` (`name`);  

CREATE TABLE `package` (
    `package_id` int(10) NOT NULL, 
    `channel_id_fk` int(10) NOT NULL, 
    `category_id_fk` int(10) NOT NULL, 
    `name` varchar(255) NOT NULL, 
    `licence` varchar(25) NOT NULL, 
    `licence_uri` varchar(255) NOT NULL, 
    `short_description` varchar(255), 
    `description` text(16), 
    `deprecated` tinyint(1) NOT NULL default 0, 
    `deprecated_channel` varchar(255), 
    `deprecated_package` varchar(255)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ENGINE=MyISAM;

ALTER TABLE `package` ADD CONSTRAINT package_pk PRIMARY KEY (`package_id`); 
ALTER TABLE `package` CHANGE package_id `package_id` int(10) AUTO_INCREMENT;

ALTER TABLE `package` ADD UNIQUE `package_uidx_01` (`name`);       
CREATE INDEX package_idx_01 ON `package` (`channel_id_fk`);
CREATE INDEX package_idx_02 ON `package` (`category_id_fk`);
       
ALTER TABLE `package` ADD CONSTRAINT package_fk_01 FOREIGN KEY (`channel_id_fk`) REFERENCES `channel` (`channel_id`) ON DELETE CASCADE;
ALTER TABLE `package` ADD CONSTRAINT package_fk_02 FOREIGN KEY (`category_id_fk`) REFERENCES `category` (`category_id`) ON DELETE CASCADE;

CREATE TABLE `maintainer` (
    `maintainer_id` int(10) NOT NULL, 
    `package_id_fk` int(10) NOT NULL, 
    `user_id_fk` int(10) NOT NULL,
    `active` tinyint(1) NOT NULL default '1',
    `role` enum('lead','developer','contributor','helper') NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ENGINE=MyISAM;

ALTER TABLE `maintainer` ADD CONSTRAINT maintainer_pk PRIMARY KEY (`maintainer_id`); 
ALTER TABLE `maintainer` CHANGE maintainer_id `maintainer_id` int(10) AUTO_INCREMENT;

CREATE INDEX maintainer_idx_01 ON `maintainer` (`package_id_fk`);
CREATE INDEX maintainer_idx_02 ON `maintainer` (`user_id_fk`);

ALTER TABLE `maintainer` ADD CONSTRAINT maintainer_fk_01 FOREIGN KEY (`package_id_fk`) REFERENCES `package` (`package_id`) ON DELETE CASCADE;
ALTER TABLE `maintainer` ADD CONSTRAINT maintainer_fk_02 FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;