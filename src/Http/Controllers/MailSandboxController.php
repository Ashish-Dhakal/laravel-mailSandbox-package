<?php

namespace AshishDhakal\MailSandbox\Http\Controllers;

use AshishDhakal\MailSandbox\Models\MailSandboxMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MailSandboxController extends Controller
{
    /**
     * Display a listing of the captured emails.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = MailSandboxMessage::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('from', 'like', "%{$search}%")
                    ->orWhere('to', 'like', "%{$search}%")
                    ->orWhere('cc', 'like', "%{$search}%")
                    ->orWhere('attachments', 'like', "%{$search}%");
            });
        }

        $emails = $query->paginate(20);

        if ($request->ajax()) {
            return view('mail-sandbox::partials.email-list', compact('emails'))->render();
        }

        return view('mail-sandbox::index', compact('emails'));
    }

    /**
     * Serve the package logo directly.
     */
    public function logo()
    {
        $path = dirname(__DIR__, 3) . '/public/assets/logo.png';
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    }

    /**
     * Display the specified captured email.
     */
    public function show(MailSandboxMessage $email)
    {
        return view('mail-sandbox::show', compact('email'));
    }

    /**
     * Remove all captured emails.
     */
    public function clear()
    {
        MailSandboxMessage::truncate();

        return redirect()->route('mail-sandbox.index')->with('success', 'Inbox cleared!');
    }

    /**
     * Get the processed HTML content for the email.
     */
    public function content(MailSandboxMessage $email)
    {
        $html = $email->body_html ?: '<pre>' . e($email->body_text) . '</pre>';

        // 1. Replace CIDs with local download links using array index
        if (!empty($email->attachments)) {
            foreach ($email->attachments as $index => $attachment) {
                if (!empty($attachment['cid'])) {
                    $html = str_replace(
                        'cid:' . $attachment['cid'],
                        route('mail-sandbox.download', [$email->id, $index]),
                        $html
                    );
                }
            }
        }

        // 2. Fix APP_URL mismatch (remap absolute URLs to current host)
        $appUrl = rtrim(config('app.url'), '/');
        $currentUrl = rtrim(url('/'), '/');
        if ($appUrl && $appUrl !== $currentUrl) {
            $html = str_replace($appUrl, $currentUrl, $html);
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Download or preview an attachment.
     */
    public function download(MailSandboxMessage $email, int $index)
    {
        $attachments = $email->attachments ?? [];

        if (!isset($attachments[$index])) {
            abort(404);
        }

        $attachment = $attachments[$index];

        if (!isset($attachment['content'])) {
            abort(404);
        }

        $content = base64_decode($attachment['content']);
        $filename = $attachment['name'] ?? 'attachment';
        $contentType = $attachment['content_type'] ?? 'application/octet-stream';

        return response($content)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
