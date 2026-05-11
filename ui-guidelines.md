# UI Theme Guidelines (ALCATT System)

This guide captures the system theme colors, usage rules, and reusable class recipes.

## Theme palette

Primary (royal blue)
- Primary: #2B2D7E
- Primary hover: #3540A3
- Primary active: #1F2161
- Primary soft: #EEF1FF

Accent (gold)
- Accent: #F4B400
- Accent hover: #D99A00
- Accent active: #B67C00
- Accent soft: #FFF7D6

Neutral grays
- Gray dark: #1F2937
- Gray medium: #6B7280
- Gray light: #F3F4F6
- Gray border: #D1D5DB
- Gray hover: #E5E7EB

Status colors
- Success: #22C55E
- Warning: #F59E0B
- Danger: #EF4444
- Info: #3B82F6

## File separation
- Tailwind tokens: tailwind.config.js
- Component utilities: resources/css/app.css
- Blade templates should prefer component classes (btn-primary, surface, form-input).

## Usage rules

Do:
- Use royal blue for primary actions and navigation.
- Use yellow only as an accent or highlight.
- Use light gray for app backgrounds and white for cards.
- Keep shadows subtle and consistent.

Do not:
- Overuse yellow.
- Use pure black text.
- Mix random colors outside the palette.
- Use heavy gradients everywhere.

## Layout recipes

Application background
- bg-grayTheme-light text-grayTheme-dark

Sidebar
- Container: bg-primary text-white shadow-sidebar
- Active item: bg-primary-hover border-l-4 border-accent text-white
- Inactive item: text-white/80 hover:bg-primary-hover hover:text-white
- Active icon: text-accent
- Inactive icon: text-white/70

Top navbar
- bg-white border-b border-grayTheme-border shadow-card

Cards
- bg-white rounded-card shadow-card border border-grayTheme-border

Buttons
- Primary: btn-primary
- Secondary: btn-secondary
- Danger: btn-danger
- Outline: border border-primary text-primary hover:bg-primary-soft rounded-button

Forms
- Label: form-label
- Input: form-input

Tables
- Header: bg-primary text-white
- Row hover: hover:bg-grayTheme-hover
- Border: border-grayTheme-border

Modals
- Container: bg-white rounded-card shadow-modal
- Header/footer: border-grayTheme-border

Alerts
- Success: bg-success-soft text-success border border-success
- Warning: bg-warning-soft text-warning border border-warning
- Danger: bg-danger-soft text-danger border border-danger
- Info: bg-info-soft text-info border border-info
