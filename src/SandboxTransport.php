<?php

namespace AshishDhakal\MailSandbox;

use AshishDhakal\MailSandbox\Models\MailSandboxMessage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class SandboxTransport extends AbstractTransport
{
    /**
     * @inheritdoc
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        MailSandboxMessage::create([
            'subject' => $email->getSubject(),
            'to' => $this->formatAddresses((array)$email->getTo()),
            'from' => $this->formatAddresses((array)$email->getFrom()),
            'cc' => $this->formatAddresses((array)$email->getCc()),
            'bcc' => $this->formatAddresses((array)$email->getBcc()),
            'body_html' => $email->getHtmlBody(),
            'body_text' => $email->getTextBody(),
            'attachments' => $this->getSerializedAttachments($email),
        ]);
    }

    /**
     * Format an array of Address objects into a comma-separated string.
     */
    protected function formatAddresses(array $addresses): string
    {
        return implode(', ', array_map(fn($addr) => $addr->getAddress(), $addresses));
    }

    protected function getSerializedAttachments($email): array
    {
        $attachments = [];
        $seenHashes = [];

        // 1. Collect all potential parts from the entire message structure
        // We use only the recursive scan as it covers everything in the final email
        $allParts = $this->collectDataParts($email->getBody());

        // 2. Also check explicit attachments just in case they are not in the body tree
        foreach ($email->getAttachments() as $part) {
            $allParts[] = $part;
        }

        foreach ($allParts as $part) {
            if (!$part instanceof \Symfony\Component\Mime\Part\DataPart) {
                continue;
            }

            // Deduplicate exact object instances (same file found in body and attachments list)
            $hash = spl_object_hash($part);
            if (isset($seenHashes[$hash])) {
                continue;
            }
            $seenHashes[$hash] = true;

            // We save it using the current count as index for fallback naming
            $serialized = $this->serializePart($part, count($attachments));
            if ($serialized) {
                $attachments[] = $serialized;
            }
        }

        return $attachments;
    }

    /**
     * Serialize a DataPart into our storage format.
     */
    protected function serializePart($part, $index): array
    {
        $body = $part->getBody();
        $content = '';

        if (is_resource($body)) {
            rewind($body);
            $content = stream_get_contents($body);
            rewind($body);
        } else {
            $content = (string) $body;
        }

        $headers = $part->getPreparedHeaders();
        $cid = null;
        if ($headers->has('Content-ID')) {
            $cid = trim($headers->get('Content-ID')->getBodyAsString(), '<>');
        }

        $name = $headers->getHeaderParameter('Content-Disposition', 'filename')
            ?? $headers->getHeaderParameter('Content-Type', 'name')
            ?? ($cid ? $cid . '.' . $part->getMediaSubtype() : 'attachment_' . $index . '.' . $part->getMediaSubtype());

        return [
            'name' => $name,
            'cid' => $cid,
            'content_type' => $part->getMediaType() . '/' . $part->getMediaSubtype(),
            'size' => strlen($content),
            'content' => base64_encode($content),
        ];
    }

    /**
     * Recursively collect all DataPart instances from a message part.
     */
    protected function collectDataParts($part): array
    {
        $dataParts = [];
        if ($part instanceof \Symfony\Component\Mime\Part\DataPart) {
            $dataParts[] = $part;
        } elseif ($part instanceof \Symfony\Component\Mime\Part\AbstractMultipartPart) {
            foreach ($part->getParts() as $childPart) {
                $dataParts = array_merge($dataParts, $this->collectDataParts($childPart));
            }
        }
        return $dataParts;
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'sandbox';
    }
}
