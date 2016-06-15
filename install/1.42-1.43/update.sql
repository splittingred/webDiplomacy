CREATE TABLE wD_FtfLinks ( `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `England` varchar(100) NOT NULL, `France` varchar(100) NOT NULL, `Italy` varchar(100) NOT NULL, `Germany` varchar(100) NOT NULL, `Austria` varchar(100) NOT NULL, `Turkey` varchar(100) NOT NULL, `Russia` varchar(100) NOT NULL, `mapLink` varchar(512) NOT NULL,  `videoLink`  varchar(512) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `wD_Users`
CHANGE `type` `type` SET(
			'Banned', 'Guest', 'System', 'User', 'Moderator',
			'Admin', 'Donator', 'DonatorBronze', 'DonatorSilver',
			'DonatorGold', 'DonatorPlatinum', 'ForumModerator', 'FtfTD'
) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'User';

UPDATE `wD_Misc` SET `value` = '143' WHERE `name` = 'Version';
