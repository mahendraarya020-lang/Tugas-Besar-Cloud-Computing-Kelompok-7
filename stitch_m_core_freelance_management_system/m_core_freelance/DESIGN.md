---
name: M-Core Freelance
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#454652'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#757684'
  outline-variant: '#c5c5d4'
  surface-tint: '#4355b9'
  primary: '#24389c'
  on-primary: '#ffffff'
  primary-container: '#3f51b5'
  on-primary-container: '#cacfff'
  inverse-primary: '#bac3ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#6c3400'
  on-tertiary: '#ffffff'
  tertiary-container: '#8f4700'
  on-tertiary-container: '#ffc7a2'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dee0ff'
  primary-fixed-dim: '#bac3ff'
  on-primary-fixed: '#00105c'
  on-primary-fixed-variant: '#293ca0'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#ffdcc6'
  tertiary-fixed-dim: '#ffb784'
  on-tertiary-fixed: '#301400'
  on-tertiary-fixed-variant: '#713700'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  title-lg:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  title-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
  gutter: 24px
  margin: 24px
---

## Brand & Style
The design system is engineered for a high-performance freelance management ecosystem. It balances corporate reliability with a modern, agile startup aesthetic. The target audience includes independent contractors, creative agencies, and project managers who require a focused, distraction-free environment to manage complex workflows.

The visual style is **Corporate Modern with a Card-Based Layout**. It emphasizes clarity through a flat design language, utilizing structured information architecture and subtle depth to define interactive zones. The interface should feel dependable, efficient, and sophisticated, evoking a sense of organized momentum.

## Colors
The palette is anchored by **Deep Indigo**, providing a professional and authoritative foundation. **Slate Blue** serves as the secondary color, used for utilitarian elements and non-primary actions to reduce cognitive load. 

A high-contrast status system is employed for immediate data recognition:
- **Success Green:** Project completions, payment received, active status.
- **Warning Amber:** Approaching deadlines, pending approvals.
- **Danger Red:** Overdue tasks, payment failures, high-priority alerts.

Backgrounds utilize a cool neutral scale to ensure the primary and status colors remain the focal points of the dashboard.

## Typography
**Inter** is the exclusive typeface for this design system, chosen for its exceptional legibility in data-heavy environments. The typographic hierarchy relies on weight contrast and subtle letter-spacing adjustments to differentiate between navigational elements and data points.

Headlines use semi-bold and bold weights with tight tracking to appear cohesive and modern. Body text is optimized for long-form reading in task descriptions, while labels use a medium weight to maintain visibility at smaller scales.

## Layout & Spacing
The design system utilizes a **12-column fluid grid** for desktop and a **4-column grid** for mobile. A strict 4px/8px baseline rhythm ensures vertical consistency across all components.

- **Desktop (1440px+):** 24px margins and gutters. 
- **Tablet (768px - 1439px):** 16px gutters, 24px margins.
- **Mobile (Under 768px):** 16px margins and gutters.

Layouts are primarily card-based, where each project or data module occupies a container that spans a specific number of grid columns. Sidebars for navigation should remain fixed at 280px on desktop to maximize the workspace for data visualization.

## Elevation & Depth
Depth is created through **Tonal Layers** combined with **Ambient Shadows**. 

The canvas uses a light gray background, while the primary "work" surfaces (cards, sidebars) are pure white. Shadows are kept soft and minimal to prevent the UI from feeling "heavy."
- **Level 1 (Default Cards):** 0px 1px 3px rgba(0,0,0,0.1).
- **Level 2 (Hover/Active):** 0px 10px 15px -3px rgba(0,0,0,0.08).
- **Level 3 (Modals/Popovers):** 0px 20px 25px -5px rgba(0,0,0,0.1).

Outlines are used sparingly, primarily as low-contrast dividers (#E2E8F0) to separate sections within a card without adding visual noise.

## Shapes
A consistent **Rounded** language is applied to humanize the professional environment.
- **Buttons & Inputs:** 8px (0.5rem) for a precise, modern feel.
- **Cards & Large Containers:** 12px - 16px to clearly define major content blocks.
- **Status Chips:** Full pill-shaped (999px) to distinguish them from interactive buttons.

## Components
- **Buttons:** Primary buttons use a solid Deep Indigo fill with white text. Secondary buttons use a Slate Blue ghost style (outline and text only).
- **Cards:** White backgrounds with a 12px corner radius and a Level 1 shadow. Include a 4px colored top-border for "Status" identification (e.g., a green top-border for a completed project card).
- **Input Fields:** 8px corner radius with a 1px Slate Blue border (#CBD5E1). On focus, the border transitions to Deep Indigo with a subtle 3px outer glow.
- **Chips:** Small, pill-shaped indicators for tags (e.g., "UI/UX", "Billing"). Use a light tint of the status colors for the background and a dark shade for the text.
- **Data Visualization:** Charts should utilize the primary Deep Indigo and Secondary Slate Blue, with Success/Warning/Danger colors used only to indicate performance metrics.
- **Task Lists:** Use high-contrast checkboxes. When a task is completed, the text transitions to a lighter Slate Blue with a strikethrough.