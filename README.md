# Form Builder

A drag-and-drop form builder built inside a Laravel project using Blade components, Vanilla JavaScript, and Tailwind CSS.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser. No database setup required.

## How It Works

Fields are dragged from the right panel onto the canvas. Each placed field can be edited, duplicated, reordered, or deleted. The form schema is saved in localStorage so it persists across page refreshes. Clicking "Next" generates the JSON output.

## Drag & Drop Approach

Used the native **HTML5 Drag and Drop API** — no third-party DnD library.

Reasons:
- No extra dependencies, no build step needed beyond `php artisan serve`
- Works well for both palette-to-canvas drops and within-canvas reordering
- Keeps the code straightforward and easy to follow

## Assumptions

- No backend needed for form submission — the assignment says "No API calls needed". State is stored in localStorage.
- The submission URL shown in the header is a display label, not a real endpoint.
- Tailwind is loaded via CDN to avoid requiring an npm build step. This keeps setup to just `php artisan serve`.

## Supported Fields

Text Input, Text Area, Number, Email, Phone, Dropdown, Radio Buttons, Checkboxes, Date Picker, File Upload, Hidden Field

## Bonus Features Included

- Undo / Redo (Ctrl+Z / Ctrl+Y)
- Form Preview Mode
- LocalStorage persistence
- Delete confirmation
- Drag-over canvas highlight

## Sample JSON Output

```json
{
  "title": "Contact Form",
  "fields": [
    {
      "id": 1718000000001,
      "type": "text",
      "label": "Full Name",
      "placeholder": "Enter your name",
      "required": true,
      "defaultValue": "",
      "cssClass": "",
      "min": "",
      "max": ""
    },
    {
      "id": 1718000000002,
      "type": "email",
      "label": "Email",
      "placeholder": "you@example.com",
      "required": true,
      "defaultValue": "",
      "cssClass": "",
      "min": "",
      "max": ""
    },
    {
      "id": 1718000000003,
      "type": "dropdown",
      "label": "Subject",
      "required": false,
      "options": ["General Inquiry", "Support", "Other"],
      "defaultValue": "",
      "cssClass": "",
      "min": "",
      "max": ""
    }
  ]
}
```
