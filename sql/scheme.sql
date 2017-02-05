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
  `type` TINYINT UNSIGNED NOT NULL COMMENT '창작자/공간소유자 구분 여부',
  `name` VARCHAR(45) NOT NULL COMMENT '유저 이름',
  `email` VARCHAR(50) NOT NULL COMMENT '유저 이메일주소',
  `profile_image` VARCHAR(45) NOT NULL COMMENT '유저 프로필 이미지 파일 이름',
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
  `status` TINYINT NOT NULL COMMENT '작품의 상태 공간구함/전시중/공간구하지 않음',
  `title` VARCHAR(100) NOT NULL COMMENT '작품 제목',
  `description` TEXT NULL COMMENT '작품 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '작품의 대표 이미지',
  `for_sale` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '판매여부 설정',
  `use_comment` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '댓글 사용 여부',
  `upload_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '작품 업로드 시간',
  PRIMARY KEY (`idx`),
  INDEX `idx_owner_idx` (`owner_idx` ASC),
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
  `status` TINYINT NOT NULL COMMENT '작품 모집중 / 모집완료',
  `name` VARCHAR(45) NOT NULL COMMENT '장소 이름',
  `description` TEXT NULL COMMENT '장소 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '장소 대표 이미지',
  `address` VARCHAR(250) NOT NULL COMMENT '장소의 실제 주소',
  `artwork_count` SMALLINT NOT NULL COMMENT '장소에서 모집하는 작품 수',
  `exhibit_period` VARCHAR(10) NOT NULL COMMENT '작품 전시 기간',
  `is_free_exhibit` TINYINT(1) NOT NULL COMMENT '무료 전시 / 비용 지불 여부',
  `use_comment` TINYINT(1) NOT NULL COMMENT '댓글 사용 여부',
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
  `image` VARCHAR(45) NOT NULL COMMENT '작품 이미지 파일명',
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
  `place_idx` INT NOT NULL COMMENT '장소 PK',
  `image` VARCHAR(45) NOT NULL COMMENT '장소 이미지 파일 이름',
  PRIMARY KEY (`idx`),
  INDEX `fk_place_idx_idx` (`place_idx` ASC),
  CONSTRAINT `fk_place_images_place_idx`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`user_artwork_picks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`user_artwork_picks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`user_artwork_picks` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `user_idx` INT NOT NULL COMMENT '유저의 PK',
  `artwork_idx` INT NOT NULL COMMENT '작품의 PK',
  PRIMARY KEY (`idx`),
  INDEX `fk_user_picks_user_idx_idx` (`user_idx` ASC),
  INDEX `fk_user_picks_artwork_idx_idx` (`artwork_idx` ASC),
  CONSTRAINT `fk_user_picks_user_idx`
    FOREIGN KEY (`user_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_picks_artwork_idx`
    FOREIGN KEY (`artwork_idx`)
    REFERENCES `pickartyou`.`artworks` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_comments` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_comments` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `user_idx` INT NOT NULL COMMENT '유저 PK',
  `artwork_idx` INT NOT NULL COMMENT '작품 PK',
  `comment` TEXT NOT NULL COMMENT '작품에 달린 댓글',
  PRIMARY KEY (`idx`),
  INDEX `fk_user_comments_user_idx_idx` (`user_idx` ASC),
  INDEX `fk_user_comments_artwork_idx_idx` (`artwork_idx` ASC),
  CONSTRAINT `fk_artwork_comments_user_idx`
    FOREIGN KEY (`user_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artwork_comments_artwork_idx`
    FOREIGN KEY (`artwork_idx`)
    REFERENCES `pickartyou`.`artworks` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`exhibitions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`exhibitions` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`exhibitions` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `artwork_idx` INT NOT NULL COMMENT '유저의 PK',
  `place_idx` INT NOT NULL COMMENT '장소의 PK',
  `start_date` DATETIME NOT NULL COMMENT '전시 시작 날짜',
  `end_date` DATETIME NOT NULL COMMENT '전시 종료 날짜',
  PRIMARY KEY (`idx`),
  INDEX `fk_exhibitions_place_idx_idx` (`place_idx` ASC),
  INDEX `fk_exhibitions_artwork_idx_idx` (`artwork_idx` ASC),
  CONSTRAINT `fk_exhibitions_artwork_idx`
    FOREIGN KEY (`artwork_idx`)
    REFERENCES `pickartyou`.`artworks` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exhibitions_place_idx`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_tags` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_tags` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL COMMENT '작품에 사용될 태그명',
  PRIMARY KEY (`idx`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_tag_pair`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_tag_pair` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_tag_pair` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `artwork_idx` INT NOT NULL COMMENT '작품 PK',
  `tag_idx` INT NOT NULL COMMENT '작품 태그 PK',
  PRIMARY KEY (`idx`),
  INDEX `fk_tag_matching_artwork_idx_idx` (`artwork_idx` ASC),
  INDEX `fk_tag_matching_tag_idx_idx` (`tag_idx` ASC),
  CONSTRAINT `fk_artwork_tag_pair_artwork_idx`
    FOREIGN KEY (`artwork_idx`)
    REFERENCES `pickartyou`.`artworks` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artwork_tag_pair_tag_idx`
    FOREIGN KEY (`tag_idx`)
    REFERENCES `pickartyou`.`artwork_tags` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_tags` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_tags` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL COMMENT '장소에 사용할 태그 이름',
  PRIMARY KEY (`idx`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_tag_pair`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_tag_pair` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_tag_pair` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `place_idx` INT NOT NULL COMMENT '장소 PK',
  `tag_idx` INT NOT NULL COMMENT '장소 태그 PK',
  PRIMARY KEY (`idx`),
  INDEX `fk_place_tag_pair_place_idx_idx` (`place_idx` ASC),
  INDEX `fk_place_tag_pair_tag_idx_idx` (`tag_idx` ASC),
  CONSTRAINT `fk_place_tag_pair_place_idx`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_place_tag_pair_tag_idx`
    FOREIGN KEY (`tag_idx`)
    REFERENCES `pickartyou`.`place_tags` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_comments` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_comments` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `user_idx` INT NOT NULL COMMENT '유저 PK',
  `place_idx` INT NOT NULL COMMENT '장소 PK',
  `comment` TEXT NOT NULL COMMENT '장소에 달린 댓글',
  PRIMARY KEY (`idx`),
  INDEX `fk_user_comments_user_idx_idx` (`user_idx` ASC),
  INDEX `fk_place_comments_place_idx_idx` (`place_idx` ASC),
  CONSTRAINT `fk_place_comments_user_idx`
    FOREIGN KEY (`user_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_place_comments_place_idx`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`user_place_picks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`user_place_picks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`user_place_picks` (
  `idx` INT NOT NULL AUTO_INCREMENT,
  `user_idx` INT NOT NULL COMMENT '유저의 PK',
  `place_idx` INT NOT NULL COMMENT '작품의 PK',
  PRIMARY KEY (`idx`),
  INDEX `fk_user_picks_user_idx_idx` (`user_idx` ASC),
  INDEX `fk_user_picks_place_idx0_idx` (`place_idx` ASC),
  CONSTRAINT `fk_user_picks_user_idx0`
    FOREIGN KEY (`user_idx`)
    REFERENCES `pickartyou`.`users` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_picks_place_idx0`
    FOREIGN KEY (`place_idx`)
    REFERENCES `pickartyou`.`places` (`idx`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
