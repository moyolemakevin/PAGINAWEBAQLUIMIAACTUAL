<?php
/** Return plain text, shortened at a word boundary and escaped by the caller. */
function getProductSummary(array $product, int $limit = 160): string {
    $source = trim((string) ($product['description'] ?? ''));
    if ($source === '') $source = (string) ($product['short_description'] ?? '');
    $plain = html_entity_decode(preg_replace('/<[^>]*>/', ' ', $source), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plain = trim(preg_replace('/\s+/u', ' ', preg_replace('/[?？]{2,}/u', '', $plain)));
    if ($plain === '') return 'Consulta sus características y disponibilidad antes de realizar tu pedido.';
    $length = static fn ($value) => function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    if ($length($plain) <= $limit) return $plain;
    $summary = '';
    foreach (preg_split('/\s+/u', $plain) as $word) {
        $candidate = $summary === '' ? $word : $summary . ' ' . $word;
        if ($summary !== '' && $length($candidate) > $limit) break;
        $summary = $candidate;
    }
    return rtrim($summary, " ,;:") . '…';
}
