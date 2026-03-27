# e.PLAN Frontend Documentation: Register Page

This document provides details on the frontend implementation for the Registration page of the Event Management System, built directly from the provided design.

## File Structure

- **View HTML**: `app/views/auth/register.php`
- **Stylesheet**: `public/assets/css/register.css`

## Design System Elements

### Color Palette
- **Deep Architectural Green (`#2B6E52`)**: Used for the primary left-panel branding background.
- **Golden Yellow (`#FCBC42`)**: Used for the high-impact "Register Account" call-to-action button.
- **Form Input Gray (`#EAEAEA`)**: A soft neutral gray for input fields that lack a harsh border to maintain a premium feel.
- **Body Background (`#F8F8F8`)**: Surrounds the main layout component.

### Typography
The UI leverages three fonts retrieved from Google Fonts:
1. **Inter**: Used for headings and main UI body copy. Gives a clean, modernist structure.
2. **Playfair Display**: A premium serif font used for form labels (e.g., `FIRST NAME`) and input placeholders to create contrast and evoke editorial/architectural vibes, precisely matching the mockup style.
3. **Permanent Marker**: Applied specifically to the `e.PLAN` text to mimic the custom hand-drawn logo style shown in the design.

## Responsive Layout
The page utilizes a CSS Flexbox `split-layout` component.
- **Desktop (>=900px)**: The left panel (branding) and right panel (form) sit side-by-side using Flex ratios.
- **Mobile (<900px)**: The container flips into `flex-direction: column` wrapping gracefully into a vertical scroll. Form fields like First/Last Name collapse from a two-column row into a single column at <500px widths.

## Integration Notes for Backend Collaborator
This view has been structured intentionally so you can easily wire up backend functionality:
- The form currently issues a `POST` request to `/auth/register`. Adjust the `action` attribute as necessary.
- Input elements all contain standard `name` attributes (`first_name`, `last_name`, `email`, `password`) and are ready for validation.
- OTP logic handling has been purposefully omitted from this frontend view. The collaborator handling OTP can mount their component securely within the form section before or after the submit button (e.g., using an AJAX fetch or appending fields dynamically).
