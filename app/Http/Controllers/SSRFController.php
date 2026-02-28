<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SSRFController extends Controller
{
    public function show(Request $request)
    {
        // Mostramos la vista con resultados si existen
        return view('ssrf', [
            'vuln_result'   => session('vuln_result'),
            'secure_result' => session('secure_result'),
            'error'         => session('error'),
        ]);
    }


    // Toma una URL del usuario y el servidor hace la petición SIN validación. */
    public function fetchVulnerable(Request $request)
    {
        $url = trim($request->input('url'));

        try {
            $client = new Client([
                'timeout' => 5,
                // allow_redirects=true puede empeorar SSRF; aquí lo dejamos por defecto (vulnerable)
            ]);

            $resp = $client->get($url);
            $body = (string) $resp->getBody();

            return redirect('/ssrf')->with('vuln_result', $body);
        } catch (\Exception $e) {
            return redirect('/ssrf')->with('error', 'Error (vulnerable): ' . $e->getMessage());
        }
    }

    /**
     * SSRF MITIGADO:
     * - Solo permite http/https
     * - Bloquea IPs privadas/loopback/reservadas (incluye 127.0.0.1)
     * - (Opcional) allowlist de dominios
     * - Bloquea redirects
     */
    public function fetchSecure(Request $request)
    {
        $url = trim($request->input('url'));

        try {
            $this->validateUrlOrFail($url);

            $client = new Client([
                'timeout' => 5,
                'allow_redirects' => false, // Importante: evita pivot a destinos internos via 30x
            ]);

            $resp = $client->get($url);
            $body = (string) $resp->getBody();

            return redirect('/ssrf')->with('secure_result', $body);
        } catch (\Exception $e) {
            return redirect('/ssrf')->with('error', 'Bloqueado (secure): ' . $e->getMessage());
        }
    }

    /**
     * "Servicio interno" simulado:
     * Solo debería ser accesible desde localhost.
     * Si entras desde el navegador, lo normal es que falle (dependiendo de tu IP).
     * Pero cuando el servidor se llama a sí mismo (127.0.0.1), sí lo obtiene.
     */
    public function internalMetadata(Request $request)
    {
        // En local, muchas veces será 127.0.0.1; si usas red, puede variar.
        // Para el demo, restringimos a loopback.
        $ip = $request->ip();
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            abort(403, 'Forbidden: internal only');
        }

        // "Secreto" simulado
        return response()->json([
            'service' => 'internal-metadata',
            'secret'  => 'FAKE-SECRET-12345',
            'note'    => 'Este endpoint simula un recurso interno no expuesto públicamente.',
        ]);
    }

    /**
     * Validación defensiva de URL.
     */
    private function validateUrlOrFail(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('URL inválida');
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host   = $parts['host'] ?? '';

        // 1) Solo http/https
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \Exception('Esquema no permitido (solo http/https)');
        }

        if ($host === '') {
            throw new \Exception('Host vacío');
        }

        // (Opcional) allowlist: descomenta si quieres “solo dominios permitidos”
        // $allowedHosts = ['example.com', 'api.example.com'];
        // if (!in_array(strtolower($host), $allowedHosts, true)) {
        //     throw new \Exception('Host no permitido por allowlist');
        // }

        // 2) Resolver DNS y bloquear IPs internas/reservadas
        $ips = $this->resolveHostToIps($host);
        if (empty($ips)) {
            throw new \Exception('No se pudo resolver el host');
        }

        foreach ($ips as $ip) {
            if (!$this->isPublicIp($ip)) {
                throw new \Exception("Destino no permitido (IP no pública detectada: {$ip})");
            }
        }
    }

    private function resolveHostToIps(string $host): array
    {
        $ips = [];

        // Si el host ya es IP literal:
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        // IPv4
        $ipv4 = gethostbyname($host);
        if ($ipv4 && $ipv4 !== $host && filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ips[] = $ipv4;
        }

        // IPv6 (si está disponible)
        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($host, DNS_AAAA);
            if (is_array($records)) {
                foreach ($records as $r) {
                    if (!empty($r['ipv6']) && filter_var($r['ipv6'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        $ips[] = $r['ipv6'];
                    }
                }
            }
        }

        return array_values(array_unique($ips));
    }

    private function isPublicIp(string $ip): bool
    {
        // Retorna true solo si es IP pública (no privada, no loopback, no reservada)
        // FILTER_FLAG_NO_PRIV_RANGE y NO_RES_RANGE ayudan a filtrar privadas/reservadas en IPv4.
        // Para IPv6, también filtra varios rangos no globales.
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}