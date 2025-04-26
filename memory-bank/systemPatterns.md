# Padrões do Sistema

## Arquitetura
- Arquitetura monolítica PHP
- Separação de responsabilidades por arquivos
- Integração direta com banco de dados via PDO
- Processamento server-side com PHP

## Fluxo de Pagamento
1. Recebimento de dados do veículo via GET
2. Consulta de chave PIX no banco
3. Geração de QR Code via API externa
4. Exibição de dados e QR Code para pagamento
5. Funcionalidade de cópia de chave PIX

## Padrões de Código
### Backend (PHP)
- Uso de PDO para conexão segura
- Tratamento de exceções com try/catch
- Validação de dados de entrada
- Sanitização de output
- Sessions para controle de estado

### Frontend
- Layout responsivo com Bootstrap
- Componentes reutilizáveis
- Validações client-side
- Feedback visual para usuário
- Copiar para clipboard

## Padrões de Interface
- Cores institucionais
- Ícones consistentes (Font Awesome)
- Feedback visual de ações
- Mensagens de sucesso/erro
- Layout adaptativo

## Padrões de Segurança
- Prepared Statements
- Validação de sessão
- Sanitização de dados
- Tratamento de erros
- Logs de operações críticas

## Padrões de Dados
### Entrada
- Validação de RENAVAM
- Validação de CPF
- Formatação de valores monetários
- Validação de datas

### Saída
- Formatação monetária BR
- Máscaras para documentos
- Formatação de datas BR
- Mensagens padronizadas 