/** @type {import('next').NextConfig} */
const nextConfig = {
  // Optimize images
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: '**',
      },
    ],
  },

  // Enable experimental features if needed
  experimental: {
    // Add experimental features here
  },

  // Optimize production builds
  poweredByHeader: false,
  compress: true,
};

export default nextConfig;
