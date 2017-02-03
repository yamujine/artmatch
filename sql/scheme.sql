SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema pickartyou
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema pickartyou
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `pickartyou` DEFAULT CHARACTER SET utf8 ;
USE `pickartyou` ;

-- -----------------------------------------------------
-- Table `pickartyou`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`users` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`users` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `type` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`idx`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artworks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artworks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artworks` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `owner_idx` INT NOT NULL COMMENT '작가의 user PK',
  `type` TINYINT UNSIGNED NOT NULL COMMENT '작품 구분 - 일러스트/회화/설치 작품 등 구분',
  `title` VARCHAR(100) NOT NULL COMMENT '작품 제목',
  `description` TEXT NULL COMMENT '작품 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '작품의 대표 이미지',
  `upload_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '작품 업로드 시간',
  `views` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '조회수',
  PRIMARY KEY (`idx`),
  INDEX `fk_owner_idx_idx` (`owner_idx` ASC),
  CONSTRAINT `fk_artworks_owner_idx`
    FOREIGN KEY (`owner_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`places`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`places` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`places` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `owner_idx` INT NOT NULL COMMENT '장소 소유자의 user PK',
  `name` VARCHAR(45) NOT NULL COMMENT '장소 이름',
  `description` TEXT NULL COMMENT '장소 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '장소 대표 이미지',
  `address` VARCHAR(250) NOT NULL COMMENT '장소의 실제 주소',
  PRIMARY KEY (`idx`),
  INDEX `fk_owner_idx_idx` (`owner_idx` ASC),
  CONSTRAINT `fk_places_owner_idx`
    FOREIGN KEY (`owner_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_images` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_images` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `artwork_idx` INT NOT NULL COMMENT '작품의 PK',
  `image` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idx`),
  INDEX `fk_artwork_idx_idx` (`artwork_idx` ASC),
  CONSTRAINT `fk_artwork_images_artwork_idx`
    FOREIGN KEY (`artwork_idx`)
    REFERENCES `pickartyou`.`artworks` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_images` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_images` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `place_idx` INT NOT NULL,
  `image` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idx`),
  INDEX `fk_place_idx_idx` (`place_idx` ASC),
  CONSTRAINT `fk_place_images_place_idx`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
