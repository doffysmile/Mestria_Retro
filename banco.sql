CREATE DATABASE IF NOT EXISTS mestria;


USE mestria;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(60) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email),
    UNIQUE KEY uq_usuarios_cpf (cpf)
);

CREATE TABLE IF NOT EXISTS aulas (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(60) NOT NULL,
    horario TIME NOT NULL,
    assunto VARCHAR(45) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_aula DATE NOT NULL,
    capacidade INT NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS matriculas (
    id_usuario INT NOT NULL,
    id_aula INT NOT NULL,
    data_matricula DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativa', 'cancelada') NOT NULL DEFAULT 'ativa',
    PRIMARY KEY (id_usuario, id_aula),
    KEY idx_matricula_aula (id_aula),
    CONSTRAINT fk_matricula_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_matricula_aula
        FOREIGN KEY (id_aula) REFERENCES aulas (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);
