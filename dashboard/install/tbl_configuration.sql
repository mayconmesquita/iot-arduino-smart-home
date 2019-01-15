CREATE TABLE configs(
    `id` INT(11) NOT NULL DEFAULT '1',
	`title` VARCHAR(100) NOT NULL DEFAULT 'Painel de Controle',
	`ip` VARCHAR(100) NOT NULL DEFAULT '192.168.1.50',
	`porta` VARCHAR(20) NOT NULL DEFAULT '8080',
	`tempo_receber` VARCHAR(100) NOT NULL DEFAULT '8000',
	`url` VARCHAR(255) NOT NULL
);

INSERT INTO `configs` (`id`,`title`,`ip`,`porta`,`tempo_receber`,`url`) VALUES 
 (1,'Smart Home','192.168.1.50','8080','8000','');
