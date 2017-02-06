SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema pickartyou
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema pickartyou
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `pickartyou` DEFAULT CHARACTER SET utf8mb4 ;
USE `pickartyou` ;

-- -----------------------------------------------------
-- Table `pickartyou`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`users` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(50) NOT NULL COMMENT '유저 이메일주소 / 로그인 이메일?',
  `password` VARCHAR(40) NOT NULL COMMENT '패스워드',
  `type` TINYINT UNSIGNED NOT NULL COMMENT '창작자/공간소유자 구분 여부',
  `name` VARCHAR(45) NOT NULL COMMENT '유저 이름',
  `profile_image` VARCHAR(45) NOT NULL COMMENT '유저 프로필 이미지 파일 이름',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artworks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artworks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artworks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '작가의 user PK',
  `type` TINYINT UNSIGNED NOT NULL COMMENT '작품 구분 - 일러스트/회화/설치 작품 등 구분',
  `status` TINYINT UNSIGNED NOT NULL COMMENT '작품의 상태 공간구함/전시중/공간구하지 않음',
  `title` VARCHAR(100) NOT NULL COMMENT '작품 제목',
  `description` TEXT NULL COMMENT '작품 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '작품의 대표 이미지',
  `for_sale` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '판매여부 설정',
  `use_comment` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '댓글 사용 여부',
  `upload_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '작품 업로드 시간',
  PRIMARY KEY (`id`),
  INDEX `fk_artworks_owner_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_artworks_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`places`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`places` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`places` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '장소 소유자의 user PK',
  `status` TINYINT UNSIGNED NOT NULL COMMENT '작품 모집중 / 모집완료',
  `name` VARCHAR(45) NOT NULL COMMENT '장소 이름',
  `description` TEXT NULL COMMENT '장소 설명',
  `image` VARCHAR(45) NOT NULL COMMENT '장소 대표 이미지',
  `address` VARCHAR(250) NOT NULL COMMENT '장소의 실제 주소',
  `artwork_count` SMALLINT UNSIGNED NOT NULL COMMENT '장소에서 모집하는 작품 수',
  `exhibit_period` VARCHAR(10) NOT NULL COMMENT '작품 전시 기간',
  `is_free_exhibit` TINYINT(1) NOT NULL COMMENT '무료 전시 / 비용 지불 여부',
  `use_comment` TINYINT(1) NOT NULL COMMENT '댓글 사용 여부',
  PRIMARY KEY (`id`),
  INDEX `fk_places_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_places_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_images` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `artwork_id` INT NOT NULL COMMENT '작품의 PK',
  `image` VARCHAR(45) NOT NULL COMMENT '작품 이미지 파일명',
  PRIMARY KEY (`id`),
  INDEX `fk_artwork_images_artwork_id_idx` (`artwork_id` ASC),
  CONSTRAINT `fk_artwork_images_artwork_id`
    FOREIGN KEY (`artwork_id`)
    REFERENCES `pickartyou`.`artworks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_images` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `place_id` INT NOT NULL COMMENT '장소 PK',
  `image` VARCHAR(45) NOT NULL COMMENT '장소 이미지 파일 이름',
  PRIMARY KEY (`id`),
  INDEX `fk_place_images_place_id_idx` (`place_id` ASC),
  CONSTRAINT `fk_place_images_place_id`
    FOREIGN KEY (`place_id`)
    REFERENCES `pickartyou`.`places` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`user_artwork_picks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`user_artwork_picks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`user_artwork_picks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '유저의 PK',
  `artwork_id` INT NOT NULL COMMENT '작품의 PK',
  PRIMARY KEY (`id`),
  INDEX `fk_user_artwork_picks_idx` (`user_id` ASC),
  INDEX `fk_user_artwork_picks_artwork_id_idx` (`artwork_id` ASC),
  CONSTRAINT `fk_user_artwork_picks_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_artwork_picks_artwork_id`
    FOREIGN KEY (`artwork_id`)
    REFERENCES `pickartyou`.`artworks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_comments` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '유저 PK',
  `artwork_id` INT NOT NULL COMMENT '작품 PK',
  `comment` TEXT NOT NULL COMMENT '작품에 달린 댓글',
  PRIMARY KEY (`id`),
  INDEX `fk_artwork_comments_user_id_idx` (`user_id` ASC),
  INDEX `fk_artwork_comments_artwork_id_idx` (`artwork_id` ASC),
  CONSTRAINT `fk_artwork_comments_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artwork_comments_artwork_id`
    FOREIGN KEY (`artwork_id`)
    REFERENCES `pickartyou`.`artworks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`exhibitions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`exhibitions` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`exhibitions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `artwork_id` INT NOT NULL COMMENT '유저의 PK',
  `place_id` INT NOT NULL COMMENT '장소의 PK',
  `start_date` DATETIME NOT NULL COMMENT '전시 시작 날짜',
  `end_date` DATETIME NOT NULL COMMENT '전시 종료 날짜',
  PRIMARY KEY (`id`),
  INDEX `fk_exhibitions_artwork_id_idx` (`artwork_id` ASC),
  INDEX `fk_exhibitions_place_id_idx` (`place_id` ASC),
  CONSTRAINT `fk_exhibitions_artwork_id`
    FOREIGN KEY (`artwork_id`)
    REFERENCES `pickartyou`.`artworks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exhibitions_place_id`
    FOREIGN KEY (`place_id`)
    REFERENCES `pickartyou`.`places` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_tags` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL COMMENT '작품에 사용될 태그명',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`artwork_tag_pair`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`artwork_tag_pair` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`artwork_tag_pair` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `artwork_id` INT NOT NULL COMMENT '작품 PK',
  `tag_id` INT NOT NULL COMMENT '작품 태그 PK',
  PRIMARY KEY (`id`),
  INDEX `fk_artwork_tag_pair_artwork_id_idx` (`artwork_id` ASC),
  INDEX `fk_artwork_tag_pair_tag_id_idx` (`tag_id` ASC),
  CONSTRAINT `fk_artwork_tag_pair_artwork_id`
    FOREIGN KEY (`artwork_id`)
    REFERENCES `pickartyou`.`artworks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artwork_tag_pair_tag_id`
    FOREIGN KEY (`tag_id`)
    REFERENCES `pickartyou`.`artwork_tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_tags` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL COMMENT '장소에 사용할 태그 이름',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_tag_pair`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_tag_pair` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_tag_pair` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `place_id` INT NOT NULL COMMENT '장소 PK',
  `tag_id` INT NOT NULL COMMENT '장소 태그 PK',
  PRIMARY KEY (`id`),
  INDEX `fk_place_tag_pair_place_id_idx` (`place_id` ASC),
  INDEX `fk_place_tag_pair_tag_id_idx` (`tag_id` ASC),
  CONSTRAINT `fk_place_tag_pair_place_id`
    FOREIGN KEY (`place_id`)
    REFERENCES `pickartyou`.`places` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_place_tag_pair_tag_id`
    FOREIGN KEY (`tag_id`)
    REFERENCES `pickartyou`.`place_tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`place_comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`place_comments` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`place_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '유저 PK',
  `place_id` INT NOT NULL COMMENT '장소 PK',
  `comment` TEXT NOT NULL COMMENT '장소에 달린 댓글',
  PRIMARY KEY (`id`),
  INDEX `fk_place_comments_user_id_idx` (`user_id` ASC),
  INDEX `fk_place_comments_place_id_idx` (`place_id` ASC),
  CONSTRAINT `fk_place_comments_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_place_comments_place_id`
    FOREIGN KEY (`place_id`)
    REFERENCES `pickartyou`.`places` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pickartyou`.`user_place_picks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pickartyou`.`user_place_picks` ;

CREATE TABLE IF NOT EXISTS `pickartyou`.`user_place_picks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL COMMENT '유저의 PK',
  `place_id` INT NOT NULL COMMENT '작품의 PK',
  PRIMARY KEY (`id`),
  INDEX `fk_user_place_picks_user_id_idx` (`user_id` ASC),
  INDEX `fk_user_place_picks_place_id_idx` (`place_id` ASC),
  CONSTRAINT `fk_user_place_picks_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `pickartyou`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_place_picks_place_id`
    FOREIGN KEY (`place_id`)
    REFERENCES `pickartyou`.`places` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
