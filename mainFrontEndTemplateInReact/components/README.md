# Components Directory

Organized component structure for the EBS website.

## Structure

### `/ui`

Reusable UI components (buttons, cards, inputs, etc.)

- `Button.tsx` - Button component with variants
- `Card.tsx` - Card container component
- `Container.tsx` - Page container with responsive padding

### `/layout`

Layout components (header, footer, navigation)

- `Header.tsx` - Main navigation header
- `Footer.tsx` - Site footer

### `/features`

Feature-specific components that combine UI components

## Usage Example

```tsx
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';

export default function Example() {
  return (
    <Card>
      <h2>Hello World</h2>
      <Button variant="primary" size="md">
        Click me
      </Button>
    </Card>
  );
}
```

## Best Practices

1. Use TypeScript for all components
2. Export components as default exports
3. Use the `cn()` utility from `@/lib/utils` for className merging
4. Follow the 'use client' directive when needed for client-side interactivity
