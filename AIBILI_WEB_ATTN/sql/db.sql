CREATE TABLE `colaborador` (
 `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `nome` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `administrador` (
 `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `funcao` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`username`),
 CONSTRAINT `fk_administrador_username` FOREIGN KEY (`username`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `requerimento` (
 `id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `nivel` int(11) NOT NULL,
 `colaborador` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `inicio` datetime NOT NULL,
 `fim` datetime NOT NULL,
 `estado` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `observacoes` text CHARACTER SET latin1 COLLATE latin1_general_cs,
 PRIMARY KEY (`id`),
 KEY `fk_requerimento_colaborador` (`colaborador`),
 CONSTRAINT `fk_requerimento_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `unidade` (
 `nome` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `diretor` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`nome`),
 KEY `fk_unidade_diretor` (`diretor`),
 CONSTRAINT `fk_unidade_diretor` FOREIGN KEY (`diretor`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `pertence` (
 `colaborador` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `unidade` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`colaborador`,`unidade`),
 KEY `fk_pertence_unidade` (`unidade`),
 CONSTRAINT `fk_pertence_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_pertence_unidade` FOREIGN KEY (`unidade`) REFERENCES `unidade` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `aprovacoes_necessarias` (
 `id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`id`,`username`),
 KEY `fk_aprovacoes_username` (`username`),
 CONSTRAINT `fk_aprovacoes_id` FOREIGN KEY (`id`) REFERENCES `requerimento` (`id`),
 CONSTRAINT `fk_aprovacoes_username` FOREIGN KEY (`username`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `atividade` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `colaborador` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `descricao` text CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `duracao` time NOT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_atividade_colaborador` (`colaborador`),
 CONSTRAINT `fk_atividade_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `projeto` (
 `nome` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `associada` (
 `atividade` int(11) NOT NULL,
 `projeto` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`atividade`,`projeto`),
 KEY `fk_associada_projeto` (`projeto`),
 CONSTRAINT `fk_associada_atividade` FOREIGN KEY (`atividade`) REFERENCES `atividade` (`id`),
 CONSTRAINT `fk_associada_projeto` FOREIGN KEY (`projeto`) REFERENCES `projeto` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `colabora` (
 `colaborador` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `projeto` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`colaborador`,`projeto`),
 KEY `fk_colabora_projeto` (`projeto`),
 CONSTRAINT `fk_colabora_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_colabora_projeto` FOREIGN KEY (`projeto`) REFERENCES `projeto` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `requerimento_ausencia` (
 `id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `url_doc` text CHARACTER SET latin1 COLLATE latin1_general_cs,
 PRIMARY KEY (`id`),
 CONSTRAINT `fk_ausencia_id` FOREIGN KEY (`id`) REFERENCES `requerimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `requerimento_ferias` (
 `id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`id`),
 CONSTRAINT `fk_ferias_id` FOREIGN KEY (`id`) REFERENCES `requerimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `supervisiona` (
 `colaborador` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 `supervisor` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
 PRIMARY KEY (`colaborador`,`supervisor`),
 KEY `fk_supervisiona_supervisor` (`supervisor`),
 CONSTRAINT `fk_superivisiona_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_supervisiona_supervisor` FOREIGN KEY (`supervisor`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
