
import Newsletter from '@/components/features/Newsletter';
import Testimonials from '@/components/features/Testimonials';
import TravelBlog from '@/components/features/TravelBlog';
import MobileApp from '@/components/features/MobileApp';
import HowItWorks from '@/components/features/HowItWorks';
import About from '@/components/features/About';
import Hero from '@/components/features/Hero';

export default function Home() {
  return (
    <>
      {/* Hero Section */}
      <Hero />

      {/* About Section */}
      <About />

      {/* How It Works Section */}
      <HowItWorks />

      {/* Mobile App Section */}
      <MobileApp />

      {/* Travel Blog Section */}
      <TravelBlog />

      {/* Testimonials Section */}
      <Testimonials />

      {/* Newsletter Section */}
      <Newsletter />
    </>
  );
}
