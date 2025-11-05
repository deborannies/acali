DROP TABLE IF EXISTS `arquivos`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `users`;

-- Criação da tabela de usuários
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `encrypted_password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criação da tabela de projetos
CREATE TABLE `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criação da tabela de arquivos
CREATE TABLE `arquivos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    
    -- A Chave Estrangeira (FK)
    `project_id` INT NOT NULL, 
    
    `path_arquivo` VARCHAR(512) NOT NULL,    -- 'uploads/proj_1_abc.pdf'
    `nome_original` VARCHAR(255) NOT NULL,  -- 'meu_artigo.pdf'
    `mime_type` VARCHAR(100) NOT NULL,      -- 'application/pdf'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`project_id`)
        REFERENCES `projects`(`id`)
        ON DELETE CASCADE -- Se deletar o 'project', deleta este 'arquivo' do BANCO.
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;