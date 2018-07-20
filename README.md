# Descrição do domínio
Um colaborador é identificado pelo seu nome de utilizador e caraterizado pelo seu nome completo.  
Um colaborador pode ser administrador, que é caraterizado pela sua função (CEO ou financeiro).  
Um colaborador pode pertencer a uma equipa, tendo nesse caso um supervisor.  
Considera-se que colaboradores supervisionados pelo mesmo colaborador pertencem à mesma equipa.  

Uma unidade é identificada pelo seu nome.  
Um colaborador pertence a uma única unidade. Uma unidade tem pelo menos um colaborador.  
Uma unidade tem sempre um único diretor. Um diretor pode apenas dirigir uma unidade de cada vez.  

Um projeto é identificado pelo seu nome.  
Um colaborador pode colaborar em projectos. Um projecto pode ter vários colaboradores.  

Uma atividade é realizada por um colaborador e identificada por um identificador único. É caraterizada pelo seu nome,  
a hora de início e o tempo de duração em horas e minutos.  
Uma atividade pode estar associada a projectos. Um projecto pode ser constituído por atividades.  

Um colaborador pode fazer um requerimento de ausência. Uma ausência é identificada pelo seu início e fim, e não podem haver  
ausências sobrepostas. Um requerimento tem um identificador único e pode estar num de três estados: pendente, aprovado ou rejeitado.  
Uma ausência pode ser pontual (por exemplo, uma consulta) ou um período de férias. Ausências pontuais têm uma categoria e são  acompanhadas de um documento justificativo.  


# Validação dos diagramas de casos de uso
[UML Use Case Diagram](https://www.lucidchart.com/documents/edit/a3c0dfd0-8d7e-418e-a441-6910246505de/0)

Colaborador - Pode criar requerimentos de férias ou reportar ausências, podendo anexar documentos no processo. Pode
				  consultar os seus requerimentos, modificar requerimentos que não tenham sido aceites ou rejeitados ainda, e
				  visualizar o seu mapa de férias (possivelmente de assiduidade em geral) em vista de calendário. Pode eventualmente
				  exportar este mapa para um ficheiro (Excel, PDF?).  
				  Pode eventualmente agendar as suas atividades e reportar a sua realização.

Coordenador - Mesmas operações do colaborador. Adicionalmente:  
				  Pode consultar e exportar os mapas de todos os colaboradores da equipa que supervisiona.  
				  Pode encaminhar um requerimento para o diretor da unidade a que pertence para continuar o processo de
				  aprovação, ou rejeitá-lo no momento.

Diretor - Mesmas operações do coordenador. Adicionalmente:
			  Pode consultar e exportar os mapas das equipas que pertencem à sua unidade.  
			  Pode encaminhar um requerimento para a administração (CEO e Financeiros).

CEO - Mesmas operações do diretor. Adicionalmente:
		  Pode consultar e exportar os mapas de todas as unidades da organização.  
		  Pode aprovar um requerimento após ter sido aprovado por todos os nós da cadeia.

Financeiro - Mesmas operações do CEO, excepto que não pode aceitar ou rejeitar um requerimento.


# Modelo de Dados
[Entity-Relationship Model](https://www.lucidchart.com/documents/edit/a3c0dfd0-8d7e-418e-a441-6910246505de/0)

**Modelo Relacional da BD**



```
colaborador(username, nome, email)
PK: username

supervisiona(supervisor, colaborador)
PK: (supervisor, colaborador)
supervisiona: FK(colaborador.username)
colaborador: FK(colaborador.username)
```

## Autenticação integrada com AD - tabelas adicionais:

Autenticação assegurada pelas tabelas:  
-Colaboradores apenas podem ver entradas que correspondem a si próprios. 

-Coordenadores podem ver as suas entradas e as dos colaboradores que supervisionam.

-Diretores podem ver as suas entradas e as dos colaboradores que pertencem à sua unidade.

-Administradores podem ver todas as entradas, mas apenas o CEO as pode aprovar ou rejeitar.

No início do processo, verificar o nome de utilizador e fazer as queries apropriadas.

## Configurações:

## Tabelas de feriados nacionais:
Tabela extra na base de dados com uma entrada para cada feriado: dia e mês.

## Templates:

## Dados de ausências / férias - codificação:
Por agora apenas uma string (absence/vacation). Sujeito a mudar aquando do desenvolvimento em PHP.

# Estrutura de pastas:
	-AIBILI_WEB_ATTN/
		-index.html/php
		-php/
		-css/
		-js/
		-sql/db



