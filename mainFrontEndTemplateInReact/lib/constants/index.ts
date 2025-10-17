export const SITE_NAME = 'Enugu Smart Bus Service';
export const SITE_DESCRIPTION =
  'ENUGU BUS SERVICE MANAGEMENT LTD - Smart transportation solutions';
export const SITE_URL =
  process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

export const ROUTES = {
  HOME: '/',
  ABOUT: '/about',
  HOW_IT_WORKS: '/how-it-works',
  SERVICES: '/services',
  BLOG: '/blog',
  CONTACT: '/contact',
} as const;

export const API_ENDPOINTS = {
  BASE_URL: process.env.NEXT_PUBLIC_API_URL || '/api',
} as const;

export const COLORS = {
  PRIMARY: '#1F2B6C',
  PRIMARY_HOVER: '#1F2B6C90',
  PRIMARY_LIGHT: '#1F2B6C80',
} as const;
