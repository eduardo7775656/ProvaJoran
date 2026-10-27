-- Banco de dados do Sistema de Assistência Técnica
-- Execute este script no phpMyAdmin conectado ao MySQL/MariaDB na porta 3307.
CREATE DATABASE IF NOT EXISTS saep_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saep_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ordens_servico;
DROP TABLE IF EXISTS equipamentos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NULL UNIQUE,
  telefone VARCHAR(30) NULL,
  cpf_cifrado TEXT NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_clientes_nome (nome),
  INDEX idx_clientes_telefone (telefone)
) ENGINE=InnoDB;

CREATE TABLE equipamentos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  marca VARCHAR(80) NULL,
  modelo VARCHAR(100) NULL,
  numero_serie VARCHAR(100) NULL UNIQUE,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_equip_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE ordens_servico (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  equipamento_id INT UNSIGNED NOT NULL,
  descricao TEXT NOT NULL,
  status ENUM('Aberta','Em diagnóstico','Aguardando aprovação','Em reparo','Concluída','Cancelada') NOT NULL DEFAULT 'Aberta',
  data_abertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_conclusao DATETIME NULL,
  CONSTRAINT fk_os_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_os_equip FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_os_abertura (data_abertura),
  INDEX idx_os_status (status)
) ENGINE=InnoDB;

-- Senha inicial para os três usuários: password
-- Hash bcrypt válido de exemplo; altere as senhas em ambiente real.
INSERT INTO usuarios (nome,email,senha_hash,ativo) VALUES
('Administrador','admin@assistencia.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1),
('Mariana Costa','mariana@assistencia.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1),
('Rafael Lima','rafael@assistencia.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1);

-- CPFs de demonstração criptografados por PHP no cadastro.
-- Registros iniciais usam texto explicitamente identificado como dado fictício;
-- o aplicativo não tenta descriptografá-los nem os exibe na listagem.
INSERT INTO clientes (nome,email,telefone,cpf_cifrado) VALUES
('Ana Souza','ana.souza@example.test','81999990001','DEMO_NAO_E_CPF'),
('Bruno Lima','bruno.lima@example.test','81999990002','DEMO_NAO_E_CPF'),
('Carla Mendes','carla.mendes@example.test','81999990003','DEMO_NAO_E_CPF');

INSERT INTO equipamentos (cliente_id,tipo,marca,modelo,numero_serie) VALUES
(1,'Notebook','Dell','Inspiron 15','DEMO-SERIE-001'),
(2,'Impressora','HP','LaserJet Pro','DEMO-SERIE-002'),
(3,'Desktop','Lenovo','ThinkCentre','DEMO-SERIE-003');

INSERT INTO ordens_servico (cliente_id,equipamento_id,descricao,status,data_abertura) VALUES
(1,1,'Notebook não liga; verificar fonte e placa.','Aberta',NOW() - INTERVAL 3 DAY),
(2,2,'Impressora apresenta falha na alimentação de papel.','Em diagnóstico',NOW() - INTERVAL 2 DAY),
(3,3,'Computador apresenta lentidão durante a inicialização.','Em reparo',NOW() - INTERVAL 1 DAY);
