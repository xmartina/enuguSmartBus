/**
 * API Configuration and Helper Functions
 * for connecting to CodeIgniter Backend
 */

const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || '';

/**
 * Generic API Response Interface
 */
export interface ApiResponse<T> {
  status: 'success' | 'failed';
  response: number;
  data?: T;
  message?: string;
}

/**
 * Generic fetch wrapper for API calls
 */
async function fetchAPI<T>(endpoint: string): Promise<T | null> {
  try {
    const url = `${API_BASE_URL}${endpoint}`;
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      cache: 'no-store', // Ensure fresh data on each request
    });

    if (!response.ok) {
      console.error(`API Error: ${response.status} ${response.statusText}`);
      return null;
    }

    const result: ApiResponse<T> = await response.json();

    if (result.status === 'success' && result.data) {
      return result.data;
    } else {
      console.error(`API returned failed status: ${result.message}`);
      return null;
    }
  } catch (error) {
    console.error('API Fetch Error:', error);
    return null;
  }
}

/**
 * API Endpoint Functions
 */

// Hero Section (Section One)
export async function getHeroData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/hero');
}

// How It Works Section (Section Two)
export async function getHowItWorksData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/work');
}

// How It Works Articles
export async function getHowItWorksArticles() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/work/articles');
}

// Journey/About Section (Section Three)
export async function getJourneyData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/journey');
}

// Journey Trips
export async function getJourneyTrips() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/journey/trips');
}

// Testimonial Header (Section Four)
export async function getTestimonialHeader() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/testiimonial');
}

// Testimonials/Comments
export async function getTestimonials() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/comments');
}

// Mobile App Section (Section Five)
export async function getMobileAppData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/app');
}

// Blog/CMS Section (Section Six)
export async function getBlogData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/cms');
}

// Newsletter/Subscribe Section (Section Seven)
export async function getNewsletterData() {
  return fetchAPI<any[]>('/modules/api/v1/frontend/subscribe');
}

/**
 * POST request for newsletter subscription
 */
export async function subscribeNewsletter(email: string): Promise<boolean> {
  try {
    const url = `${API_BASE_URL}/modules/api/v1/subscribe`;
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email }),
    });

    if (!response.ok) {
      return false;
    }

    const result = await response.json();
    return result.status === 'success';
  } catch (error) {
    console.error('Newsletter subscription error:', error);
    return false;
  }
}
