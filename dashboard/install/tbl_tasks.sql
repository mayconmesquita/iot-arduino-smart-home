CREATE TABLE tbl_tasks(
	`task_id` INT(7) NOT NULL auto_increment,
	`task_name` INT(7) NOT NULL,
	`task_action` INT(2),
	`task_action_offset` INT(2),
	`task_frequency` INT(2),
	`task_date` VARCHAR(10),
	`task_time` VARCHAR(8),
	`task_device_id` INT(7) NOT NULL,
	`task_ground_id` INT(7) NOT NULL,
	`task_building_id` INT(7) NOT NULL,
	`task_status` TINYINT(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (task_id)
);