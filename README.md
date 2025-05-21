`markdown
# AIBNet – Conexão IA “Lily” para Monitoramento de Redes Sociais

Este projeto é um **backend** em Laravel chamado **AIBNet**, que integra uma IA (via OpenAI GPT) ao sistema de monitoramento, captura, processamento e geração de relatórios sobre redes sociais. Também disponibiliza uma **interface gráfica de chat** para que a IA, chamada **Lily**, possa se comunicar diretamente com o usuário.

---

## 📋 Funcionalidades

- **Integração OpenAI GPT**  
  - Envia prompts e recebe respostas da IA **Lily** para análises, insights e conversas.
- **Monitoramento de Redes Sociais**  
  - Captura posts, comentários, curtidas e métricas de engajamento de várias plataformas.
- **Processamento e Análise de Dados**  
  - Limpeza, classificação de sentimento, extração de temas e detecção de tendências.
- **Geração de Relatórios**  
  - Exportação em PDF/CSV com gráficos, tabelas e métricas consolidadas.
- **Interface de Chat**  
  - Chat em tempo real para interação usuário ↔ **Lily**, com histórico e recursos de formatação.
- **Scheduler & Queues**  
  - Tarefas agendadas para coleta periódica.

---

## ⚙ Pré-requisitos

- PHP >= 8.1  
- Composer  
- Node.js & NPM (ou Yarn)  
- MySQL / PostgreSQL / SQLite  
- Extensões PHP: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `gd`  
- Conta na OpenAI com chave de API válida  

---

## 🚀 Instalação

1. **Clone o repositório**  
   bash
   git clone https://github.com/MarcosPCB/aibnet_monitor.git
   cd aibnet
`

2. **Instale dependências PHP**

   bash
   composer install
   

3. **Instale dependências JavaScript**

   bash
   npm install
   # ou
   yarn install
   

4. **Copie o arquivo de ambiente**

   bash
   cp .env.example .env
   

---

## 🔧 Configuração

1. **Variáveis de ambiente**
   No arquivo `.env`, ajuste:

   ini
   APP_NAME="AIBNet"
   APP_ENV=local
   APP_KEY=          # será gerada abaixo
   APP_DEBUG=true
   APP_URL=http://localhost

   LOG_CHANNEL=stack

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nome_do_banco
   DB_USERNAME=usuario
   DB_PASSWORD=senha

   BROADCAST_DRIVER=log
   CACHE_DRIVER=file
   QUEUE_CONNECTION=database
   SESSION_DRIVER=file
   SESSION_LIFETIME=120

   MAIL_FROM_ADDRESS="admin@aibnet.online"
   MAIL_FROM_NAME="${APP_NAME}"

   LLM_TOKEN=sk-XXXXXX
   

2. **Gerar a chave da aplicação**

   bash
   php artisan key:generate
   

3. **Configurar o cron para scheduler**
   No crontab do servidor (crie com `crontab -e`):

   cron
   * * * * * cd /caminho/para/aibnet && php artisan schedule:run >> /dev/null 2>&1
   

---

## 🗄 Banco de Dados & Migrations

1. **Executar migrations**

   bash
   php artisan migrate
   

---

## 🖥 Execução Local

* **Iniciar servidor**

  bash
  php artisan serve
  

  Acesse em `http://localhost:8000`.

* **Compilar assets**

  bash
  npm run dev
  # ou
  yarn dev
  

  Para produção:

  bash
  npm run build
  # ou
  yarn build
  

---

## 💬 Interface de Chat

Para conversar com a IA **Lily**, abra no navegador:


http://localhost:8000/


---

## 🛠 Comandos Úteis

* `php artisan config:cache`
* `php artisan route:cache`
* `php artisan schedule:run`
* `php artisan queue:restart`

---

## ⚖ Licença

Este projeto está licenciado sob a **MIT License**. Veja [LICENSE](LICENSE) para detalhes.

---

> Desenvolvido com ♥ por ItsMarcos


```