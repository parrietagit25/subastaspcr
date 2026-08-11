-- Esquema mínimo + parches para que el panel de subasta arranque.
-- Ejecutar contra la BD `subastas` (phpMyAdmin o mysql CLI).
-- No borra datos existentes.

CREATE DATABASE IF NOT EXISTS `subastas`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `subastas`;

-- Staff / login
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `edad` int DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_user` varchar(10) NOT NULL,
  `stat` int NOT NULL COMMENT '1=on; 2=off; 3=eliminado',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Solicitudes KYC (estructura base del backup)
CREATE TABLE IF NOT EXISTS `cc_subastas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_persona` varchar(50) DEFAULT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `pn_recibo_servicios` varchar(150) DEFAULT NULL,
  `pn_ficha` varchar(150) DEFAULT NULL,
  `pn_carta_ex` varchar(150) DEFAULT NULL,
  `pn_contrato` varchar(150) DEFAULT NULL,
  `pn_cc` varchar(150) DEFAULT NULL,
  `pn_cedula` varchar(150) DEFAULT NULL,
  `pni_cedula` varchar(150) DEFAULT NULL,
  `pni_aviso_op` varchar(150) DEFAULT NULL,
  `pni_servicios` varchar(150) DEFAULT NULL,
  `pni_referencia` varchar(150) DEFAULT NULL,
  `pni_cc` varchar(150) DEFAULT NULL,
  `pni_carta_ex` varchar(150) DEFAULT NULL,
  `actividad_economica_pni` varchar(150) DEFAULT NULL,
  `pj_registro_publico` varchar(150) DEFAULT NULL,
  `pj_aviso_ope` varchar(150) DEFAULT NULL,
  `pj_cedula_pass` varchar(150) DEFAULT NULL,
  `pj_servicios` varchar(150) DEFAULT NULL,
  `pj_cc` varchar(150) DEFAULT NULL,
  `pj_carta_exo` varchar(150) DEFAULT NULL,
  `pj_contrato` varchar(150) DEFAULT NULL,
  `pni_contrato` varchar(150) DEFAULT NULL,
  `stat` int NOT NULL COMMENT '1=pendiente 2=aprobado 3=eliminado 4=supervisor',
  `codigo` varchar(20) NOT NULL DEFAULT '0',
  `fecha_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Adjuntos administrativos
CREATE TABLE IF NOT EXISTS `cc_adjuntos_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `adjunto` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `fecha_log` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Parches si la tabla ya existía sin columnas nuevas
SET @db := DATABASE();

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `cc_subastas` ADD COLUMN `pn_cedula` varchar(150) DEFAULT NULL',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'cc_subastas' AND COLUMN_NAME = 'pn_cedula'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `cc_subastas` ADD COLUMN `actividad_economica_pni` varchar(150) DEFAULT NULL',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'cc_subastas' AND COLUMN_NAME = 'actividad_economica_pni'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Usuario admin de desarrollo
-- email: admin@local.test  password: admin123
INSERT INTO `usuarios` (`nombre`, `email`, `edad`, `password`, `tipo_user`, `stat`)
SELECT 'Admin Local', 'admin@local.test', NULL,
       '$2y$10$LHBpk7S6IUzQYteUsPa7oujDuFNtLskhYDbjtXueOT.nzBeb5fjgm',
       'admin', 1
WHERE NOT EXISTS (
  SELECT 1 FROM `usuarios` WHERE email = 'admin@local.test'
);