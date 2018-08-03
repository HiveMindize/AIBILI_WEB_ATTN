# Plugins third-party utilizados:
[Date Range Picker](http://www.daterangepicker.com/) por Dan Grossman, MIT License

[FullCalendar](https://fullcalendar.io/) por Adam Shaw, MIT License

[Yasumi](https://azuyalabs.github.io/yasumi/) por AzuyaLabs, MIT License

# Descrição do domínio
Um colaborador é identificado pelo seu nome de utilizador e caraterizado pelo seu nome completo.  
Um colaborador pode ser administrador, que é caraterizado pela sua função (CEO ou financeiro).  
Um colaborador pode pertencer a uma ou várias equipas (ou nenhuma), tendo nesse caso um supervisor para cada equipa.  

Uma unidade é identificada pelo seu nome.  
Um colaborador pertence a uma ou mais unidades. Uma unidade tem pelo menos um colaborador.  
Uma unidade tem sempre um único diretor de cada vez. Um diretor pode apenas dirigir uma unidade de cada vez.  

Um projeto é identificado pelo seu nome.  
Um colaborador pode colaborar em projectos. Um projecto pode ter vários colaboradores.  

Uma atividade é realizada por um colaborador e identificada por um identificador único. É caraterizada pelo seu nome,  
a hora de início e o tempo de duração em horas e minutos.  
Uma atividade pode estar associada a projectos. Um projecto pode ser constituído por atividades.  

Um colaborador pode fazer um requerimento. Um requerimento tem um identificador único e pode estar num de três estados: pendente, aprovado ou rejeitado. Tem também associado um nível hierárquico que
corresponde ao nível hierárquico dos colaboradores que o podem avaliar.  
Os requerimentos avaliam períodos de ausência ou um período de férias. As ausências são  acompanhadas de um documento justificativo.  

# Validação dos diagramas de casos de uso
[UML Use Case Diagram](https://www.lucidchart.com/documents/edit/a3c0dfd0-8d7e-418e-a441-6910246505de/0)

Colaborador - Pode criar requerimentos de férias ou reportar ausências, podendo anexar documentos no processo. Pode
                  consultar os seus requerimentos, modificar requerimentos que não tenham sido aceites ou rejeitados ainda, e
                  visualizar o seu mapa de assiduidade em vista de calendário.

Coordenador - Mesmas operações do colaborador. Adicionalmente:  
                  Pode consultar os mapas de todos os colaboradores da equipa que supervisiona.  
                  Pode encaminhar um requerimento para o diretor da unidade a que pertence para continuar o processo de
                  aprovação, ou rejeitá-lo no momento.

Diretor - Mesmas operações do coordenador. Adicionalmente:
              Pode consultar  os mapas das equipas que pertencem à sua unidade.  
              Pode encaminhar um requerimento para a administração (CEO e Financeiros) após o aprovar.

CEO - Mesmas operações do diretor. Adicionalmente:
          Pode consultar os mapas de todas as unidades da organização.  
          Pode aprovar um requerimento após ter sido aprovado por todos os nós da cadeia.

Financeiro - Mesmas operações do CEO, exceto que não pode avaliar um requerimento.


# Modelo de Dados
[Entity-Relationship Model](https://www.lucidchart.com/documents/edit/a3c0dfd0-8d7e-418e-a441-6910246505de/0)

**Modelo Relacional da BD**

```
colaborador(username, nome)
PK: username

administrador(username, função)
PK: username
username: FK(colaborador)

requerimento(id, nivel, colaborador, inicio, fim, estado, observacoes)
PK: id
colaborador: FK(colaborador.username)


unidade: (nome, diretor)
PK: nome
diretor: FK(colaborador.username)

pertence(colaborador, unidade)
PK: (colaborador, unidade)
colaborador: FK(colaborador.username)
unidade: FK(unidade.nome)

aprovacoes_necessarias(id, username)
PK: (id, username)
id: FK(requerimento)
username: FK(colaborador)

atividade(id, colaborador, descricao, inicio, duracao)
PK: id 
colaborador: FK(colaborador.username)

projeto(nome)
PK: nome

associada(atividade, projeto)
PK: (projeto, atividade)
atividade: FK(atividade.nome)
projeto: FK(projeto.nome)

colabora(colaborador, projeto)
PK: (colaborador, projeto)
colaborador: FK(colaborador.username)
projeto: FK(projeto.descricao)

requerimento_ausencia(id, url_doc)
PK: id
id: FK(requerimento.id)

requerimento_ferias(id)
PK: id
id: FK(requerimento.id)

supervisiona(colaborador, supervisor)
PK: (colaborador, supervisor)
colaborador: FK(colaborador.username)
supervisor: FK(colaborador.username)
```

## Autenticação integrada com AD - tabelas adicionais:

Autenticação assegurada pelas tabelas:  
-Colaboradores apenas podem ver entradas que correspondem a si próprios. 

-Coordenadores podem ver as suas entradas e as dos colaboradores que supervisionam.

-Diretores podem ver as suas entradas e as dos colaboradores que pertencem à sua unidade.

-Administradores podem ver todas as entradas, mas apenas o CEO as pode aprovar ou rejeitar.

No início do processo, verifica o nome de utilizador e fazer as queries apropriadas.

## Configurações:
Variável cujo valor muda consoante o nível hierárquico do colaborador. Gerir acesso a partir desse princípio.

## Tabelas de feriados nacionais:
Lista de feriados nacionais disponibilizada pela biblioteca PHP open-source [Yasumi](https://azuyalabs.github.io/yasumi/) por AzuyaLabs.

## Dados de ausências / férias - codificação:
Na base de dados, ausências pontuais e férias são guardadas em tabelas separadas.

# Estrutura de pastas:
    -AIBILI_WEB_ATTN/
        -docs
        -index.php
        -php/
          -attendance_map.php
          -header.php
          -lib.php
          -my_requests.php
          -pending_requests.php
          -request_form.php
          -setup.php
        -js/
          -calendar.js
          -form.js
        -sql/
          -db.sql