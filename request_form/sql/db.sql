CREATE TABLE `colaborador` (
 `username` varchar(64) NOT NULL,
 `nome` varchar(64) NOT NULL,
 PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `administrador` (
 `username` varchar(64) NOT NULL,
 `funcao` varchar(64) NOT NULL,
 PRIMARY KEY (`username`),
 CONSTRAINT `fk_username_administrador` FOREIGN KEY (`username`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `atividade` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `colaborador` varchar(64) NOT NULL,
 `descricao` text NOT NULL,
 `inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `duracao` time NOT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_colaborador_atividade` (`colaborador`),
 CONSTRAINT `fk_colaborador_atividade` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


CREATE TABLE `unidade` (
 `nome` varchar(64) NOT NULL,
 `diretor` varchar(64) NOT NULL,
 PRIMARY KEY (`nome`),
 KEY `fk_diretor_unidade` (`diretor`),
 CONSTRAINT `fk_diretor_unidade` FOREIGN KEY (`diretor`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `supervisiona` (
 `colaborador` varchar(64) NOT NULL,
 `supervisor` varchar(64) NOT NULL,
 PRIMARY KEY (`colaborador`,`supervisor`),
 KEY `fk_supervisor_supervisiona` (`supervisor`),
 CONSTRAINT `fk_colaborador_supervisiona` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_supervisor_supervisiona` FOREIGN KEY (`supervisor`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `requerimento` (
 `id` varchar(64) NOT NULL,
 `colaborador` varchar(64) NOT NULL,
 `inicio` datetime NOT NULL,
 `fim` datetime NOT NULL,
 `estado` varchar(64) NOT NULL,
 `observacoes` text,
 PRIMARY KEY (`id`),
 KEY `fk_colaborador_requerimento` (`colaborador`),
 CONSTRAINT `fk_colaborador_requerimento` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `requerimento_ausencia` (
 `id` varchar(64) NOT NULL,
 `url_doc` text,
 PRIMARY KEY (`id`),
 CONSTRAINT `fk_id_requerimento` FOREIGN KEY (`id`) REFERENCES `requerimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `requerimento_ferias` (
 `id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `aprovacoes_necessarias` (
 `id` varchar(64) NOT NULL,
 `username` varchar(64) NOT NULL,
 PRIMARY KEY (`id`,`username`),
 KEY `fk_aprovacoes_username` (`username`),
 CONSTRAINT `fk_aprovacoes_id` FOREIGN KEY (`id`) REFERENCES `requerimento` (`id`),
 CONSTRAINT `fk_aprovacoes_username` FOREIGN KEY (`username`) REFERENCES `colaborador` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `pertence` (
 `colaborador` varchar(64) NOT NULL,
 `unidade` varchar(64) NOT NULL,
 PRIMARY KEY (`colaborador`,`unidade`),
 KEY `fk_unidade_pertence` (`unidade`),
 CONSTRAINT `fk_colaborador_pertence` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_unidade_pertence` FOREIGN KEY (`unidade`) REFERENCES `unidade` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `projeto` (
 `nome` varchar(64) NOT NULL,
 PRIMARY KEY (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `associada` (
 `atividade` int(11) NOT NULL,
 `projeto` varchar(64) NOT NULL,
 PRIMARY KEY (`atividade`,`projeto`),
 KEY `fk_projeto_associada` (`projeto`),
 CONSTRAINT `fk_atividade_associada` FOREIGN KEY (`atividade`) REFERENCES `atividade` (`id`),
 CONSTRAINT `fk_projeto_associada` FOREIGN KEY (`projeto`) REFERENCES `projeto` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `colabora` (
 `colaborador` varchar(64) NOT NULL,
 `projeto` varchar(64) NOT NULL,
 PRIMARY KEY (`colaborador`,`projeto`),
 KEY `fk_colabora_projeto` (`projeto`),
 CONSTRAINT `fk_colabora_colaborador` FOREIGN KEY (`colaborador`) REFERENCES `colaborador` (`username`),
 CONSTRAINT `fk_colabora_projeto` FOREIGN KEY (`projeto`) REFERENCES `projeto` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
