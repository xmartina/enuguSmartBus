/** @type {import('next').NextConfig} */
const nextConfig = {
  // Enable static export for cPanel deployment
  output: 'export',
  
  // Optimize images - disable optimization for static export
  images: {
    unoptimized: true,
  },

  // Add trailing slashes for better compatibility with static hosting
  trailingSlash: true,

  // Optimize production builds
  poweredByHeader: false,
  compress: true,
};

export default nextConfig;
