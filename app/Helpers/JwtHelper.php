<?php

namespace App\Helpers;

/**
 * Helper para decodificar y validar JWT (sin verificar firma por ahora)
 */
class JwtHelper
{
    /**
     * Decodifica un JWT y retorna el payload
     * 
     * @param string $token Token JWT
     * @return array|null Payload decodificado o null si falla
     */
    public static function decode(string $token): ?array
    {
        try {
            // Un JWT tiene 3 partes separadas por punto: header.payload.signature
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                error_log("JWT inválido: no tiene 3 partes");
                return null;
            }
            
            [$headerB64, $payloadB64, $signatureB64] = $parts;
            
            // Decodificar payload (es base64url, no base64 estándar)
            $payload = self::base64UrlDecode($payloadB64);
            
            if (!$payload) {
                error_log("JWT: No se pudo decodificar el payload");
                return null;
            }
            
            $data = json_decode($payload, true);
            
            if (!$data) {
                error_log("JWT: Payload no es JSON válido");
                return null;
            }
            
            // Validar expiración si existe
            if (isset($data['exp'])) {
                $exp = (int)$data['exp'];
                if (time() > $exp) {
                    error_log("JWT: Token expirado. Exp: {$exp}, Now: " . time());
                    return null;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            error_log("JWT: Error al decodificar - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verifica la firma del JWT usando una clave pública RSA
     * 
     * @param string $token Token JWT completo
     * @param string $publicKeyPath Ruta al archivo de clave pública
     * @return bool True si la firma es válida
     */
    public static function verify(string $token, string $publicKeyPath): bool
    {
        try {
            if (!file_exists($publicKeyPath)) {
                error_log("JWT: Archivo de clave pública no encontrado: {$publicKeyPath}");
                return false;
            }
            
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }
            
            [$headerB64, $payloadB64, $signatureB64] = $parts;
            
            // Decodificar header para verificar algoritmo
            $header = json_decode(self::base64UrlDecode($headerB64), true);
            if (!$header || !isset($header['alg'])) {
                error_log("JWT: Header inválido");
                return false;
            }
            
            // Verificar que sea RS256
            if ($header['alg'] !== 'RS256') {
                error_log("JWT: Algoritmo no soportado: " . $header['alg']);
                return false;
            }
            
            // Leer clave pública
            $publicKey = file_get_contents($publicKeyPath);
            if (!$publicKey) {
                error_log("JWT: No se pudo leer la clave pública");
                return false;
            }
            
            // Preparar datos a verificar
            $dataToVerify = $headerB64 . '.' . $payloadB64;
            $signature = self::base64UrlDecode($signatureB64);
            
            // Verificar firma con OpenSSL
            $result = openssl_verify($dataToVerify, $signature, $publicKey, OPENSSL_ALGO_SHA256);
            
            if ($result === 1) {
                error_log("JWT: Firma verificada correctamente");
                return true;
            } elseif ($result === 0) {
                error_log("JWT: Firma inválida");
                return false;
            } else {
                error_log("JWT: Error al verificar firma - " . openssl_error_string());
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("JWT: Error al verificar - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Decodifica una cadena base64url
     * 
     * @param string $input Cadena en base64url
     * @return string|false Cadena decodificada o false si falla
     */
    private static function base64UrlDecode(string $input)
    {
        // Convertir de base64url a base64 estándar
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        
        $base64 = strtr($input, '-_', '+/');
        return base64_decode($base64);
    }
    
    /**
     * Extrae el email del payload JWT
     * 
     * @param array $payload Payload decodificado
     * @return string|null Email o null si no existe
     */
    public static function getEmail(array $payload): ?string
    {
        return $payload['email'] ?? null;
    }
    
    /**
     * Extrae información adicional del usuario del payload JWT
     * 
     * @param array $payload Payload decodificado
     * @return array Datos del usuario
     */
    public static function getUserData(array $payload): array
    {
        return [
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? null,
            'namePaternal' => $payload['namePaternal'] ?? null,
            'nameMaternal' => $payload['nameMaternal'] ?? null,
            'shortName' => $payload['shortName'] ?? null,
            'avatarUrl' => $payload['avatarUrl'] ?? null,
            'role' => $payload['role'] ?? null,
            'atomId' => $payload['atomId'] ?? null,
            'uid' => $payload['uid'] ?? null,
        ];
    }
}

