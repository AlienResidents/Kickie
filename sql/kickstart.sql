create database kickstart;

use kickstart;

grant all on kickstart.* to 'kickstart'@'localhost' identified by 'the bucket';

create table servers (
    id int(11) auto_increment primary key not null,
    hostname varchar(128) not null unique,
    mac_address varchar(17),
    ip_address varchar(15),
    netmask varchar(15),
    profile int(11),
    scheduled_build tinyint(1) default 0 not null,
    last_build_start datetime,
    last_build_finish datetime,
    last_build_duration time
);

DELIMITER //
CREATE trigger update_last_build_duration
  BEFORE UPDATE ON servers
  FOR EACH ROW BEGIN IF NEW.last_build_finish <> OLD.last_build_finish THEN
  SET new.last_build_duration = TIMEDIFF(new.last_build_finish, old.last_build_start);
  END IF;
END//
DELIMITER ;

