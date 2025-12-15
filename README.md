# local_userfeedback

Plugin para Moodle destinado à coleta rápida de avaliações dos usuários 
por meio de um widget flutuante exibido de forma discreta na interface. 
O objetivo é registrar a experiência dos participantes com agilidade, 
sem interromper a navegação e seguindo rigorosamente os padrões 
oficiais da plataforma.

## Visão Geral

O widget apresenta uma interface simples onde o usuário pode registrar uma 
nota entre 1 e 5 e adicionar uma observação opcional. As informações são 
enviadas via AJAX, processadas por um serviço externo e armazenadas em 
tabela própria. O plugin também fornece páginas administrativas, relatórios, 
eventos, observers e uma tarefa agendada para notificações.

O plugin utiliza microanimações em CSS, aplicadas durante a seleção 
da nota. Cada avaliação possui um efeito visual próprio criado com keyframes, 
pseudo-elementos e transições leves, garantindo resposta imediata ao usuário 
e mantendo compatibilidade com o Moodle 4.5 em desktop e mobile.

A implementação segue o padrão **local plugin** do Moodle e foi construída 
de acordo com os requisitos do processo avaliativo.

---

## Funcionalidades

- Exibição de widget flutuante para envio de feedback.
- Envio assíncrono via **AMD + AJAX** integrado à API `external_api`.
- Armazenamento dos registros na tabela `local_userfeedback`.
- Ocultação automática do widget após envio.
- Ocultação do widget em páginas de atividades.
- Páginas administrativas para gestão dos envios.
- Relatórios contendo número total e média geral das avaliações.
- Evento próprio acionado ao registrar feedback.
- Observer responsável por inserir dados em log.
- Tarefa agendada que executa notificações periódicas.
- Suporte completo a permissões configuráveis.

---

## Requisitos

- Moodle 4.5 ou superior.
- Banco de dados compatível com a versão do Moodle utilizada.
- Perfis de usuário ajustados com as capabilities corretas.

---

## Instalação

1. Copiar a pasta `userfeedback` para:

```
local/
```

2. Acessar `/admin/index.php` para que o Moodle:
   - identifique o plugin,
   - execute a instalação,
   - crie automaticamente as tabelas definidas em `install.xml`.

3. Configurar as capabilities utilizadas pelo plugin:

- `local/userfeedback:submit` — permite enviar feedback.
- `local/userfeedback:manage` — acesso à página administrativa.
- `local/userfeedback:viewreports` — acesso aos relatórios.

---

## Ciclo de Execução

A seguir, o fluxo completo da operação do plugin:

1. **Exibição do widget**  
   - O `lib.php` integra o widget à interface utilizando callbacks do Moodle.  
   - O widget só aparece caso o usuário possua capacidade `submit` e ainda não tenha enviado feedback.

2. **Envio de dados**  
   - O arquivo `amd/src/submit.js` captura as interações do usuário.  
   - Os dados são enviados via AJAX para o serviço externo `submit_feedback`.

3. **Processamento no servidor**  
   - `classes/external/submit_feedback.php` valida os dados, registra o feedback e dispara evento.

4. **Disparo de evento**  
   - O plugin emite `feedback_submitted`, definido em `classes/event/feedback_submitted.php`.

5. **Observer do evento**  
   - `classes/observers.php` registra ações no log conforme definição de `db/events.php`.

6. **Tarefa agendada**  
   - `classes/task/send_notif.php` é executada conforme cron e envia notificações a usuários com permissão de gerenciamento.

7. **Exibição administrativa**  
   - `manage.php` exibe listagem completa.  
   - `reports.php` exibe totais e média das avaliações.

---

## Estrutura do Plugin

```
local/userfeedback
│
├── amd/
│   ├── src/submit.js
│   └── build/submit.min.js
│
├── classes/
│   ├── event/
│   │   └── feedback_submitted.php
│   ├── external/
│   │   └── submit_feedback.php
│   ├── form/
│   │   └── edit_form.php
│   ├── privacy/
│   │   └── provider.php
│   ├── observers.php
│   └── task/
│       └── send_notif.php
│
├── db/
│   ├── access.php
│   ├── events.php
│   ├── services.php
│   ├── tasks.php
│   └── install.xml
│
├── lang/
│   ├── en/local_userfeedback.php
│   └── pt_br/local_userfeedback.php
│
├── pix/
│   └── icon.svg
│
├── templates/
│   └── feedback.mustache
│
├── styles.css
├── lib.php
├── manage.php
├── edit.php
├── delete.php
├── reports.php
└── version.php
```

---

## Documentação das Capabilities

O plugin utiliza três capabilities principais:

| Capability | Função |
|------------|--------|
| `local/userfeedback:submit` | Permite ao usuário visualizar o widget e enviar feedback. |
| `local/userfeedback:manage` | Libera acesso à página de gerenciamento administrativo. |
| `local/userfeedback:viewreports` | Permite visualizar relatórios agregados. |

Essas capabilities podem ser atribuídas a papéis via Administração do Moodle.

---

## Considerações Arquiteturais e Pontos de Avaliação

Este README incorpora ajustes solicitados com base em avaliação técnica:

### Estrutura do Plugin  
- Segue o padrão Moodle com organização correta de classes, AMD, templates, banco e assets.

### Banco de Dados  
- `install.xml` define duas tabelas: a tabela principal de feedback (local_userfeedback), 
contendo chave primária, timestamps e relação com usuário, e uma tabela adicional de log 
(local_userfeedback_log) utilizada pelos observers.

### Permissões  
- `access.php` define capabilities claras e bem distribuídas (submit, manage, report).

### Widget  
- Baseado em Mustache + CSS, carregado via `lib.php`.  
- Possível refinamento visual adicional.

### AJAX / AMD / Webservice  
- Fluxo completo atendendo normas do Moodle.

### CRUD e Relatórios  
- Disponíveis, com funcionamento adequado, porém com layout simples.

### Eventos  
- Evento implementado corretamente e registrado conforme documentação.

### Scheduled Task  
- Execução funcional, com espaço para refinamento da lógica de notificação.

### Navegação (hooks)  
- Presentes, podendo ser modularizados para melhor manutenibilidade.

### Ocultação do widget  
- Implementada e funcional em todos os cenários principais.

---

## Licença

Distribuído sob a licença **GNU GPL v3 ou posterior**. Consulte o arquivo de licença incluído para mais detalhes.

