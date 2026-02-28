<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SQL Injection Demo</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; }
    .box { border: 1px solid #ddd; padding: 16px; border-radius: 8px; margin-bottom: 16px; }
    input { width: 100%; padding: 10px; margin: 8px 0; }
    button { padding: 10px 14px; cursor: pointer; }
    .msg { margin-top: 10px; padding: 10px; background: #f6f6f6; border-radius: 8px; }
    .error { color: #b00020; }
  </style>
</head>
<body>

<h1>SQL Injection Demo (Laravel + MySQL)</h1>

@if(session('error'))
  <p class="error"><b>{{ session('error') }}</b></p>
@endif

<div class="box">
  <h2>1) Login Vulnerable</h2>
  <form method="POST" action="/sqli/vulnerable">
    @csrf
    <label>Email</label>
    <input name="email" placeholder="admin@demo.com">
    <label>Password</label>
    <input name="password" placeholder="123456">
    <button type="submit">Login Vulnerable</button>
  </form>

  @if(session('vuln_result'))
    <div class="msg">{{ session('vuln_result') }}</div>
  @endif
</div>

<div class="box">
  <h2>2) Login Seguro</h2>
  <form method="POST" action="/sqli/secure">
    @csrf
    <label>Email</label>
    <input name="email" placeholder="admin@demo.com">
    <label>Password</label>
    <input name="password" placeholder="123456">
    <button type="submit">Login Seguro</button>
  </form>

  @if(session('secure_result'))
    <div class="msg">{{ session('secure_result') }}</div>
  @endif
</div>

<div class="box">
  <h3>Payload sugerido (solo para demo)</h3>
  <div class="msg">
    Email: <code>' OR 1=1 #</code><br>
    Password: <code>cualquiercosa</code>
  </div>
</div>

</body>
</html>