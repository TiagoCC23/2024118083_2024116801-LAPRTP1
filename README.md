# 2024118083_2024116801-LAPRTP1

![Language](https://img.shields.io/badge/Language-PHP-777BB3.svg)
![Course](https://img.shields.io/badge/Course-LAPRTP1-3399FE.svg)
![University](https://img.shields.io/badge/University-UFP-00764B.svg)

> **Projeto Prático de Desenvolvimento de Software em PHP**
> 
> *Laboratório de Programação Lado Servidor*
> 
> ***FEITO POR: Rayssa Santos e Tiago chousal***

---

# Tema: 
Site de uma biblioteca para fazer reservas de salas de estudo

---

# Descrição do domínio:
Uma biblioteca muito requisitada está tendo problemas com superlotação então contactou **especialistas** para criarem um site onde o utilizador pode entrar na sua conta e solicitar por uma sala de estudo, lá o utilizador consegue **ver e escolher** os horários disponíveis para a reserva. Caso o utilizador não tenha uma conta, poderá **criar uma com a conta da escola ou universidade, ou conta pessoal** para a biblioteca ter uma média de pessoas que vão estudar, trabalhar ou ler. Mas, se o utilizador não quiser criar uma conta, poderá na mesma escolher uma sala de estudo **mas será obrigada a responder um formulário com as suas informações**: nome, email para enviar um lembrete e tipo de uso. Quando o utilizador escolher uma sala de estudo, **deverá especificar a quantidade de pessoas para a biblioteca**, em si, ter em conta a quantidade de pessoas que frequentam essas salas e criar mais no futuro.

---

# Perfis do utilizador:
* **Convidado** – Default
* **Utilizador** registrado
* **Admin** → Bibliotecários das alas

---

# Lista preliminar de entidades:
* **Utilizador** → id, nome, email, password_hash, tipo (Estudante/Normal/Adm), ala_atribuída (nullable, só para Adm)
* **SalaDeEstudo** → id, nome/numero, ala, capacidade_máxima
* **Horario** → id, hora_início, hora_fim, dia
* **Reserva** → id, sala_id, horario_id, utilizador_id, quantidade_pessoas, data, estado (confirmada/cancelada)
* **ReservaConvidado** → id, reserva_id, nome, email, tipo_de_uso e quantidade_pessoas
* **Ala** → id, nome, piso

---

# Requisitos Funcionais:
* Utilizador/Convidado: pode consultar salas disponíveis por ala, capacidade e horário
* Utilizador/Convidado: pode criar uma reserva, escolhendo sala, horário e quantidade de pessoas
* Utilizador registado pode ver e cancelar as suas próprias reservas
* Convidado (sem conta) preenche formulário (nome, email, tipo de uso) para reservar
* Permitir criar e apagar conta (todos que são registrados)
* Adm pode criar, editar e apagar Salas, Horários e Alas
* Adm pode ver e cancelar qualquer reserva (caso a pessoa não apareça)
* Site apresenta planta/fotos da biblioteca por ala/andar

---

# Requisitos não Funcionais:
* **Apenas o perfil Adm pode editar ou apagar** reservas e salas já reservadas por outros; utilizadores comuns só gerem as suas próprias reservas
* **Acessos diferenciados** entre Convidado, Estudante/Normal e Adm, verificados sempre no servidor
* **Proteção** contra **SQL injection** em todas as queries
* Passwords guardadas com **hash** (nunca em texto simples)
* Uso de cookies/sessão para **manter o utilizador autenticado** entre pedidos
* Dados de convidados (nome, email) **tratados com o mesmo cuidado que dados de conta**, apesar de não terem password
