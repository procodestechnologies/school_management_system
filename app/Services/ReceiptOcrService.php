<?php

namespace App\Services;

use Carbon\Carbon;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * Local, self-hosted alternative to a cloud AI read of a receipt: OCR via
 * Tesseract for images, embedded-text extraction via smalot/pdfparser for
 * PDFs, then regex/heuristic parsing of the raw text for the fields we
 * care about.
 *
 * This is meaningfully less robust than an AI-based read - it has no
 * understanding of the receipt, just pattern-matching over whatever text
 * came out. The patterns here are tuned for Safaricom M-Pesa confirmation
 * messages (the dominant payment method for Kenyan schools) with generic
 * fallbacks for other formats; expect to extend these patterns as new
 * receipt formats show up in practice.
 */
class ReceiptOcrService
{
    /**
     * @return array{amount: ?float, currency: ?string, admission_number: ?string, student_name: ?string, paid_at: ?string, reference: ?string, payment_method: ?string, raw: array}
     */
    public function extractReceiptDetails(string $absoluteFilePath, string $mimeType): array
    {
        $text = $mimeType === 'application/pdf'
            ? $this->extractPdfText($absoluteFilePath)
            : $this->extractImageText($absoluteFilePath);

        return $this->parse($text);
    }

    private function extractImageText(string $path): string
    {
        $ocr = new TesseractOCR($path);

        if ($executable = config('services.tesseract.executable')) {
            $ocr->executable($executable);
        }

        return $ocr->run();
    }

    /**
     * Only reads a PDF's embedded text layer (e.g. an emailed bank
     * statement or digitally generated receipt) - a scanned/photographed
     * PDF with no text layer will come back empty, since that would need
     * rasterizing pages to images first (not implemented here).
     */
    private function extractPdfText(string $path): string
    {
        return (new PdfParser)->parseFile($path)->getText();
    }

    /**
     * @return array{amount: ?float, currency: ?string, admission_number: ?string, student_name: ?string, paid_at: ?string, reference: ?string, payment_method: ?string, raw: array}
     */
    private function parse(string $text): array
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text));

        return [
            'amount' => $this->extractAmount($normalized),
            'currency' => $this->extractCurrency($normalized),
            'admission_number' => $this->extractAdmissionNumber($normalized),
            'student_name' => $this->extractPayerName($normalized),
            'paid_at' => $this->extractDate($normalized),
            'reference' => $this->extractReference($normalized),
            'payment_method' => $this->extractPaymentMethod($normalized),
            'raw' => ['text' => $text],
        ];
    }

    private function extractAmount(string $text): ?float
    {
        // "Ksh1,500.00" / "KES 1500" / "Kshs. 1,500"
        if (preg_match('/\b(?:Ksh|KES|Kshs)\.?\s?([\d,]+(?:\.\d{1,2})?)/i', $text, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }

        return null;
    }

    private function extractCurrency(string $text): ?string
    {
        return preg_match('/\b(Ksh|KES|Kshs)\b/i', $text) ? 'KES' : null;
    }

    /**
     * Looks for an explicit label first ("account", "acc no", "admission
     * no") since that's the most reliable signal; falls back to any
     * standalone alphanumeric token that looks admission-number-shaped
     * (mixes letters and digits, 5-15 chars) if no label is found.
     */
    private function extractAdmissionNumber(string $text): ?string
    {
        if (preg_match('/\b(?:for\s+)?(?:account|acc\.?\s*no\.?|admission\s*no\.?|adm\.?\s*no\.?)\s*[:\-]?\s*([A-Z0-9\-\/]{3,20})/i', $text, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/\b([A-Z]{2,5}[\-\/]?\d{3,8})\b/', $text, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Best-effort only - "sent to NAME" / "paid to NAME" is the M-Pesa
     * pattern; there's no reliable generic fallback for other formats.
     */
    private function extractPayerName(string $text): ?string
    {
        if (preg_match('/\b(?:sent to|paid to|received from|from)\s+([A-Z][A-Z\s]{2,40}?)(?=\s+\d|\s+for\b|\.|,)/', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function extractDate(string $text): ?string
    {
        // "5/8/26", "05/08/2026", "5-8-26" - Kenyan receipts are day/month/year.
        if (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})\b/', $text, $m)) {
            try {
                $year = strlen($m[3]) === 2 ? '20'.$m[3] : $m[3];

                return Carbon::create((int) $year, (int) $m[2], (int) $m[1])->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * M-Pesa transaction codes are a 10-character alphanumeric string,
     * conventionally the very first token in the confirmation message.
     */
    private function extractReference(string $text): ?string
    {
        if (preg_match('/^([A-Z0-9]{10})\b/', $text, $m)) {
            return $m[1];
        }

        if (preg_match('/\b(?:ref(?:erence)?|transaction\s*(?:code|id))\.?\s*[:\-]?\s*([A-Z0-9]{6,15})/i', $text, $m)) {
            return $m[1];
        }

        return null;
    }

    private function extractPaymentMethod(string $text): ?string
    {
        return match (true) {
            (bool) preg_match('/\b(m-?pesa|mpesa)\b/i', $text) => 'mobile_money',
            (bool) preg_match('/\b(bank|deposit|swift|rtgs)\b/i', $text) => 'bank',
            (bool) preg_match('/\bcheque\b/i', $text) => 'cheque',
            (bool) preg_match('/\bcash\b/i', $text) => 'cash',
            default => null,
        };
    }
}
