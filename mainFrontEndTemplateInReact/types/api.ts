/**
 * TypeScript Types for API Responses
 * Based on CodeIgniter Backend Data Structure
 */

// Hero Section (Section One)
export interface HeroData {
  id: number;
  title: string;
  sub_title: string;
  image: string;
  button_text: string;
}

// How It Works Section (Section Two)
export interface HowItWorksData {
  id: number;
  title: string;
  subtitle: string;
}

// How It Works Article
export interface HowItWorksArticle {
  id: number;
  title: string;
  details: string;
  image: string;
  serial: number;
}

// Journey/About Section (Section Three)
export interface JourneyData {
  id: number;
  title: string;
  description: string;
}

// Trip Data
export interface TripData {
  id: number;
  pick_location_id: number;
  drop_location_id: number;
  imglocation: string;
  status: number;
  show: number;
  [key: string]: any; // For additional joined fields
}

// Testimonial Header
export interface TestimonialHeader {
  id: number;
  title: string;
  description: string;
}

// Testimonial/Comment
export interface Testimonial {
  id: number;
  name: string;
  designation: string;
  comment: string;
  image: string;
  serial: number;
}

// Mobile App Section (Section Five)
export interface MobileAppData {
  id: number;
  title: string;
  sub_title: string;
  image: string;
  android_link: string;
  ios_link: string;
}

// Blog/CMS Section (Section Six)
export interface BlogData {
  id: number;
  title: string;
  description: string;
}

// Newsletter/Subscribe Section (Section Seven)
export interface NewsletterData {
  id: number;
  title: string;
  sub_title: string;
  image: string;
}
