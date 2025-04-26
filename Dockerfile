# Usar uma imagem base oficial do PHP com Apache
# Escolha a versão do PHP apropriada (ex: 8.1, 8.2, 7.4)
FROM php:8.1-apache

# Instalar extensões PHP necessárias
# Atualizar lista de pacotes e instalar dependências
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
  && docker-php-ext-install pdo pdo_mysql zip curl

# Habilitar mod_rewrite do Apache (opcional, mas comum)
RUN a2enmod rewrite

# Definir o diretório de trabalho no container
WORKDIR /var/www/html

# Remover o conteúdo padrão do Apache
RUN rm -rf /var/www/html/*

# Copiar o código da aplicação (do diretório htdocs) para o diretório web do container
COPY ipvamg.rf.gd/htdocs/ /var/www/html/

# Ajustar permissões (se necessário, pode precisar de ajustes dependendo do usuário do Apache)
# RUN chown -R www-data:www-data /var/www/html
# RUN chmod -R 755 /var/www/html

# Expor a porta 80 (padrão do Apache)
EXPOSE 80

# O comando padrão (CMD) já é fornecido pela imagem base (apache2-foreground) 