---
name: EduLink
colors:
  surface: '#f7f9ff'
  surface-dim: '#d7dae0'
  surface-bright: '#f7f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f1f4fa'
  surface-container: '#ebeef4'
  surface-container-high: '#e5e8ee'
  surface-container-highest: '#dfe3e8'
  on-surface: '#181c20'
  on-surface-variant: '#414754'
  inverse-surface: '#2d3135'
  inverse-on-surface: '#eef1f7'
  outline: '#727785'
  outline-variant: '#c1c6d6'
  surface-tint: '#005bc0'
  primary: '#005bbf'
  on-primary: '#ffffff'
  primary-container: '#1a73e8'
  on-primary-container: '#ffffff'
  inverse-primary: '#adc7ff'
  secondary: '#006e2c'
  on-secondary: '#ffffff'
  secondary-container: '#86f898'
  on-secondary-container: '#00722f'
  tertiary: '#9e4300'
  on-tertiary: '#ffffff'
  tertiary-container: '#c55500'
  on-tertiary-container: '#0e0200'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d8e2ff'
  primary-fixed-dim: '#adc7ff'
  on-primary-fixed: '#001a41'
  on-primary-fixed-variant: '#004493'
  secondary-fixed: '#89fa9b'
  secondary-fixed-dim: '#6ddd81'
  on-secondary-fixed: '#002108'
  on-secondary-fixed-variant: '#005320'
  tertiary-fixed: '#ffdbcb'
  tertiary-fixed-dim: '#ffb691'
  on-tertiary-fixed: '#341100'
  on-tertiary-fixed-variant: '#783100'
  background: '#f7f9ff'
  on-background: '#181c20'
  surface-variant: '#dfe3e8'
typography:
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
---

## Brand & Style
The design system is centered on clarity, accessibility, and focus, tailored for an educational environment where cognitive load must be minimized. The personality is professional and encouraging—acting as a reliable bridge between educators and students.

The design style is **Modern Minimalist** with **Tonal Layering**. It prioritizes heavy whitespace to separate distinct learning modules and uses a limited, purposeful color palette to guide the user's eye toward critical actions. Elements utilize soft rounding and subtle depth to feel approachable rather than institutional.

## Colors
This design system utilizes a structured color logic to communicate intent:
- **Primary (Trustworthy Blue):** Used for primary actions, active navigation states, and brand-critical identifiers.
- **Secondary (Sage Green):** Reserved for success states, completed tasks, and scheduling-related indicators.
- **Neutrals:** A range of soft grays are used for text hierarchies and background layering. 
- **Surfaces:** Pure white (#FFFFFF) is used for content cards to pop against the light gray background (#F8F9FA).

## Typography
Inter is the sole typeface for this design system to ensure maximum legibility across all digital interfaces. 
- **Headlines:** Use tighter letter-spacing and heavier weights to create a strong visual anchor.
- **Body:** Standardized at 16px for optimal reading comfort. 
- **Labels:** Used for badges, tags, and small metadata. The `label-sm` variant uses uppercase with slight tracking to differentiate it from standard body text.

## Layout & Spacing
The layout follows a **Fluid Grid** model with a max-width container for desktop viewing to prevent line lengths from becoming unreadable.

- **Grid:** A 12-column system is used for desktop, 8-column for tablet, and 4-column for mobile.
- **Rhythm:** An 8px linear scale governs all padding and margins. 
- **Padding:** Vertical spacing between major sections should be generous (typically 64px or 80px) to maintain the minimalist aesthetic and provide "breathing room" for educational content.

## Elevation & Depth
Depth is expressed through **Ambient Shadows** and **Tonal Layering** rather than heavy borders.

- **Low Elevation:** Used for static cards. A soft, blurred shadow with a 4% opacity (Neutral-900 tint) creates a subtle lift.
- **High Elevation:** Used for modals and dropdowns. A more pronounced shadow with a 12% opacity indicates interactivity and temporary overlay.
- **Flat:** Used for input fields and nested containers to maintain a clean, structured look.

## Shapes
The shape language is consistently **Rounded**, reinforcing the friendly and modern persona of the platform.
- **Standard (8px):** Buttons, Input fields, and small UI components.
- **Large (16px):** Main content cards and module containers.
- **Full (Pill):** Status badges (e.g., "1:1 Class") and social login buttons to distinguish them from primary platform actions.

## Components
- **Buttons:** Primary CTAs use the Primary Blue with white text. Secondary buttons use a subtle gray ghost style with a 1px border. Social login buttons (Google/Facebook) follow brand guidelines but maintain the platform's pill-shaped corner radius.
- **Status Badges:** Compact, pill-shaped tags. "1:1" classes use a light blue tint; "Group" classes use a light purple tint. These should have a low-saturation background with high-saturation text.
- **Input Fields:** Large 48px height for accessibility. They feature a 1px light gray border that transitions to Primary Blue on focus. Labels sit clearly above the field.
- **Cards:** Used to house course information. They include a 16px corner radius, a subtle ambient shadow, and 24px of internal padding.
- **Progress Bars:** Use Sage Green to indicate completion or positive momentum.
- **Lists:** Clean rows separated by subtle 1px dividers (#E8EAED) with ample vertical padding (16px) per item.