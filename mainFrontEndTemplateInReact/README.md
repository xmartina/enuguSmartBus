# EBS Website

A modern, production-ready website built with Next.js 14, React 19, and Tailwind CSS v4.

## ✨ Features

- ✅ **Next.js 14** with App Router
- ✅ **React 19** - Latest React features
- ✅ **TypeScript** - Full type safety with strict mode
- ✅ **Tailwind CSS v4** - Latest version with new features
- ✅ **Framer Motion** - Smooth animations
- ✅ **Lucide React** - Beautiful icons
- ✅ **ESLint 9** - Modern flat config
- ✅ **Dark Mode** - Built-in support
- ✅ **Optimized Performance** - Production-ready configuration
- ✅ **Organized Structure** - Clean, scalable architecture

## 📦 Tech Stack

| Technology    | Version | Purpose         |
| ------------- | ------- | --------------- |
| Next.js       | 14.2.33 | React framework |
| React         | 19.1.0  | UI library      |
| TypeScript    | 5.x     | Type safety     |
| Tailwind CSS  | 4.x     | Styling         |
| Framer Motion | 12.x    | Animations      |
| Lucide React  | 0.545.x | Icons           |
| ESLint        | 9.x     | Code linting    |

## 🚀 Getting Started

### Prerequisites

- Node.js 20+ installed
- npm, yarn, or pnpm package manager

### Installation

1. Clone the repository:

```bash
cd /path/to/ebs-website
```

2. Install dependencies:

```bash
npm install
```

3. Copy environment variables:

```bash
cp .env.example .env.local
```

### Development

Run the development server:

```bash
npm run dev
```

Open [http://localhost:3000](http://localhost:3000) in your browser to see the result.

### Build

Build the production version:

```bash
npm run build
```

### Start Production Server

```bash
npm start
```

### Linting

Run ESLint:

```bash
npm run lint
```

## 📁 Project Structure

```
ebs-website/
├── app/                      # Next.js app directory
│   ├── favicon.ico          # Favicon
│   ├── globals.css          # Global styles
│   ├── layout.tsx           # Root layout with Header/Footer
│   └── page.tsx             # Homepage
├── components/              # React components
│   ├── ui/                  # Reusable UI components
│   │   ├── Button.tsx       # Button with variants
│   │   ├── Card.tsx         # Card container
│   │   └── Container.tsx    # Page container
│   ├── layout/              # Layout components
│   │   ├── Header.tsx       # Navigation header
│   │   └── Footer.tsx       # Site footer
│   ├── features/            # Feature-specific components
│   └── README.md            # Component documentation
├── lib/                     # Utility libraries
│   ├── utils/               # Utility functions
│   │   ├── cn.ts           # className merger
│   │   └── index.ts        # Helpers (formatDate, delay, etc.)
│   └── constants/           # App constants
│       └── index.ts        # Site config, routes, endpoints
├── hooks/                   # Custom React hooks
│   ├── useMediaQuery.ts    # Media query hook
│   ├── useMounted.ts       # Mounted state hook
│   └── index.ts            # Hook exports
├── types/                   # TypeScript type definitions
│   └── index.ts            # Shared types
├── public/                  # Static assets
│   ├── images/             # Image files
│   └── svg/                # SVG files
├── styles/                  # Additional styles
├── .env.example            # Environment variables template
├── .env.local              # Local environment variables (gitignored)
├── .gitignore              # Git ignore rules
├── eslint.config.mjs       # ESLint configuration
├── next.config.mjs         # Next.js configuration
├── package.json            # Dependencies
├── postcss.config.mjs      # PostCSS configuration
├── README.md               # This file
└── tsconfig.json           # TypeScript configuration
```

## 🎨 Component Structure

### UI Components (`/components/ui`)

Reusable, atomic UI components:

- `Button.tsx` - Flexible button with multiple variants (primary, secondary, outline, ghost)
- `Card.tsx` - Card container with consistent styling
- `Container.tsx` - Responsive page container with max-width

### Layout Components (`/components/layout`)

Page-level layout components:

- `Header.tsx` - Main navigation with site logo
- `Footer.tsx` - Site footer with links and copyright

### Usage Example

```tsx
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';
import Container from '@/components/ui/Container';

export default function Example() {
  return (
    <Container>
      <Card>
        <h2>Hello World</h2>
        <Button variant="primary" size="md">
          Click me
        </Button>
      </Card>
    </Container>
  );
}
```

## 🛠️ Utilities

### `cn()` - className Merger

Combines Tailwind classes with proper conflict resolution:

```tsx
import { cn } from '@/lib/utils';

<div className={cn('text-base', active && 'text-blue-600')} />;
```

### Helper Functions

- `formatDate(date)` - Format dates consistently
- `delay(ms)` - Async delay utility
- `capitalize(str)` - Capitalize first letter

## 🪝 Custom Hooks

- `useMediaQuery(query)` - Detect media query matches
- `useMounted()` - Check if component is mounted (prevents hydration issues)

## 🎯 Path Aliases

The project uses TypeScript path aliases for clean imports:

```tsx
import Button from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import { SITE_NAME } from '@/lib/constants';
```

## 🌐 Environment Variables

Create a `.env.local` file (see `.env.example`):

```env
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_SITE_NAME=EBS Website
NEXT_PUBLIC_API_URL=/api
```

## 📝 Best Practices

1. **Components**: Use TypeScript for all components
2. **Styling**: Use Tailwind CSS classes with the `cn()` utility
3. **Imports**: Use `@/` path alias for absolute imports
4. **Client Components**: Add `'use client'` directive when using hooks/events
5. **Type Safety**: Define proper TypeScript interfaces in `/types`

## 🔧 Configuration

### Next.js (`next.config.mjs`)

- Image optimization with remote patterns
- Disabled powered-by header for security
- Compression enabled

### TypeScript (`tsconfig.json`)

- Strict mode enabled
- Path aliases configured
- Latest ES features

### Tailwind CSS (`globals.css`)

- v4 import syntax
- CSS custom properties for theming
- Dark mode support

## 📚 Learn More

- [Next.js Documentation](https://nextjs.org/docs)
- [React Documentation](https://react.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [TypeScript Documentation](https://www.typescriptlang.org/docs)
- [Framer Motion Documentation](https://www.framer.com/motion/)
- [Lucide Icons](https://lucide.dev/)

## 📄 License

Private
