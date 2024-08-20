<?php
/**
 * Parâmetros de configuração.
 * 
 * Os valores preenchidos são exemplos. É necessário substituí-los pelos
 * valores reais.
 */

 
 
#Host do banco de dados
define('BD_HOST','45.232.39.215');
#Nome do database
define('BD_DATABASE','femarh_bdg');
#usuário do banco de dados
define('BD_USUARIO','pnla_user');
#senha do banco de dados
define('BD_SENHA','pnlalic@2024');

#Configurações das views. Colocar o caminho completo.
# ex: define('PNLA_LIC','[MMA].[dbo].[PNLA_LIC]');
define('PNLA_LIC','pnla_lic');
define('PNLA_DOC','[PNLA_DOC]');
define('PNLA_MULTA','[PNLA_MULTA]');

#Constante que define qual a codificação de caracteres no banco de dados
define('CODIFICACAO_BD','WINDOWS-1252');