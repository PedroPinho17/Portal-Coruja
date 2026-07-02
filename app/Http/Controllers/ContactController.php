<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'contactMessage' => $request->message, // Renomeado para evitar conflito com $message do Laravel
            ];

            // Enviar email para o destinatário
            Mail::send('emails.contact', $data, function ($message) use ($data) {
                $message->to('pedro0409romariz@gmail.com')
                    ->subject('Nova Mensagem de Contacto - Corujinha')
                    ->replyTo($data['email'], $data['name']);
            });

            return redirect()->to(url()->previous() . '#contact')->with('success', 'Mensagem enviada com sucesso! Entraremos em contacto em breve.');
        } catch (\Exception $e) {
            // Log do erro para debug
            Log::error('Erro ao enviar email de contacto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Mensagem de erro mais amigável
            $errorMessage = 'Erro ao enviar mensagem. Por favor, tente novamente mais tarde.';
            
            // Se for erro de autenticação SMTP, dar dica
            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), '535')) {
                $errorMessage = 'Erro de autenticação. Verifique as credenciais SMTP no servidor.';
            }
            
            return redirect()->to(url()->previous() . '#contact')
                ->withErrors(['error' => $errorMessage])
                ->withInput();
        }
    }
}

