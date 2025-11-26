<?php
// ============================================
// config/env.php - Carregador de Variáveis de Ambiente
// ============================================

/**
 * Carrega variáveis de ambiente do arquivo .env
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentários
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Ignorar linhas vazias
        if (empty(trim($line))) {
            continue;
        }

        // Separar chave e valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remover aspas se existirem
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Definir variável de ambiente se ainda não estiver definida
            if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

/**
 * Obtém uma variável de ambiente
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    
    if ($value === false) {
        return $default;
    }

    // Converter strings booleanas
    if (strtolower($value) === 'true') {
        return true;
    }
    if (strtolower($value) === 'false') {
        return false;
    }

    // Converter strings numéricas
    if (is_numeric($value)) {
        return strpos($value, '.') !== false ? (float) $value : (int) $value;
    }

    return $value;
}

// Carregar arquivo .env
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);

