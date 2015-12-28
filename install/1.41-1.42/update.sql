ALTER TABLE wD_Backup_Games CHANGE `pressType` `pressType` enum('Regular','PublicPressOnly','NoPress','RulebookPress') NOT NULL DEFAULT 'Regular';
ALTER TABLE wD_Games CHANGE `pressType` `pressType` enum('Regular','PublicPressOnly','NoPress','RulebookPress') NOT NULL DEFAULT 'Regular';
ALTER TABLE wD_Users CHANGE `type` `type` set('Banned','Guest','System','User','Moderator','Admin','Donator','DonatorBronze','DonatorSilver','DonatorGold','DonatorPlatinum','ForumModerator','Developer') NOT NULL DEFAULT 'User';

UPDATE `wD_Misc` SET `value` = '142' WHERE `name` = 'Version';
