<?php

namespace App\Notifications\Admin;

use App\Models\Document;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentUploadedNotification extends Notification
{
    public function __construct(private readonly Document $document) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName  = $this->document->user?->name ?? 'Unknown User';
        $typeMap   = ['cv' => 'CV/Resume', 'training' => 'Training Certificate', 'other' => 'Document'];
        $typeLabel = $typeMap[$this->document->type] ?? ucfirst($this->document->type);
        $sizeLabel = $this->document->size ? number_format($this->document->size / 1024, 1) . ' KB' : 'unknown size';

        return (new MailMessage)
            ->subject('New Document Uploaded — Alcatt Portal')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line("{$userName} has uploaded a new document to their account.")
            ->line('**Document:** ' . $this->document->document_name)
            ->line('**Type:** ' . $typeLabel)
            ->line('**Size:** ' . $sizeLabel)
            ->action('View Documents', route('admin.documents.index'))
            ->line('You can review all uploaded documents from the administration panel.')
            ->salutation('Alcatt Portal Admin');
    }

    public function toArray(object $notifiable): array
    {
        $userName = $this->document->user?->name ?? 'Unknown User';
        $typeMap  = ['cv' => 'CV/Resume', 'training' => 'Training Certificate', 'other' => 'Document'];
        $typeLabel = $typeMap[$this->document->type] ?? ucfirst($this->document->type);

        return [
            'type'          => 'document_uploaded',
            'title'         => 'New Document Uploaded',
            'message'       => "{$userName} uploaded a new {$typeLabel}: {$this->document->document_name}.",
            'url'           => route('admin.documents.index'),
            'user_id'       => $this->document->user_id,
            'user_name'     => $userName,
            'document_id'   => $this->document->id,
            'document_name' => $this->document->document_name,
            'document_type' => $this->document->type,
        ];
    }
}
