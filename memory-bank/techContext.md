# Contexto Técnico

## Stack Tecnológico
- Backend: PHP 7+
- Banco de Dados: MySQL (Host: sql108.infinityfree.com)
- Frontend: HTML5, CSS3, JavaScript
- Framework CSS: Bootstrap 4.5.2
- Ícones: Font Awesome 5.15.4

## Integrações
### API PIX (Zippify)
- Endpoint: https://api.zippify.com.br/api/public/v1/transactions
- Método: POST
- Autenticação: api_token via query string
- Formato Requisição: application/json
- Formato Resposta: JSON com string PIX em `pix.pix_qr_code`
- Credenciais:
    - Token: `q0s9BAe4jtddZ3MKV8qv8Nc9k5pkvOpnSgMX7GmnYDSoaUXJj1grjbT7n0uA`
    - Offer Hash: `ugjfdma1i8`
    - Product Hash: `uyrnqqr9f8`

## Configurações de Banco de Dados
- Host: sql108.infinityfree.com
- Database: if0_37765204_ipva
- Usuário: if0_37765204
- Conexão: PDO com tratamento de erros

## Estrutura de Dados
### Tabela usuarios
- id (PK)
- tipo_chave (PIX)
- chave_pix
- [outros campos...]

## Segurança
- Sessões PHP ativas
- Prepared Statements para queries
- Validação de dados de entrada
- Sanitização de output HTML
- Tratamento de erros PDO

## Dependências Frontend
- Bootstrap CDN
- Font Awesome CDN
- QRCode.js CDN (para renderizar QR Code da Zippify)
- JavaScript vanilla para interações
- Responsividade mobile-first 