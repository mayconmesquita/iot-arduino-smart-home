CREATE TABLE users(
	`id_user` INT(11) NOT NULL auto_increment,
	`nome_user` VARCHAR(100) NOT NULL ,
	`email_user` VARCHAR(100) NOT NULL ,
	`senha_user` VARCHAR(128) NOT NULL ,
	`permissao_user` INT(2) DEFAULT '1' ,
	`link_user` VARCHAR(40) NOT NULL ,
	`data_cadastro` DATE NOT NULL ,
    `hora_cadastro` TIME NOT NULL ,
    `data_ultimo_login` DATE NOT NULL ,    
    `hora_ultimo_login` TIME NOT NULL ,
    `status_user` INT(2) default '2',
	`ping_user` TIME NOT NULL ,
	`language_user` VARCHAR(40) NOT NULL ,
	PRIMARY KEY (id_user)
);

INSERT INTO `users` (`id_user`,`nome_user`,`email_user`,`senha_user`,`permissao_user`,`link_user`,`data_cadastro`,`hora_cadastro`,`data_ultimo_login`,`hora_ultimo_login`,`status_user`,`ping_user`) VALUES 
 (1,'Administrador','admin@admin.com','58bf76d01766a431d76209672b3735f7ba50f9be9bb46d7d060d082cb561a2c41a1e54419705c3102267efe863972a35723256af533cf69b8110ac6da7ce4eee','5','dc0ba866ed62bcb59ed1ecf50a8a73a16e1ba7c7',NOW(),NOW(),'','','1',NOW());