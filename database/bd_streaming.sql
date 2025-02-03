CREATE SCHEMA `bd_streaming` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE bd_streaming;

-- CREACIÓN TABLA ROLES
CREATE TABLE `bd_streaming`.`tbl_roles` (
    `id_rol` INT NOT NULL AUTO_INCREMENT,
    `nombre_rol` VARCHAR(30) NOT NULL UNIQUE,
    PRIMARY KEY (`id_rol`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA USUARIOS
CREATE TABLE `bd_streaming`.`tbl_usuarios` (
    `id_u` INT NOT NULL AUTO_INCREMENT,
    `username_u` VARCHAR(30) NOT NULL,
    `email_u` VARCHAR(100) NOT NULL UNIQUE,
    `password_u` VARCHAR(255) NOT NULL,
    `activo_u` BOOLEAN DEFAULT FALSE,
    `id_rol` INT,
    PRIMARY KEY (`id_u`),
    CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `bd_streaming`.`tbl_roles` (`id_rol`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA PELÍCULAS
CREATE TABLE `bd_streaming`.`tbl_peliculas` (
    `id_peli` INT NOT NULL AUTO_INCREMENT,
    `titulo_peli` VARCHAR(50) NOT NULL,
    `descripcion_peli` TEXT,
    `poster_peli` VARCHAR(255),
    `fecha_estreno_peli` DATE,
    `director_peli` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id_peli`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA CATEGORÍAS
CREATE TABLE `bd_streaming`.`tbl_categorias` (
    `id_cat` INT NOT NULL AUTO_INCREMENT,
    `nombre_cat` VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (`id_cat`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- RELACIÓN N:M ENTRE PELÍCULAS Y CATEGORÍAS
CREATE TABLE `bd_streaming`.`tbl_pelicula_categoria` (
    `id_peli_cat` INT NOT NULL AUTO_INCREMENT,
    `id_peli` INT NOT NULL,
    `id_cat` INT NOT NULL,
    PRIMARY KEY (`id_peli_cat`),
    CONSTRAINT `fk_pelicula_categoria_peli` FOREIGN KEY (`id_peli`) REFERENCES `bd_streaming`.`tbl_peliculas` (`id_peli`) ON DELETE CASCADE,
    CONSTRAINT `fk_pelicula_categoria_cat` FOREIGN KEY (`id_cat`) REFERENCES `bd_streaming`.`tbl_categorias` (`id_cat`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA LIKES
CREATE TABLE `bd_streaming`.`tbl_likes` (
    `id_likes` INT NOT NULL AUTO_INCREMENT,
    `id_u` INT NOT NULL,
    `id_peli` INT NOT NULL,
    PRIMARY KEY (`id_likes`),
    CONSTRAINT `fk_likes_usuario` FOREIGN KEY (`id_u`) REFERENCES `bd_streaming`.`tbl_usuarios` (`id_u`) ON DELETE CASCADE,
    CONSTRAINT `fk_likes_pelicula` FOREIGN KEY (`id_peli`) REFERENCES `bd_streaming`.`tbl_peliculas` (`id_peli`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA SOLICITUDES DE REGISTRO
CREATE TABLE `bd_streaming`.`tbl_solicitudes_registro` (
    `id_soli` INT NOT NULL AUTO_INCREMENT,
    `id_u` INT NOT NULL,
    `estado` ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    PRIMARY KEY (`id_soli`),
    CONSTRAINT `fk_solicitudes_usuario` FOREIGN KEY (`id_u`) REFERENCES `bd_streaming`.`tbl_usuarios` (`id_u`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- INSERTS DE ROLES
INSERT INTO `bd_streaming`.`tbl_roles` (`nombre_rol`) VALUES ('administrador');
INSERT INTO `bd_streaming`.`tbl_roles` (`nombre_rol`) VALUES ('usuario');

-- INSERTS DE USUARIOS
INSERT INTO `bd_streaming`.`tbl_usuarios` (`username_u`, `email_u`, `password_u`, `activo_u`, `id_rol`) VALUES ('admin', 'admin@gmail.com', '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS', TRUE, 1);