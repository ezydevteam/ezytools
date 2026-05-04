# Bangla Font Files

This directory holds Bangla font files used by both the browser (FontFace API) and
server-side PDF generation (DomPDF / FPDI).

## Required Font Files

| File                | Font Family      | Type      |
|---------------------|------------------|-----------|
| SutonnyMJ.ttf       | SutonnyMJ        | Bijoy     |
| SutonnyMJBold.ttf   | SutonnyMJ Bold   | Bijoy     |
| NikoshBAN.ttf       | NikoshBAN        | Bijoy     |
| NikoshBANBold.ttf   | NikoshBAN Bold   | Bijoy     |
| HindSiliguri.ttf    | Hind Siliguri    | Unicode   |
| Kalpurush.ttf       | Kalpurush        | Unicode   |

## Installation

1. Download the font files from trusted sources
2. Place all `.ttf` files in this directory (`/public/fonts/bangla/`)
3. The fonts will be automatically:
   - Loaded by browsers via CSS `@font-face` or JavaScript `FontFace` API
   - Used by DomPDF for server-side PDF generation (configured in `config/dompdf.php`)

## Notes

- `HindSiliguri.ttf` is already loaded from Google Fonts via `<link>` in `app.blade.php`
- For offline/PDF use, the local TTF copy is required
- SutonnyMJ and NikoshBAN use Bijoy encoding (not Unicode)
