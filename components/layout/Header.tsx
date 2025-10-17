'use client';

import Link from 'next/link';
import Image from 'next/image';
import { useState } from 'react';
import { Menu, X } from 'lucide-react';
import Button from '@/components/ui/Button';
import Container from '@/components/ui/Container';
import { ROUTES } from '@/lib/constants';

const navigationItems = [
  { name: 'Home', href: ROUTES.HOME },
  { name: 'About', href: ROUTES.ABOUT },
  { name: 'How it works', href: ROUTES.HOW_IT_WORKS },
  { name: 'Services', href: ROUTES.SERVICES },
  { name: 'Blog', href: ROUTES.BLOG },
  { name: 'Contact Us', href: ROUTES.CONTACT },
];

export default function Header() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60">
      <Container>
        <div className="flex h-20 items-center justify-between">
          {/* Logo */}
          <Link href="/" className="flex items-center space-x-3">
            <div className="relative h-16 w-16">
              <Image
                src="/images/logo.png"
                alt="Enugu Smart Bus Service"
                fill
                className="object-contain"
                priority
              />
            </div>
          </Link>

          {/* Desktop Navigation Links */}
          <nav className="hidden lg:flex items-center space-x-8">
            {navigationItems.map((item) => (
              <Link
                key={item.name}
                href={item.href}
                className="text-sm text-primary transition-colors hover:text-primary-80 font-bold"
              >
                {item.name}
              </Link>
            ))}
          </nav>

          {/* Desktop Action Buttons */}
          <div className="hidden md:flex items-center space-x-4">
            <Button
              variant="ghost"
              size="sm"
              className="text-primary hover:bg-gray-50 shadow-xs border-none"
            >
              Login
            </Button>
            <Button
              variant="primary"
              size="sm"
              className="bg-primary text-white hover:bg-primary-90"
            >
              Sign Up
            </Button>
          </div>

          {/* Mobile Menu Button */}
          <button
            className="lg:hidden p-2 text-primary"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            aria-label="Toggle mobile menu"
          >
            {isMobileMenuOpen ? (
              <X className="h-6 w-6" />
            ) : (
              <Menu className="h-6 w-6" />
            )}
          </button>
        </div>

        {/* Mobile Navigation Menu */}
        {isMobileMenuOpen && (
          <div className="lg:hidden border-t border-gray-200 bg-white">
            <nav className="py-4 space-y-2">
              {navigationItems.map((item) => (
                <Link
                  key={item.name}
                  href={item.href}
                  className="block px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-gray-50"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  {item.name}
                </Link>
              ))}
              <div className="px-4 pt-4 space-y-2 border-t border-gray-200">
                <Button
                  variant="outline"
                  size="sm"
                  className="w-full border-gray-300 text-primary hover:bg-gray-50"
                >
                  Login
                </Button>
                <Button
                  variant="primary"
                  size="sm"
                  className="w-full bg-primary text-white hover:bg-primary-90"
                >
                  Sign Up
                </Button>
              </div>
            </nav>
          </div>
        )}
      </Container>
    </header>
  );
}
