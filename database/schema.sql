DROP TABLE IF EXISTS `arquivos`;
DROP TABLE IF EXISTS `project_user`;
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
    `title` VARCHAR(255) NOT NULL,
    `status` ENUM('open', 'finished') NOT NULL DEFAULT 'open',
    `user_id` INT NOT NULL,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criação da tabela de arquivos
CREATE TABLE `arquivos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL, 
    `path_arquivo` VARCHAR(512) NOT NULL,    
    `nome_original` VARCHAR(255) NOT NULL,  
    `mime_type` VARCHAR(100) NOT NULL,      
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`project_id`)
        REFERENCES `projects`(`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela Pivô para Projetos <-> Usuários (NxN)
CREATE TABLE `project_user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    UNIQUE KEY `unique_project_user` (`project_id`, `user_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;