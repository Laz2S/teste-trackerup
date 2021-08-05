# teste-typescript
Projeto com intuito de testar Typescript dentro do Vue.js

Backend - Symfony (php)

Frontend - Quasar (Vue.js) - Typescript

Banco - MySQL

Banco: Para o teste foi criado duas entidades, uma chamada category (id, name), e outra chamada product (id, name, identifier, category[FK]).
Backend: Foi utilizado de softdelete na implementação do crud, fazendo validação de [FK] para não ser possível deletar um registro que já esteja sendo usado por outro. Fora também implementado paginação nas rotas de Get All.
Frontend: Foi desenvolvido duas telas para o gerenciamento das entidades category e product, ambas com listagem paginada e um modal de formulário para adição e edição de registros. Através da listagem é possível deletar um registro.
