CREATE DATABASE gestor_vendas;

-- DROP TABLE cliente;
CREATE TABLE cliente (
	id serial NOT NULL,
	nome varchar(100) NOT NULL,
 	email varchar(50) NOT NULL,
	cpf varchar(12) NOT NULL,
	
	CONSTRAINT pk_cliente_id PRIMARY KEY (id)
);

-- DROP TABLE produto;
CREATE TABLE produto (
	id serial NOT NULL,
	nome varchar(100) NOT NULL,
	preco numeric(10,2) NOT NULL,
 	quantidade integer NOT NULL,
	
	CONSTRAINT pk_produto_id PRIMARY KEY (id)
);

-- DROP TABLE pedido;
CREATE TABLE pedido (
	id serial NOT NULL,
	id_cliente integer NOT NULL,
	valor numeric(10,2) NOT NULL,
	
	CONSTRAINT pk_pedido_id PRIMARY KEY (id),
	CONSTRAINT fk_pedido_x_cliente FOREIGN KEY (id_cliente)
		REFERENCES cliente (id) MATCH SIMPLE
      	ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE pedido_produto (
	id_pedido integer NOT NULL,
	id_produto integer NOT NULL,
 	quantidade integer NOT NULL,

 	CONSTRAINT fk_pedido_produto_x_pedido FOREIGN KEY (id_pedido)
		REFERENCES pedido (id) MATCH SIMPLE
      	ON UPDATE NO ACTION ON DELETE NO ACTION,
  	CONSTRAINT fk_pedido_produto_x_produto FOREIGN KEY (id_produto)
		REFERENCES produto (id) MATCH SIMPLE
      	ON UPDATE NO ACTION ON DELETE NO ACTION
);

