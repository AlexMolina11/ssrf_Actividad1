<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SSQLIController extends Controller
{
    public function show()
    {
        return view('sqli', [
            'vuln_result'   => session('vuln_result'),
            'secure_result' => session('secure_result'),
            'error'         => session('error'),
        ]);
    }

    // ✅ Vulnerable: concatenación directa -> SQL Injection
    public function loginVulnerable(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $query = "SELECT * FROM users_demo WHERE email = '$email' AND password = '$password' ";
            $user = DB::select($query);

            if (count($user) > 0) {
                return redirect('/sqli')->with('vuln_result', 'Login exitoso (VULNERABLE) ✅');
            }

            return redirect('/sqli')->with('vuln_result', 'Login fallido (VULNERABLE) ❌');
        } catch (\Exception $e) {
            return redirect('/sqli')->with('error', 'Error vulnerable: ' . $e->getMessage());
        }
    }

    // ✅ Mitigado: query parametrizada (Query Builder)
    public function loginSecure(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $user = DB::table('users_demo')
                ->where('email', $email)
                ->where('password', $password)
                ->first();

            if ($user) {
                return redirect('/sqli')->with('secure_result', 'Login exitoso (SEGURO) ✅');
            }

            return redirect('/sqli')->with('secure_result', 'Login fallido (SEGURO) ❌');
        } catch (\Exception $e) {
            return redirect('/sqli')->with('error', 'Error secure: ' . $e->getMessage());
        }
    }
}