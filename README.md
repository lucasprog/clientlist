
# Projeto Listagem de Clientes

Listagem de dados armazenados no banco de dados usando Docker, Lumen(PHP) e VueJS.


## Rodar Projeto

Para subir os containers rode o comando abaixo: 
```bash
  docker-compose up -d
```

## Observação!
Portas que estarão sendo rodadas:
```bash
    Front-End: https://localhost:8081
    Back-End: https://localhost:8080
```

## Criar banco de dados
Primeira você precisará entrar no container para rodar as migrations:
```bash
  docker exec -it app-back-end bash
```
Após ter entrado no container, rode o comando abaixo para criar as migrations:
```bash
php artisan migrate
```

## IMPORTANTE

Rode o comando abaixo para criar o login e senha admin e popular o banco de dados com alguns dados fakers (para teste), :
```
php artisan db:seed
```

Após o comando ter rodado, a tabela de clientes estará populada com os dados de clientes fake, e o login admin estará sendo criado:
```
Email: admin@teste.com
Senha: 123123
```