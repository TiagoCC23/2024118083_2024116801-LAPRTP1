# 📚 Reserva de Salas — UFP
 
![PHP](https://img.shields.io/badge/PHP-8.5-777BB3.svg)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20.svg)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-4-38BDF8.svg)
![Vite](https://img.shields.io/badge/Vite-8-646CFF.svg)
![Tests](https://img.shields.io/badge/Testes-123-2EA44F.svg)
![Course](https://img.shields.io/badge/UC-LAPRTP1-3399FE.svg)
![University](https://img.shields.io/badge/UFP-Universidade_Fernando_Pessoa-00764B.svg)
 
> **Projeto Prático de Desenvolvimento de Software em PHP**
> *Laboratório de Programação Lado Servidor (LAPRTP1)* — Universidade Fernando Pessoa (UFP)
> **Autores:** Rayssa Santos (`2024118083`) e Tiago Chousal (`2024116801`)
 
---
 
## 📑 Índice
 
1. [Sobre o Projeto](#-sobre-o-projeto)
2. [Funcionalidades](#-funcionalidades)
3. [Regras de Negócio](#-regras-de-negócio)
4. [Tecnologias](#️-tecnologias)
5. [Perfis de Utilizador](#-perfis-de-utilizador)
6. [Requisitos Funcionais](#-requisitos-funcionais)
7. [Requisitos Não Funcionais](#-requisitos-não-funcionais)
8. [Arquitetura e Estrutura](#️-arquitetura-e-estrutura)
9. [Modelo E-R](#️-modelo-entidade-relacionamento-e-r)
10. [Dicionário de Dados](#-dicionário-de-dados)
11. [Rotas Principais](#️-rotas-principais)
12. [Instalação e Execução](#-instalação-e-execução-local)
13. [Importação de Ocupações (NONIO)](#-importação-de-ocupações-nonio)
14. [Testes](#-testes)
---
 
## 🎯 Sobre o Projeto
 
Portal web da **Universidade Fernando Pessoa** para **consulta e reserva de salas de estudo**.
 
Os espaços de estudo da UFP estão muitas vezes sobrelotados. O portal permite **reservar salas com antecedência** e dá à universidade **dados reais de ocupação**. Os utilizadores autenticam-se com a **conta institucional Google (`@ufp.edu.pt`)**, escolhem sala, data e horário, e indicam **quantas pessoas** vão usar o espaço e **para quê**:
 
- Preparação para exames;
- Trabalhos de grupo;
- Leitura / estudo individual.
Com esta informação, a UFP pode **analisar a taxa de ocupação**, **otimizar os espaços existentes** e **justificar a criação de novas salas**.
 
As aulas e outras ocupações oficiais das salas também podem ser **importadas a partir dos mapas de ocupação exportados do NONIO** (ficheiros CSV).
 
---
 
## ✨ Funcionalidades
 
| Área | O que faz |
| :--- | :--- |
| 🔐 **Autenticação** | Login com Google restrito ao domínio `@ufp.edu.pt` (Laravel Socialite). No primeiro acesso, o utilizador completa o registo com nome e palavra-passe. Também é possível entrar com email e palavra-passe. |
| 🏫 **Salas e edifícios** | Listagem e detalhe de edifícios e salas (capacidade, piso, tipo, disponibilidade). |
| 🔎 **Disponibilidade** | Pesquisa de salas livres para uma data e um número mínimo de lugares. |
| 📅 **Reservas** | Criar, editar, ver e cancelar reservas de 1 a 3 blocos horários seguidos. Pesquisa por tipo de uso ou sala, com paginação. |
| ✅ **Aprovação** | Os pedidos ficam `pendente` até o administrador os **confirmar**, **rejeitar** ou **cancelar**. |
| ✉️ **Emails** | Email automático quando a reserva é criada e quando é aprovada. |
| ⭐ **Avaliações** | Depois de uma reserva confirmada, o estudante pode dar uma nota de 1 a 5 com comentário opcional. O admin pode apagar comentários ofensivos. |
| 📥 **Importação NONIO** | O admin carrega um ou mais CSV de ocupação (via web ou Artisan), que são guardados na tabela `ocupacoes`. |
| 🛠️ **Administração** | CRUD de edifícios, salas e horários, reservado a administradores. |
| 🗑️ **Conta** | O utilizador pode eliminar a sua conta (*soft delete*). |
 
---
 
## 📏 Regras de Negócio
 
- Uma reserva tem entre **1 e 3 blocos horários**, que têm de ser **consecutivos**.
- Os blocos horários são de **1 hora, entre as 08:00 e as 18:00**.
- **Não há reservas ao domingo** nem em datas passadas.
- A `quantidade_pessoas` não pode passar a **capacidade máxima da sala**.
- Não é possível reservar um bloco que já tenha uma reserva **pendente** ou **confirmada** para a mesma sala no mesmo dia.
- O estudante só pode **editar ou cancelar** as suas reservas enquanto estão **pendentes**.
- Só se pode **avaliar** uma reserva **confirmada**, que já tenha acontecido, e **uma única vez**.
- Tipos de uso aceites: `AulaPratica`, `AulaTeorica`, `Estudo`, `Defesa`, `Palestra`, `Leitura`, `SalaDeEstudo`, `Laboratorio` e `Outro`.
Estas regras estão no `ReservaController` (validação) e na `ReservaPolicy` (autorização).
 
---
 
## 🛠️ Tecnologias
 
| Camada | Tecnologia |
| :--- | :--- |
| Back-end | PHP 8.5, Laravel 13 |
| Autenticação | Laravel Socialite (Google OAuth 2.0) |
| Front-end | Blade, Tailwind CSS 4, Vite 8 |
| Base de dados | SQLite por defeito (também compatível com MySQL/PostgreSQL), gerida por *migrations* do Laravel |
| Email | Laravel Mail (`ReservaCriada`, `ReservaAprovada`) |
| Testes | PHPUnit / Pest, com SQLite em memória |
| Qualidade | Laravel Pint |
 
---
 
## 👥 Perfis de Utilizador
 
| Perfil | `tipo_user` | Descrição |
| :--- | :--- | :--- |
| **Estudante** (default) | `estudante` | Conta institucional UFP. Consulta salas, faz e gere as **suas** reservas e avaliações. |
| **Administrador** | `admin` | Funcionário que gere edifícios, salas, horários e reservas, e importa ocupações. Os admins são criados pelo *seeder*, não pelo registo. |
 
---
 
## ✅ Requisitos Funcionais
 
### Consulta e Reservas (Estudante)
- **RF1.** Consultar as salas disponíveis por edifício, capacidade e horário.
- **RF2.** Criar uma reserva, escolhendo sala, data, um ou mais horários seguidos, quantidade de pessoas e tipo de uso.
- **RF3.** Ver as plantas e fotos de cada edifício.
- **RF4.** Iniciar sessão (conta institucional).
- **RF5.** Ver as suas próprias reservas.
- **RF6.** Cancelar as suas próprias reservas.
- **RF13.** Avaliar uma sala de estudo com uma nota e um comentário opcional.
- **RF14.** Filtrar e ordenar a lista de salas de estudo disponíveis.
### Administração (Admin)
- **RF7.** Criar, editar e apagar salas.
- **RF8.** Criar, editar e apagar horários.
- **RF9.** Criar, editar e apagar edifícios (e as respetivas imagens).
- **RF10.** Ver e cancelar qualquer reserva (por exemplo, em caso de não comparência).
- **RF11.** Apagar comentários ofensivos.
- **RF12.** Aprovar ou rejeitar as reservas efetuadas pelos utilizadores.
- **RF15.** Importar mapas de ocupação de salas exportados do NONIO (CSV).
---
 
## 🔒 Requisitos Não Funcionais
 
- **RNF1.** Apenas o Admin pode editar ou apagar reservas de outros utilizadores. O utilizador comum gere apenas as suas (`ReservaPolicy`).
- **RNF2.** A administração está separada da área do utilizador, e o servidor verifica o acesso em cada pedido (middleware `admin` → `CheckAdmin`).
- **RNF3.** Proteção contra *SQL Injection* e *XSS*, com o ORM Eloquent / *prepared statements* e o *escape* automático das *views* Blade. Os formulários usam proteção CSRF.
- **RNF4.** As palavras-passe são guardadas com *hash* seguro (*cast* `hashed`, bcrypt) e nunca em texto simples.
- **RNF5.** A sessão é gerida em base de dados e regenerada no login e no logout, para evitar *session fixation*.
- **RNF6.** Quando uma conta é eliminada, os dados pessoais são anonimizados e o histórico de reservas é mantido para fins estatísticos (*soft delete* em `eliminado_em`).
- **RNF7.** Só são aceites contas Google do domínio `@ufp.edu.pt`.
---
 
## 🏗️ Arquitetura e Estrutura
 
O projeto segue o padrão **MVC** do Laravel:
 
```text
app/
├── Console/Commands/    # ImportarOcupacoes (php artisan ocupacoes:importar)
├── Enums/               # TipoUtilizador, EstadoReserva, Disponibilidade
├── Http/
│   ├── Controllers/     # Auth, Edificio, Sala, Horario, Reserva, Avaliacao
│   └── Middleware/      # CheckAdmin (admin), GoogleAuth (registo pós-Google)
├── Mail/                # ReservaCriada, ReservaAprovada
├── Models/              # Utilizador, Edificio, Sala, Horario, Reserva, Reserva_Horario, Avaliacao, Ocupacao
├── Policies/            # ReservaPolicy (quem pode ver/editar/aprovar/avaliar)
└── Services/            # Importador (parser dos CSV do NONIO)
apresentacao/            # Modelo E-R (draw.io), protocolo, mockups e CSV reais do NONIO
database/
├── migrations/          # Esquema da base de dados
├── seeders/             # Dados iniciais (+ seeders/data/*.csv)
└── factories/
resources/views/         # Blade: auth, edificios, salas, horarios, reservas, avaliacoes, emails
routes/web.php           # Rotas web
tests/
├── Feature/             # Controllers, importação, migrations, seeders
└── Unit/                # Enums, middleware, models, policy
```
 
---
 
## 🗄️ Modelo Entidade-Relacionamento (E-R)
 
![Modelo E-R](apresentacao/LAPR_projeto.drawio.png)
 
**Relações principais:**
 
- Um **Edifício** tem várias **Salas**.
- Uma **Sala** tem várias **Reservas** e várias **Ocupações** (importadas do NONIO).
- Um **Utilizador** faz várias **Reservas**.
- Uma **Reserva** ocupa 1 a 3 **Horários** (N:M através de `reserva_horario`).
- Uma **Reserva** pode ter no máximo uma **Avaliação** (1:1).
- Um **Admin** administra várias **Salas** (N:M através de `administra`).
---
 
## 📊 Dicionário de Dados
 
### `edificios`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador do edifício |
| `nome` | varchar(100) | | Nome do edifício (único, validado na aplicação) |
| `sigla` | varchar(10) | UQ | Sigla (ex.: `UFP`, `ESS`) |
| `imagem` | varchar | | Caminho/URL da imagem ou planta (opcional) |
 
### `utilizadores`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador do utilizador |
| `nome` | varchar(100) | | Nome |
| `email` | varchar | UQ | Email institucional |
| `google_id` | varchar | UQ | ID da conta Google (opcional) |
| `verificado_em` | timestamp | | Data de verificação do email |
| `password_hash` | varchar | | Palavra-passe com *hash* (bcrypt) |
| `tipo_user` | enum | | `estudante` ou `admin` |
| `edificio_id` | bigint | FK | Edifício associado (opcional) |
| `criado_em` | timestamp | | Data de criação da conta |
| `eliminado_em` | timestamp | | Data de eliminação (*soft delete*) |
 
### `salas`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador da sala |
| `nome_numero` | varchar(50) | UQ* | Nome ou número da sala (*único por edifício*) |
| `capacidade_maxima` | smallint | | Lotação máxima |
| `tipo_sala` | varchar | | `Exame`, `Trabalho`, `SalaDeAula` ou `Outro` |
| `piso` | int | | Piso |
| `unidade_organica` | varchar(20) | | Unidade orgânica (opcional) |
| `disponibilidade` | enum | | `disponivel`, `pendente` ou `ocupado` |
| `edificio_id` | bigint | FK | Edifício onde fica a sala |
 
### `horarios`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador do bloco |
| `hora_inicio` | time | UQ* | Hora de início |
| `hora_fim` | time | UQ* | Hora de fim (*o par início/fim é único*) |
 
### `reservas`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador da reserva |
| `data` | date | | Data da reserva |
| `quantidade_pessoas` | int | | Número de ocupantes |
| `tipo_de_uso` | varchar(100) | | Finalidade (ex.: `Estudo`, `Defesa`, `Leitura`) |
| `estado` | enum | | `pendente`, `confirmada`, `rejeitada` ou `cancelada` |
| `sala_id` | bigint | FK | Sala reservada |
| `utilizador_id` | bigint | FK | Quem fez a reserva |
| `criado_em` | timestamp | | Data do pedido |
| `eliminado_em` | timestamp | | Data de eliminação |
 
### `reserva_horario` *(N:M)*
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador |
| `reserva_id` | bigint | FK | Reserva |
| `horario_id` | bigint | FK | Bloco horário (*o par reserva/horário é único*) |
 
### `avaliacoes`
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador |
| `nota` | tinyint | | Nota de 1 a 5 |
| `comentario` | text | | Comentário (opcional) |
| `reserva_id` | bigint | FK, UQ | Reserva avaliada (1 avaliação por reserva) |
 
### `administra` *(N:M)*
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador |
| `utilizador_id` | bigint | FK | Administrador |
| `sala_id` | bigint | FK | Sala administrada |
 
### `ocupacoes` *(importadas do NONIO)*
| Atributo | Tipo | Chave | Descrição |
| :--- | :--- | :---: | :--- |
| `id` | bigint | PK | Identificador |
| `data_inicio` / `data_fim` | datetime | | Intervalo da ocupação |
| `tipo_ocupacao` | varchar(50) | | Ex.: `Aula`, `Exame`, `Seminário`, `Defesa` |
| `codigo_disciplina` | varchar(20) | | Código da UC (opcional) |
| `nome_disciplina` | varchar(150) | | Nome da UC (opcional) |
| `ciclo_disciplina` | varchar(100) | | Ciclo de estudos (opcional) |
| `descricao_reserva` | varchar | | Descrição (opcional) |
| `estado` | varchar(30) | | Estado no NONIO |
| `pessoa_responsavel` | varchar | | Docente/responsável (opcional) |
| `observacoes` | text | | Observações (opcional) |
| `sala_id` | bigint | FK | Sala ocupada |
 
---
 
## 🗺️ Rotas Principais
 
| Método | Rota | Acesso | Descrição |
| :--- | :--- | :---: | :--- |
| GET/POST | `/login` | Visitante | Login com email e palavra-passe |
| GET | `/auth/google` → `/auth/google/callback` | Visitante | Login com Google (`@ufp.edu.pt`) |
| GET/POST | `/registo` | Pós-Google | Completar o registo |
| POST | `/logout` | Autenticado | Terminar sessão |
| POST | `/conta/eliminar` | Autenticado | Eliminar a conta |
| GET | `/home` | Autenticado | Página inicial |
| GET/POST | `/salas/disponibilidade` | Autenticado | Pesquisar salas livres |
| GET | `/edificios`, `/salas`, `/horarios` | Autenticado | Consulta (index/show) |
| * | `/reservas` | Autenticado | CRUD de reservas (com *policy*) |
| GET | `/minhas-reservas` | Autenticado | Reservas do utilizador |
| PATCH | `/reservas/{reserva}/estado` | Autenticado | Aprovar/rejeitar/cancelar |
| * | `/avaliacoes` | Autenticado | CRUD de avaliações |
| GET | `/minhas-avaliacoes` | Autenticado | Avaliações do utilizador |
| * | `/edificios`, `/salas`, `/horarios` | **Admin** | Criar, editar, apagar |
| POST | `/reservas/importar.csv` | **Admin** | Importar CSV do NONIO |
 
> Para ver a lista completa: `php artisan route:list`
 
---
 
## 🚀 Instalação e Execução Local
 
### Pré-requisitos
- **PHP 8.5+** e **Composer**
- **Node.js** e **npm**
- **SQLite** (por defeito), MySQL ou PostgreSQL
- *(Opcional)* Credenciais **Google OAuth**, para o login com conta institucional
### Passos
 
1. **Clonar o repositório**
```bash
git clone https://github.com/TiagoCC23/2024118083_2024116801-LAPRTP1.git
cd 2024118083_2024116801-LAPRTP1
```
 
2. **Instalar dependências**
```bash
composer install
npm install
```
 
3. **Configurar o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```
   Com SQLite, cria o ficheiro da base de dados:
```bash
touch database/database.sqlite
```
 
4. **Configurar o Google OAuth** (no `.env`)
```env
GOOGLE_CLIENT_ID=o-teu-client-id
GOOGLE_CLIENT_SECRET=o-teu-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```
   As credenciais criam-se na [Google Cloud Console](https://console.cloud.google.com/apis/credentials) (*OAuth client ID → Web application*).
 
5. **Criar as tabelas e os dados iniciais**
```bash
php artisan migrate --seed
```
 
6. **Ligação simbólica do *storage*** (para as imagens)
```bash
php artisan storage:link
```
 
7. **Arrancar a aplicação**
```bash
# Terminal 1 — Laravel
php artisan serve
 
# Terminal 2 — Vite (assets)
npm run dev
```
 
A aplicação fica disponível em **http://127.0.0.1:8000**.
 
> 💡 Atalho: `composer run setup` faz os passos 2, 3 e 5 (sem *seeders*) e compila os assets.
 
### 👤 Contas de teste (seeder)
 
| Perfil | Email | Palavra-passe |
| :--- | :--- | :--- |
| Admin | `admin@ufp.test` | `admin` |
| Estudante | `estudante1@ufp.test` | `estudante1` |
| Estudante | `estudante2@ufp.test` | `estudante2` |
 
Podes mudar estes valores no `.env` com `SEED_ADMIN_EMAIL`, `SEED_ADMIN_PASSWORD`, `SEED_ESTUDANTE1_EMAIL`, etc.
 
O *seeder* também cria os edifícios **UFP** e **ESS**, salas de exemplo, blocos horários das 08:00 às 18:00, reservas, avaliações e ocupações (a partir de `database/seeders/data/*.csv`).
 
---
 
## 📥 Importação de Ocupações (NONIO)
 
O serviço `App\Services\Importador` lê os **Mapas de Ocupação de Espaço** exportados do NONIO:
 
- separador `;`;
- conversão automática de Windows-1252 para UTF-8;
- identifica a sala pela linha `Designação do Espaço` (ex.: `Sala 302`);
- datas no formato `dd-mm-aaaa hh:mm`;
- ignora duplicados (`firstOrCreate` por sala + início + fim).
**Pela linha de comandos:**
```bash
php artisan ocupacoes:importar "apresentacao/data/NONIO_MapaOcupacoesEspaco_201_....csv"
```
 
**Pela interface web:** o admin pode carregar vários CSV de uma vez (`POST /reservas/importar.csv`).
 
Há exemplos reais em [`apresentacao/data/`](apresentacao/data/) e um exemplo simplificado em [`database/seeders/data/mock_sala_302.csv`](database/seeders/data/mock_sala_302.csv).
 
---
 
## 🧪 Testes
 
O projeto tem **123 testes** automáticos, que correm numa base de dados SQLite em memória.
 
```bash
php artisan test
# ou
composer test
```
 
| Suite | O que cobre |
| :--- | :--- |
| `tests/Unit` | Enums, middleware (`CheckAdmin`, `GoogleAuth`), models e `ReservaPolicy` |
| `tests/Feature` | Controllers (Auth, Edifício, Sala, Horário, Reserva, Avaliação), importação de CSV, migrations e seeders |
 
---
 
## 📄 Licença
 
Projeto académico desenvolvido no âmbito da UC de LAPRTP1 da Universidade Fernando Pessoa.
 
