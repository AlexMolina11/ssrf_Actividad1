<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SSRF Demo (Laravel)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .box { border: 1px solid #ddd; padding: 16px; border-radius: 8px; margin-bottom: 16px; }
        input { width: 100%; padding: 10px; margin-top: 8px; }
        button { padding: 10px 14px; margin-top: 10px; cursor: pointer; }
        pre { background: #f6f6f6; padding: 12px; border-radius: 8px; overflow-x: auto; }
        .error { color: #b00020; }
        .hint { font-size: 0.95em; color: #444; }
    </style>
</head>
<body>
<h1>SSRF Demo (Laravel 8)</h1>

<div class="box">
    <p class="hint">
        Prueba sugerida (vulnerable): <b>http://127.0.0.1:8000/internal/metadata</b><br>
        La idea: el endpoint <code>/internal/metadata</code> “solo” debería ser accesible internamente,
        pero el SSRF vulnerable lo trae por ti.
    </p>

    @if($error)
        <p class="error"><b>{{ $error }}</b></p>
    @endif
</div>

<div class="box">
    <h2>1) Endpoint Vulnerable</h2>
    <form method="POST" action="/ssrf/fetch">
        {{ csrf_field() }}
        <label>URL:</label>
        <input name="url" placeholder="http://example.com" required>
        <button type="submit">Fetch (Vulnerable)</button>
    </form>

    @if($vuln_result)
        <h3>Resultado (Vulnerable)</h3>
        <pre>{{ $vuln_result }}</pre>
    @endif
</div>

<div class="box">
    <h2>2) Endpoint Mitigado</h2>
    <form method="POST" action="/ssrf/fetch-secure">
        {{ csrf_field() }}
        <label>URL:</label>
        <input name="url" placeholder="http://example.com" required>
        <button type="submit">Fetch (Secure)</button>
    </form>

    @if($secure_result)
        <h3>Resultado (Secure)</h3>
        <pre>{{ $secure_result }}</pre>
    @endif
</div>

</body>
</html>